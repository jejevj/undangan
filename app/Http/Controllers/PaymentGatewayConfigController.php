<?php

namespace App\Http\Controllers;

use App\Models\PaymentGatewayConfig;
use App\Services\DokuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentGatewayConfigController extends Controller
{
    public function index()
    {
        $configs = PaymentGatewayConfig::latest()->get();
        return view('payment-gateway.index', compact('configs'));
    }

    public function create()
    {
        return view('payment-gateway.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'provider' => 'required|string|max:50',
            'environment' => 'required|in:sandbox,production',
            'client_id' => 'required|string|max:255',
            'secret_key' => 'required|string',
            'private_key' => 'nullable|string',
            'public_key' => 'nullable|string',
            'doku_public_key' => 'nullable|string',
            'issuer' => 'nullable|string|max:255',
            'auth_code' => 'nullable|string',
            'base_url' => 'required|url|max:255',
            'is_active' => 'boolean',
        ]);

        // If setting as active, deactivate others
        if ($request->is_active) {
            PaymentGatewayConfig::where('provider', $request->provider)
                ->update(['is_active' => false]);
        }

        $config = PaymentGatewayConfig::create($validated);

        return redirect()
            ->route('payment-gateway.index')
            ->with('success', 'Konfigurasi payment gateway berhasil ditambahkan.');
    }

    public function show(PaymentGatewayConfig $paymentGateway)
    {
        return view('payment-gateway.show', compact('paymentGateway'));
    }

    public function edit(PaymentGatewayConfig $paymentGateway)
    {
        return view('payment-gateway.edit', [
            'config' => $paymentGateway
        ]);
    }

    public function update(Request $request, PaymentGatewayConfig $paymentGateway)
    {
        $validated = $request->validate([
            'provider' => 'required|string|max:50',
            'environment' => 'required|in:sandbox,production',
            'client_id' => 'required|string|max:255',
            'secret_key' => 'nullable|string',
            'private_key' => 'nullable|string',
            'public_key' => 'nullable|string',
            'doku_public_key' => 'nullable|string',
            'issuer' => 'nullable|string|max:255',
            'auth_code' => 'nullable|string',
            'base_url' => 'required|url|max:255',
            'is_active' => 'boolean',
        ]);

        // If setting as active, deactivate others
        if ($request->is_active) {
            PaymentGatewayConfig::where('provider', $request->provider)
                ->where('id', '!=', $paymentGateway->id)
                ->update(['is_active' => false]);
        }

        // Only update fields if provided
        foreach (['secret_key', 'private_key', 'auth_code'] as $field) {
            if (empty($validated[$field])) {
                unset($validated[$field]);
            }
        }

        $paymentGateway->update($validated);

        return redirect()
            ->route('payment-gateway.index')
            ->with('success', 'Konfigurasi payment gateway berhasil diperbarui.');
    }

    public function destroy(PaymentGatewayConfig $paymentGateway)
    {
        $paymentGateway->delete();

        return redirect()
            ->route('payment-gateway.index')
            ->with('success', 'Konfigurasi payment gateway berhasil dihapus.');
    }

    /**
     * Test connection to DOKU API using official library
     */
    public function testConnection(PaymentGatewayConfig $paymentGateway)
    {
        try {
            // Validate required fields
            if (empty($paymentGateway->client_id) || empty($paymentGateway->decrypted_secret_key)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client ID dan Secret Key harus diisi.',
                ], 400);
            }

            // Initialize DOKU Service
            $dokuService = new DokuService($paymentGateway);
            
            // Test connection
            $result = $dokuService->testConnection();
            
            // Log result
            Log::info('DOKU Test Connection Result', $result);
            
            if ($result['success']) {
                return response()->json($result);
            }
            
            return response()->json($result, 400);

        } catch (\Exception $e) {
            Log::error('DOKU Test Connection Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error saat test koneksi: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Debug signature generation (only available in debug mode)
     */
    public function debugSignature(PaymentGatewayConfig $paymentGateway)
    {
        if (!config('app.debug')) {
            abort(404);
        }
        
        $endpoint = '/checkout/v1/payment';
        $testBody = json_encode([
            'order' => [
                'invoice_number' => 'TEST-' . time(),
                'amount' => 10000,
            ]
        ], JSON_UNESCAPED_SLASHES);
        
        $debug = DokuSignatureService::debug(
            $paymentGateway->client_id,
            $paymentGateway->secret_key,
            $endpoint,
            $testBody
        );
        
        return response()->json($debug, 200, [], JSON_PRETTY_PRINT);
    }
}
