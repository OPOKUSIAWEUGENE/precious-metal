<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;

class ListTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:list-tokens 
                            {user? : User ID or username to list tokens for}
                            {--name= : Filter tokens by name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all personal access tokens (for all users or a specific user) in tabular format';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $userIdentifier = $this->argument('user');
        $tokenName = $this->option('name');

        // If user is specified, list tokens for that user only
        if ($userIdentifier) {
            return $this->listTokensForUser($userIdentifier, $tokenName);
        }

        // Otherwise, list tokens for all users
        return $this->listTokensForAllUsers($tokenName);
    }

    /**
     * List tokens for a specific user
     *
     * @param string $userIdentifier
     * @param string|null $tokenName
     * @return int
     */
    private function listTokensForUser(string $userIdentifier, ?string $tokenName): int
    {
        // Find user by ID or username
        $user = User::where('id', $userIdentifier)
            ->orWhere('username', $userIdentifier)
            ->first();

        if (!$user) {
            $this->error("User not found: {$userIdentifier}");
            return 1;
        }

        $this->info("Listing tokens for user: {$user->name} (ID: {$user->id}, Username: {$user->username})");
        $this->newLine();

        // Get tokens query
        $tokensQuery = $user->tokens();

        // Filter by token name if provided
        if ($tokenName) {
            $tokensQuery->where('name', $tokenName);
            $this->info("Filtering by token name: {$tokenName}");
            $this->newLine();
        }

        $tokens = $tokensQuery->get();

        if ($tokens->isEmpty()) {
            $message = $tokenName 
                ? "No tokens found with name '{$tokenName}' for this user."
                : 'No tokens found for this user.';
            $this->warn($message);
            return 0;
        }

        // Prepare table data
        $tableData = $tokens->map(function ($token) {
            return [
                'id' => $token->id,
                'name' => $token->name,
                'abilities' => is_array($token->abilities) 
                    ? implode(', ', $token->abilities) 
                    : (string) $token->abilities,
                'last_used_at' => $token->last_used_at 
                    ? $token->last_used_at->format('Y-m-d H:i:s') 
                    : 'Never',
                'created_at' => $token->created_at->format('Y-m-d H:i:s'),
                'expires_at' => $token->expires_at 
                    ? $token->expires_at->format('Y-m-d H:i:s') 
                    : 'Never',
            ];
        })->toArray();

        // Display table with proper formatting for wide content
        $this->displayTable(
            ['ID', 'Name', 'Abilities', 'Last Used At', 'Created At', 'Expires At'],
            $tableData
        );

        $this->newLine();
        $this->info("Total tokens: {$tokens->count()}");

        return 0;
    }

    /**
     * List tokens for all users
     *
     * @param string|null $tokenName
     * @return int
     */
    private function listTokensForAllUsers(?string $tokenName): int
    {
        $this->info('Listing tokens for all users');
        
        if ($tokenName) {
            $this->info("Filtering by token name: {$tokenName}");
        }
        
        $this->newLine();

        // Get all users with their tokens
        $users = User::with('tokens')->get();
        
        // Collect all tokens with user information
        $allTokens = collect();
        
        foreach ($users as $user) {
            $userTokens = $user->tokens();
            
            // Filter by token name if provided
            if ($tokenName) {
                $userTokens->where('name', $tokenName);
            }
            
            $tokens = $userTokens->get();
            
            foreach ($tokens as $token) {
                $allTokens->push([
                    'token' => $token,
                    'user' => $user,
                ]);
            }
        }

        if ($allTokens->isEmpty()) {
            $message = $tokenName 
                ? "No tokens found with name '{$tokenName}' across all users."
                : 'No tokens found across all users.';
            $this->warn($message);
            return 0;
        }

        // Prepare table data
        $tableData = $allTokens->map(function ($item) {
            $token = $item['token'];
            $user = $item['user'];
            
            return [
                'id' => $token->id,
                'user' => "{$user->name} (ID: {$user->id})",
                'username' => $user->username,
                'name' => $token->name,
                'abilities' => is_array($token->abilities) 
                    ? implode(', ', $token->abilities) 
                    : (string) $token->abilities,
                'last_used_at' => $token->last_used_at 
                    ? $token->last_used_at->format('Y-m-d H:i:s') 
                    : 'Never',
                'created_at' => $token->created_at->format('Y-m-d H:i:s'),
                'expires_at' => $token->expires_at 
                    ? $token->expires_at->format('Y-m-d H:i:s') 
                    : 'Never',
            ];
        })->toArray();

        // Display table with proper formatting for wide content
        $this->displayTable(
            ['ID', 'User', 'Username', 'Name', 'Abilities', 'Last Used At', 'Created At', 'Expires At'],
            $tableData
        );

        $this->newLine();
        $this->info("Total tokens: {$allTokens->count()}");

        return 0;
    }

    /**
     * Display table with proper formatting for wide content
     * This ensures the table is scrollable horizontally in the terminal
     *
     * @param array $headers
     * @param array $rows
     * @return void
     */
    private function displayTable(array $headers, array $rows): void
    {
        // Temporarily set a very large terminal width to prevent wrapping
        // This allows the table to render at full width, enabling horizontal scrolling
        $originalWidth = getenv('COLUMNS');
        putenv('COLUMNS=10000'); // Set very large width
        
        try {
            // Use Symfony Table component directly
            $output = new ConsoleOutput();
            $table = new Table($output);
            
            // Use default style
            $table->setStyle('default');
            
            // Set headers
            $table->setHeaders($headers);
            
            // Set rows
            $table->setRows($rows);
            
            // Render table at full width - enables horizontal scrolling
            $table->render();
        } finally {
            // Restore original terminal width
            if ($originalWidth !== false) {
                putenv('COLUMNS=' . $originalWidth);
            } else {
                putenv('COLUMNS');
            }
        }
        
        // Calculate approximate table width for tip display
        $estimatedWidth = 0;
        foreach ($headers as $header) {
            $estimatedWidth += max(strlen($header), 15);
        }
        
        // Add helpful note about horizontal scrolling for wide tables
        if (count($headers) > 5 || $estimatedWidth > 100) {
            $this->newLine();
            $this->comment('💡 Tip: Table is wider than terminal. Use horizontal scrolling:');
            $this->comment('   - macOS/Linux: Shift + Scroll or Cmd/Ctrl + Arrow keys');
            $this->comment('   - Windows: Shift + Scroll or Arrow keys');
        }
    }
}

