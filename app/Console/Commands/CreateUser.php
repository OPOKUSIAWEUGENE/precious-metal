<?php

namespace App\Console\Commands;

use App\Models\IpWhitelist;
use App\Models\User;
use App\Modules\v0\Auth\Services\AuthService;
use Illuminate\Console\Command;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:create-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create New User';

    /**
     * Available abilities for token generation
     *
     * @var array
     */
    private array $abilities = [
        'disbursement.account-enquiry' => 'Disbursement Account Enquiry',
        'disbursement.balance' => 'Disbursement Balance',
        'disbursement.all' => 'Disbursement All',
        'collection.account-enquiry' => 'Collection Account Enquiry',
        'collection.balance' => 'Collection Balance',
        'collection.all' => 'Collection All',
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return $this->initUser();
    }

    private function initUser() : int 
    {
        $this->alert('Welcome to the user creation page');
        $system= $this->ask('Please enter the system\'s name ');
        $username = $this->ask('Please enter your username  (it should be in lowercase with no spacing e.g fp-mobile)');
        $password = $this->ask('Please enter your password');

        $this->info('Please wait while we create your account');

        $payload = [
            'name'=> $system,
            'username'=>$username,
            'password'=> $password,
        ];

        $auth_service = new AuthService();
        $response = $auth_service->createUser($payload);

        if ($response->getSuccess()) {
            $this->info('User created successfully');
            
            // Get the created user
            $user = User::where('username', $username)->first();
            
            if ($user) {
                // Prompt user to select abilities
                $selectedAbilities = $this->selectAbilities();
                
                // Create token with selected abilities
                $tokenName = 'auth_token_' . $user->id;
                $token = $user->createToken($tokenName, $selectedAbilities)->plainTextToken;
                
                $this->info("\nUser Details:");
                $this->info("  Name: {$user->name}");
                $this->info("  Username: {$user->username}");
                $this->info("  Token: {$token}");
                $this->info("  Abilities: " . implode(', ', $selectedAbilities));
                
                // Prompt for IP addresses
                $this->assignIpAddresses($user);
                
                return 0;
            } else {
                $this->error('User not found after creation');
                return 1;
            }
        }

        $error = json_encode($response->getErrors());
        $this->error('User creation failed');
        $this->error("Reason: {$error}");
        
        return 1;
    }

    /**
     * Prompt user to select abilities for the token
     *
     * @return array
     */
    private function selectAbilities(): array
    {
        $this->newLine();
        $this->info('Select abilities for the user\'s token:');
        $this->newLine();
        
        // Display available abilities
        $index = 1;
        foreach ($this->abilities as $ability => $description) {
            $this->info("  {$index}. {$description} ({$ability})");
            $index++;
        }
        
        $this->newLine();
        $choices = $this->choice(
            'Enter the numbers of abilities you want to assign (comma-separated for multiple)',
            array_keys($this->abilities)
        );
        
        // Handle multiple selections
        if (is_array($choices)) {
            return $choices;
        }
        
        return [$choices];
    }

    /**
     * Assign IP addresses to the user
     *
     * @param User $user
     * @return void
     */
    private function assignIpAddresses(User $user): void
    {
        $this->newLine();
        $assignIps = $this->confirm('Would you like to assign IP address(es) to this user?', false);
        
        if (!$assignIps) {
            return;
        }
        
        $this->info('Enter IP addresses (comma-separated for multiple):');
        $ipInput = $this->ask('IP Address(es)', '');
        
        if (empty($ipInput)) {
            $this->info('No IP addresses assigned.');
            return;
        }
        
        $ipAddresses = array_map('trim', explode(',', $ipInput));
        $label = $this->ask('Enter a label for these IP address(es) (optional)', 'Created during user registration');
        $activate = $this->confirm('Activate these IP addresses immediately?', true);
        
        $assignedCount = 0;
        foreach ($ipAddresses as $ip) {
            if ($this->isValidIp($ip)) {
                // Check if IP already exists for this user
                $existing = IpWhitelist::where('user_id', $user->id)
                    ->where('ip_address', $ip)
                    ->first();
                
                if (!$existing) {
                    IpWhitelist::create([
                        'user_id' => $user->id,
                        'ip_address' => $ip,
                        'label' => $label,
                        'active' => $activate,
                    ]);
                }
                $assignedCount++;
                $this->info("  Assigned IP: {$ip}");
            } else {
                $this->warn("  Invalid IP address: {$ip} - skipped");
            }
        }
        
        $this->info("Successfully assigned {$assignedCount} IP address(es) to user {$user->username}");
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
}
