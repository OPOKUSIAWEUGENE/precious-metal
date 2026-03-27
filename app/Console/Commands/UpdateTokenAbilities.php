<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Modules\Lib\Permissions;
use Illuminate\Console\Command;
use Laravel\Sanctum\PersonalAccessToken;

class UpdateTokenAbilities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'token:update-abilities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the abilities of a personal access token interactively';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Step 1: List all users
        $users = User::all(['id', 'name', 'username'])->toArray();

        if (empty($users)) {
            $this->error('No users found.');
            return Command::FAILURE;
        }

        $this->info('Select a user:');
        $this->table(
            ['ID', 'Name', 'Username'],
            array_map(fn($user) => [
                $user['id'],
                $user['name'],
                $user['username']
            ], $users)
        );

        $userId = $this->ask('Enter user ID');

        $user = User::find($userId);
        if (! $user) {
            $this->error("User with ID {$userId} not found.");
            return Command::FAILURE;
        }

        // Step 2: List tokens for the selected user
        $tokens = PersonalAccessToken::where('tokenable_id', $userId)
            ->where('tokenable_type', User::class)
            ->get(['id', 'name', 'abilities', 'created_at', 'last_used_at'])
            ->toArray();

        if (empty($tokens)) {
            $this->error("No tokens found for user {$user->name}.");
            return Command::FAILURE;
        }

        $this->info("\nTokens for user: {$user->name}");
        $this->table(
            ['ID', 'Name', 'Abilities', 'Created At', 'Last Used'],
            array_map(fn($token) => [
                $token['id'],
                $token['name'],
                is_array($token['abilities']) ? implode(', ', $token['abilities']) : '[]',
                $token['created_at'],
                $token['last_used_at'] ?? 'Never',
            ], $tokens)
        );

        $tokenId = $this->ask('Enter token ID');

        $token = PersonalAccessToken::where('id', $tokenId)
            ->where('tokenable_id', $userId)
            ->where('tokenable_type', User::class)
            ->first();

        if (! $token) {
            $this->error("Token with ID {$tokenId} not found for user {$user->name}.");
            return Command::FAILURE;
        }

        // Step 3: Show existing abilities and available abilities
        $currentAbilities = is_array($token->abilities) ? $token->abilities : [];
        
        $this->info("\nCurrent abilities for token '{$token->name}':");
        if (empty($currentAbilities)) {
            $this->line('  (none)');
        } else {
            foreach ($currentAbilities as $ability) {
                $this->line("  - {$ability}");
            }
        }

        // Available abilities to choose from
        $availableAbilities = [
            'Disbursement' => [
                'disbursement.all' => 'All disbursement operations (initiate, status-query, account-enquiry, balance)',
                'disbursement.account-enquiry' => 'Disbursement account enquiry',
                'disbursement.balance' => 'Disbursement balance',
            ],
            'Collection' => [
                'collection.all' => 'All collection operations (initiate, status-query, account-enquiry, balance)',
                'collection.account-enquiry' => 'Collection account enquiry',
                'collection.balance' => 'Collection balance',
            ],
        ];

        $this->info("\nAvailable abilities:");
        foreach ($availableAbilities as $category => $abilities) {
            $this->line("\n{$category}:");
            foreach ($abilities as $key => $description) {
                $status = in_array($key, $currentAbilities) ? '[x]' : '[ ]';
                $this->line("  {$status} {$key} - {$description}");
            }
        }

        // Step 4: Ask which abilities to add
        $abilitiesToAdd = $this->ask("\nEnter abilities to add (comma-separated, e.g., disbursement.all,collection.all)");

        if (empty($abilitiesToAdd)) {
            $this->info('No abilities added.');
            return Command::SUCCESS;
        }

        // Parse and merge abilities
        $newAbilities = array_map('trim', explode(',', $abilitiesToAdd));
        $updatedAbilities = array_unique(array_merge($currentAbilities, $newAbilities));

        // Update the token
        $token->abilities = $updatedAbilities;
        $token->save();

        $this->info("\nToken abilities updated successfully!");
        $this->line("Token: {$token->name}");
        $this->line("New abilities: " . implode(', ', $updatedAbilities));

        return Command::SUCCESS;
    }
}
