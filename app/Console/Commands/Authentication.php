<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Modules\v0\Auth\Services\AuthService;
use Illuminate\Console\Command;

class Authentication extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:login';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Login into service';

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
        return $this->initLogin();
    }

    /**
     * init login
     *
     * @return integer
     */
    public function initLogin() : int
    {
        $this->alert('Welcome to the login page');
        $username= $this->ask('Please enter your username');
        $password = $this->ask('Please enter your password');

        $this->info('Please wait while we log you in');

        $payload = [
            'username'=> $username,
            'password'=> $password
        ];

        $auth_service = new AuthService();
        $response = $auth_service->loginUser($payload, false);
        if($response->getSuccess()) {
            $this->info('Login successful');
            
            // Get the user from the response data
            $userData = $response->getData();
            $user = User::where('username', $username)->first();
            
            if ($user) {
                // Prompt user to select abilities
                $selectedAbilities = $this->selectAbilities();
                
                // Create token with selected abilities
                $tokenName = 'auth_token_' . $user->id;
                $token = $user->createToken($tokenName, $selectedAbilities)->plainTextToken;
                
                $this->info("\nLogin Details:");
                $this->info("Token: {$token}");
                $this->info("Abilities: " . implode(', ', $selectedAbilities));
                return 0;
            } else {
                $this->error('User not found after login');
                return 1;
            }
        } else {
            $this->error('Login failed');
            $this->error('Reason: '.$response->getErrors()[0]);
            return 1;
        }
    }

    /**
     * Prompt user to select abilities for the token
     *
     * @return array
     */
    private function selectAbilities(): array
    {
        $this->newLine();
        $this->info('Select abilities for the token:');
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
}
