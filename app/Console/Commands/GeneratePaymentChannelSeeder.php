<?php

namespace App\Console\Commands;

use App\Models\PaymentChannel;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GeneratePaymentChannelSeeder extends Command
{
    protected $signature = 'payment-channel:generate-seeder {--output=database/seeders/LatestPaymentChannelSeeder.php}';
    protected $description = 'Generate payment channel seeder from current database data';

    public function handle()
    {
        $channels = PaymentChannel::orderBy('sort_order')->get();

        if ($channels->isEmpty()) {
            $this->error('No payment channels found in database');
            return 1;
        }

        $outputFile = $this->option('output');
        $className = pathinfo($outputFile, PATHINFO_FILENAME);

        $this->info("Generating seeder from {$channels->count()} payment channels...");

        $channelsArray = $channels->map(function ($channel) {
            return [
                'type' => $channel->type,
                'code' => $channel->code,
                'name' => $channel->name,
                'description' => $channel->description,
                'bin_length' => $channel->bin_length,
                'bin_notes' => $channel->bin_notes,
                'billing_type' => $channel->billing_type,
                'feature' => $channel->feature,
                'bin_type' => $channel->bin_type,
                'partner_service_id' => $channel->partner_service_id,
                'prefix_customer_no' => $channel->prefix_customer_no,
                'va_trx_type' => $channel->va_trx_type,
                'is_active' => $channel->is_active,
                'is_available' => $channel->is_available,
                'sort_order' => $channel->sort_order,
            ];
        })->toArray();

        $seederContent = $this->generateSeederContent($className, $channelsArray);

        file_put_contents($outputFile, $seederContent);

        $this->info("✓ Seeder generated successfully: {$outputFile}");
        $this->line('');
        $this->line('To use this seeder:');
        $this->line("  php artisan db:seed --class={$className}");

        return 0;
    }

    protected function generateSeederContent(string $className, array $channels): string
    {
        $channelsCode = $this->arrayToPhpCode($channels, 2);
        $date = now()->format('Y-m-d');

        return <<<PHP
<?php

namespace Database\Seeders;

use App\Models\PaymentChannel;
use Illuminate\Database\Seeder;

class {$className} extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder contains the latest payment channel configuration
     * exported from database on {$date}
     */
    public function run(): void
    {
        \$channels = {$channelsCode};

        foreach (\$channels as \$channelData) {
            PaymentChannel::updateOrCreate(
                ['code' => \$channelData['code']],
                \$channelData
            );
        }

        \$this->command->info('✓ Payment channels seeded successfully');
        \$this->command->line('');
        \$this->command->table(
            ['Type', 'Code', 'Name', 'Available', 'Sort Order'],
            collect(\$channels)->map(fn(\$ch) => [
                \$ch['type'],
                \$ch['code'],
                \$ch['name'],
                \$ch['is_available'] ? 'Yes' : 'No',
                \$ch['sort_order'],
            ])->toArray()
        );
    }
}

PHP;
    }

    protected function arrayToPhpCode(array $array, int $indent = 0): string
    {
        $indentStr = str_repeat('    ', $indent);
        $lines = ["["];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $valueStr = $this->arrayToPhpCode($value, $indent + 1);
                if (is_string($key)) {
                    $lines[] = "{$indentStr}    '{$key}' => {$valueStr},";
                } else {
                    $lines[] = "{$indentStr}    {$valueStr},";
                }
            } else {
                $valueStr = $this->valueToPhpCode($value);
                if (is_string($key)) {
                    $lines[] = "{$indentStr}    '{$key}' => {$valueStr},";
                } else {
                    $lines[] = "{$indentStr}    {$valueStr},";
                }
            }
        }

        $lines[] = "{$indentStr}]";

        return implode("\n", $lines);
    }

    protected function valueToPhpCode($value): string
    {
        if (is_null($value)) {
            return 'null';
        } elseif (is_bool($value)) {
            return $value ? 'true' : 'false';
        } elseif (is_string($value)) {
            return "'" . addslashes($value) . "'";
        } elseif (is_numeric($value)) {
            return (string) $value;
        }

        return 'null';
    }
}
