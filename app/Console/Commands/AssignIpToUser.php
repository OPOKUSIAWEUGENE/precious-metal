<?php

namespace App\Console\Commands;

use App\Models\IpWhitelist;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class AssignIpToUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:assign-ip {user_id? : The ID or username of the user}
                            {ip_address? : The IP address to assign}
                            {--label= : Optional label for the IP address}
                            {--activate : Activate the IP address immediately}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign IP address(es) to a user';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $ipAddress = $this->argument('ip_address');

        // If no user_id provided, show list of users
        if (!$userId) {
            return $this->selectUserAndAssignIp();
        }

        // Find user by ID or username
        $user = $this->findUser($userId);
        
        if (!$user) {
            $this->error("User not found with ID/username: {$userId}");
            return 1;
        }

        // If no IP address provided, prompt for it
        if (!$ipAddress) {
            return $this->promptForIpAndAssign($user);
        }

        // Assign the single IP address
        return $this->assignIp($user, $ipAddress);
    }

    /**
     * Select user from list and assign IP
     *
     * @return int
     */
    private function selectUserAndAssignIp(): int
    {
        $users = User::all(['id', 'name', 'username']);

        if ($users->isEmpty()) {
            $this->error('No users found in the system.');
            return 1;
        }

        $this->info('Select a user to assign IP address:');
        $this->newLine();

        $choices = $users->map(function ($user) {
            return "{$user->id} - {$user->name} ({$user->username})";
        })->toArray();

        $selectedIndex = $this->choice(
            'Choose a user',
            $choices,
            null,
            1
        );

        $userId = explode(' - ', $selectedIndex)[0];
        $user = User::find($userId);

        return $this->promptForIpAndAssign($user);
    }

    /**
     * Prompt for IP address and assign
     *
     * @param User $user
     * @return int
     */
    private function promptForIpAndAssign(User $user): int
    {
        $this->newLine();
        $this->info("Assigning IP address(es) to user: {$user->name} ({$user->username})");
        
        // Show existing IPs
        $this->showExistingIps($user);

        $this->newLine();
        $ipInput = $this->ask('Enter IP address(es) (comma-separated for multiple, or "done" to finish)', 'done');

        if (strtolower($ipInput) === 'done') {
            $this->info('No IP addresses added.');
            return 0;
        }

        $ipAddresses = array_map('trim', explode(',', $ipInput));
        
        $label = $this->option('label') ?? $this->ask('Enter a label for these IP address(es) (optional)', '');
        $activate = $this->option('activate') ?: $this->confirm('Activate these IP addresses immediately?', true);

        $assignedCount = 0;
        
        foreach ($ipAddresses as $ip) {
            if ($this->isValidIp($ip)) {
                $result = $this->assignSingleIp($user, $ip, $label, $activate);
                if ($result) {
                    $assignedCount++;
                }
            } else {
                $this->warn("Invalid IP address: {$ip} - skipped");
            }
        }

        $this->newLine();
        $this->info("Successfully assigned {$assignedCount} IP address(es) to user {$user->username}");
        
        // Show updated list
        $this->showExistingIps($user);

        return 0;
    }

    /**
     * Assign a single IP address to user
     *
     * @param User $user
     * @param string $ipAddress
     * @param string $label
     * @param bool $activate
     * @return int
     */
    private function assignIp(User $user, string $ipAddress): int
    {
        if (!$this->isValidIp($ipAddress)) {
            $this->error("Invalid IP address: {$ipAddress}");
            return 1;
        }

        return $this->assignSingleIp($user, $ipAddress, $this->option('label'), $this->option('activate') ?? true);
    }

    /**
     * Assign a single IP address
     *
     * @param User $user
     * @param string $ipAddress
     * @param string|null $label
     * @param bool $activate
     * @return bool
     */
    private function assignSingleIp(User $user, string $ipAddress, ?string $label, bool $activate): bool
    {
        // Check if IP already exists for this user
        $existing = IpWhitelist::where('user_id', $user->id)
            ->where('ip_address', $ipAddress)
            ->first();

        if ($existing) {
            // Update existing record
            $existing->label = $label ?? $existing->label;
            $existing->active = $activate;
            $existing->save();
            
            $this->info("Updated existing IP record: {$ipAddress}");
            return true;
        }

        // Create new IP whitelist entry
        IpWhitelist::create([
            'user_id' => $user->id,
            'ip_address' => $ipAddress,
            'label' => $label ?? 'Terminal assigned',
            'active' => $activate,
        ]);

        $this->info("Assigned IP address: {$ipAddress}" . ($label ? " (Label: {$label})" : ""));
        return true;
    }

    /**
     * Find user by ID or username
     *
     * @param string $identifier
     * @return User|null
     */
    private function findUser(string $identifier): ?User
    {
        // Try to find by ID first
        $user = User::find($identifier);
        
        if (!$user) {
            // Try to find by username
            $user = User::where('username', $identifier)->first();
        }

        return $user;
    }

    /**
     * Validate IP address
     *
     * @param string $ip
     * @return bool
     */
    private function isValidIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Show existing IP addresses for a user
     *
     * @param User $user
     * @return void
     */
    private function showExistingIps(User $user): void
    {
        $ips = IpWhitelist::where('user_id', $user->id)->get();

        if ($ips->isEmpty()) {
            $this->info("User has no whitelisted IP addresses.");
            return;
        }

        $this->info("Existing IP addresses for {$user->username}:");
        
        $headers = ['ID', 'IP Address', 'Label', 'Active'];
        $rows = $ips->map(function ($ip) {
            return [
                $ip->id,
                $ip->ip_address,
                $ip->label ?? 'N/A',
                $ip->active ? 'Yes' : 'No',
            ];
        })->toArray();

        $this->table($headers, $rows);
    }
}
