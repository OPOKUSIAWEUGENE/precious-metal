<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class RevokeToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:revoke-token 
                            {token_id : The ID of the token to revoke}
                            {--user= : User ID or username (optional, will search all users if not provided)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revoke/delete a personal access token by its ID';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $tokenId = $this->argument('token_id');
        $userIdentifier = $this->option('user');

        // Validate UUID format (UUIDs are 36 characters with hyphens)
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $tokenId)) {
            $this->error('Token ID must be a valid UUID (e.g., 550e8400-e29b-41d4-a716-446655440000)');
            return 1;
        }

        // If user is specified, search only that user's tokens
        if ($userIdentifier) {
            $user = User::where('id', $userIdentifier)
                ->orWhere('username', $userIdentifier)
                ->first();

            if (!$user) {
                $this->error("User not found: {$userIdentifier}");
                return 1;
            }

            $token = $user->tokens()->where('id', $tokenId)->first();

            if (!$token) {
                $this->error("Token with ID {$tokenId} not found for user: {$user->name} (ID: {$user->id})");
                return 1;
            }

            $tokenName = $token->name;
            $token->delete();

            $this->info("Token revoked successfully!");
            $this->table(
                ['Field', 'Value'],
                [
                    ['Token ID', $tokenId],
                    ['Token Name', $tokenName],
                    ['User', "{$user->name} (ID: {$user->id})"],
                    ['Status', 'Revoked'],
                ]
            );

            return 0;
        }

        // Search across all users
        $token = null;
        $foundUser = null;

        foreach (User::all() as $user) {
            $token = $user->tokens()->where('id', $tokenId)->first();
            if ($token) {
                $foundUser = $user;
                break;
            }
        }

        if (!$token || !$foundUser) {
            $this->error("Token with ID {$tokenId} not found");
            return 1;
        }

        $tokenName = $token->name;
        $token->delete();

        $this->info("Token revoked successfully!");
        $this->table(
            ['Field', 'Value'],
            [
                ['Token ID', $tokenId],
                ['Token Name', $tokenName],
                ['User', "{$foundUser->name} (ID: {$foundUser->id})"],
                ['Status', 'Revoked'],
            ]
        );

        return 0;
    }
}

