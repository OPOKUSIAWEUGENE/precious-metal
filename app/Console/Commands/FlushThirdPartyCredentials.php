<?php

namespace App\Console\Commands;

use App\Modules\v0\SharedResourceModule\Models\ThirdPartyCredential;
use Illuminate\Console\Command;

class FlushThirdPartyCredentials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'third-party-credentials:flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flush third-party credentials from the database. Select a gateway or flush all.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Get available gateways
        $availableGateways = ThirdPartyCredential::distinct()
            ->pluck('third_party_name')
            ->filter()
            ->values()
            ->toArray();

        if (empty($availableGateways)) {
            $this->warn('No third-party credentials found in the database.');
            return Command::SUCCESS;
        }

        // Add "All" as the first option
        $choices = array_merge(['All'], $availableGateways);

        // Show interactive choice
        $choice = $this->choice(
            'Select a gateway to flush credentials from:',
            $choices,
            0
        );

        if ($choice === 'All') {
            // Flush all credentials
            $count = ThirdPartyCredential::count();
            
            if ($count === 0) {
                $this->warn('No credentials to flush.');
                return Command::SUCCESS;
            }

            if ($this->confirm("Are you sure you want to flush all {$count} credential(s)?")) {
                ThirdPartyCredential::truncate();
                $this->info("Successfully flushed all {$count} credential(s).");
                return Command::SUCCESS;
            }
            
            $this->info('Operation cancelled.');
            return Command::FAILURE;
        }

        // Flush specific gateway
        $gateway = strtolower($choice);
        $count = ThirdPartyCredential::where('third_party_name', $gateway)->count();
        
        if ($count === 0) {
            $this->warn("No credentials found for gateway '{$gateway}'.");
            return Command::SUCCESS;
        }

        if ($this->confirm("Are you sure you want to flush {$count} credential(s) for '{$gateway}'?")) {
            ThirdPartyCredential::where('third_party_name', $gateway)->delete();
            $this->info("Successfully flushed {$count} credential(s) for '{$gateway}'.");
            return Command::SUCCESS;
        }
        
        $this->info('Operation cancelled.');
        return Command::FAILURE;
    }
}
