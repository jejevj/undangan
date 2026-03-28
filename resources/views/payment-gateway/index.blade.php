@extends('layouts.app')

@section('title', 'Konfigurasi Payment Gateway')

@section('breadcrumb')
    <li class="breadcrumb-item active">Konfigurasi Payment Gateway</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Konfigurasi Payment Gateway</h4>
                @can('payment-gateway.create')
                <a href="{{ route('payment-gateway.create') }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus"></i> Tambah Konfigurasi
                </a>
                @endcan
            </div>
            <div class="card-body">
                @if($configs->isEmpty())
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> Belum ada konfigurasi payment gateway. 
                        <a href="{{ route('payment-gateway.create') }}">Tambah sekarang</a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Provider</th>
                                    <th>Environment</th>
                                    <th>Client ID</th>
                                    <th>SNAP API</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($configs as $config)
                                <tr>
                                    <td>
                                        <strong>{{ strtoupper($config->provider) }}</strong>
                                    </td>
                                    <td>
                                        @if($config->environment === 'sandbox')
                                            <span class="badge badge-warning">Sandbox</span>
                                        @else
                                            <span class="badge badge-success">Production</span>
                                        @endif
                                    </td>
                                    <td>
                                        <code class="small">{{ Str::limit($config->client_id, 20) }}</code>
                                    </td>
                                    <td>
                                        @if($config->private_key && $config->public_key && $config->doku_public_key)
                                            <span class="badge badge-success" title="SNAP API configured">
                                                <i class="fa fa-check"></i> Configured
                                            </span>
                                        @else
                                            <span class="badge badge-secondary" title="SNAP API not configured">
                                                <i class="fa fa-times"></i> Not Set
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($config->is_active)
                                            <span class="badge badge-success">
                                                <i class="fa fa-check"></i> Aktif
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" 
                                                    class="btn btn-info test-connection" 
                                                    data-id="{{ $config->id }}"
                                                    title="Test Koneksi">
                                                <i class="fa fa-plug"></i>
                                            </button>
                                            @can('payment-gateway.edit')
                                            <a href="{{ route('payment-gateway.edit', $config) }}" 
                                               class="btn btn-primary"
                                               title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('payment-gateway.delete')
                                            <form action="{{ route('payment-gateway.destroy', $config) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  data-confirm="Hapus konfigurasi ini?"
                                                  data-confirm-title="Konfirmasi Hapus"
                                                  data-confirm-ok="Hapus"
                                                  data-confirm-type="danger">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" title="Hapus">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.test-connection').click(function() {
        const btn = $(this);
        const configId = btn.data('id');
        const originalHtml = btn.html();
        
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: `/dash/payment-gateway/${configId}/test-connection`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                alert('✅ ' + response.message);
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Gagal test koneksi';
                alert('❌ ' + message);
            },
            complete: function() {
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    });
});
</script>
@endpush
