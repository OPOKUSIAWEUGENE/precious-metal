<?php

namespace App\Http\Controllers;

use App\Support\ApiDocs\ApiDocumentationRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ApiDocumentationController extends Controller
{
    public function __construct(private readonly ApiDocumentationRepository $documentations)
    {
    }

    public function index(): View
    {
        return view('docs.index', [
            'documentations' => $this->documentations->all(),
        ]);
    }

    public function show(string $documentation): View|RedirectResponse
    {
        $doc = $this->documentations->find($documentation);

        abort_if(! $doc, 404);

        return view('docs.show', [
            'documentations' => $this->documentations->all(),
            'documentation' => $doc,
        ]);
    }

    public function modules(): View
    {
        return view('docs.modules', [
            'modules' => $this->moduleDocumentation(),
        ]);
    }

    private function moduleDocumentation(): array
    {
        return [
            [
                'key' => 'platform-user',
                'title' => 'Platform User',
                'summary' => 'The platform user is the internal operator account used by admins and staff to manage the system.',
                'what_it_does' => [
                    'Logs in through the platform auth module and receives a Sanctum token.',
                    'Manages users, customers, rates, fees, and asset operations.',
                    'Approves withdrawals and monitors wallet activity.',
                    'Can request password resets and change their password after signing in.',
                ],
                'auth' => [
                    'description' => 'Platform users authenticate through the platform-manager auth flow. Login checks the `User` record, verifies the password, rejects disabled accounts, and returns a bearer token plus the user payload. Once signed in, the user can log out or change their password. Password reset uses a hashed token stored in the `platform_manager_password_reset_tokens` table.',
                    'references' => [
                        'Route file' => 'routes/api/platformManagers.php',
                        'Controller' => 'app/Http/Controllers/v0/Auth/PlatformManager/AuthController.php',
                        'Service' => 'app/Modules/v0/Auth/Services/PlatformManager/PlatformManagerAuthService.php',
                    ],
                    'endpoints' => [
                        $this->endpoint('POST', '/v0/auth/platform-managers/login', 'Platform manager login', 'app/Http/Controllers/v0/Auth/PlatformManager/AuthController.php@login', 'Validates `email` and `password`, then issues a Sanctum token.'),
                        $this->endpoint('POST', '/v0/auth/platform-managers/password/request', 'Request platform manager password reset', 'app/Http/Controllers/v0/Auth/PlatformManager/AuthController.php@requestPasswordReset', 'Generates a reset token and emails a reset link.'),
                        $this->endpoint('POST', '/v0/auth/platform-managers/password/reset', 'Reset platform manager password', 'app/Http/Controllers/v0/Auth/PlatformManager/AuthController.php@resetPassword', 'Checks the token and updates the password after validation.'),
                        $this->endpoint('POST', '/v0/auth/platform-managers/logout', 'Platform manager logout', 'app/Http/Controllers/v0/Auth/PlatformManager/AuthController.php@logout', 'Deletes the current Sanctum token.'),
                        $this->endpoint('POST', '/v0/auth/platform-managers/change-password', 'Change platform manager password', 'app/Http/Controllers/v0/Auth/PlatformManager/AuthController.php@changePassword', 'Requires the current password from an authenticated session.'),
                    ],
                ],
                'modules' => [
                    [
                        'name' => 'Users Module',
                        'purpose' => 'Used to create, update, inspect, activate, deactivate, and reset platform user accounts.',
                        'references' => [
                            'Controller' => 'app/Http/Controllers/v0/Users/UserController.php',
                            'Service' => 'app/Modules/v0/Users/Services/UserService.php',
                            'Model' => 'app/Modules/v0/Users/Models/User.php',
                        ],
                        'endpoints' => [
                            $this->endpoint('GET', '/v0/users', 'List platform users', 'app/Http/Controllers/v0/Users/UserController.php@index', 'Protected platform management endpoint.'),
                            $this->endpoint('POST', '/v0/users', 'Create a platform user', 'app/Http/Controllers/v0/Users/UserController.php@store', 'Creates a user, generates a password, and sends a welcome email.'),
                            $this->endpoint('GET', '/v0/users/{id}', 'Fetch a platform user', 'app/Http/Controllers/v0/Users/UserController.php@show'),
                            $this->endpoint('PUT', '/v0/users/{id}', 'Update a platform user', 'app/Http/Controllers/v0/Users/UserController.php@update'),
                            $this->endpoint('POST', '/v0/users/{id}/password/request', 'Request a platform user password reset', 'app/Http/Controllers/v0/Users/UserController.php@requestPasswordReset'),
                            $this->endpoint('DELETE', '/v0/users/{id}', 'Delete a platform user', 'app/Http/Controllers/v0/Users/UserController.php@destroy'),
                            $this->endpoint('GET', '/v0/users/email/{email}', 'Find a user by email', 'app/Http/Controllers/v0/Users/UserController.php@findByEmail'),
                            $this->endpoint('PUT', '/v0/users/{id}/deactivate', 'Deactivate a user', 'app/Http/Controllers/v0/Users/UserController.php@deactivate'),
                            $this->endpoint('PUT', '/v0/users/{id}/activate', 'Reactivate a user', 'app/Http/Controllers/v0/Users/UserController.php@activate'),
                        ],
                    ],
                    [
                        'name' => 'Customers Module',
                        'purpose' => 'Used by the platform user to create, inspect, update, activate, deactivate, and reset customer accounts.',
                        'references' => [
                            'Controller' => 'app/Http/Controllers/v0/Customers/CustomerController.php',
                            'Service' => 'app/Modules/v0/Customers/Services/CustomerService.php',
                            'Model' => 'app/Modules/v0/Customers/Models/Customer.php',
                        ],
                        'endpoints' => [
                            $this->endpoint('GET', '/v0/customers', 'List customers', 'app/Http/Controllers/v0/Customers/CustomerController.php@index'),
                            $this->endpoint('POST', '/v0/customers', 'Create a customer', 'app/Http/Controllers/v0/Customers/CustomerController.php@store'),
                            $this->endpoint('GET', '/v0/customers/{id}', 'Fetch a customer', 'app/Http/Controllers/v0/Customers/CustomerController.php@show'),
                            $this->endpoint('PUT', '/v0/customers/{id}', 'Update a customer', 'app/Http/Controllers/v0/Customers/CustomerController.php@update'),
                            $this->endpoint('POST', '/v0/customers/{id}/password/request', 'Request a customer password reset from the admin side', 'app/Http/Controllers/v0/Customers/CustomerController.php@requestPasswordReset'),
                            $this->endpoint('DELETE', '/v0/customers/{id}', 'Delete a customer', 'app/Http/Controllers/v0/Customers/CustomerController.php@destroy'),
                            $this->endpoint('GET', '/v0/customers/email/{email}', 'Find a customer by email', 'app/Http/Controllers/v0/Customers/CustomerController.php@findByEmail'),
                            $this->endpoint('PUT', '/v0/customers/{id}/deactivate', 'Deactivate a customer', 'app/Http/Controllers/v0/Customers/CustomerController.php@deactivate'),
                            $this->endpoint('PUT', '/v0/customers/{id}/activate', 'Reactivate a customer', 'app/Http/Controllers/v0/Customers/CustomerController.php@activate'),
                        ],
                    ],
                    [
                        'name' => 'Assets Module',
                        'purpose' => 'Lets the platform user inspect wallets, transactions, quotes, withdrawal requests, and current asset prices.',
                        'references' => [
                            'Controller' => 'app/Http/Controllers/v0/Users/AssetController.php',
                            'Service' => 'app/Modules/v0/Assets/Services/AssetService.php',
                            'Wallet model' => 'app/Modules/v0/Assets/Models/AssetWallet.php',
                            'Withdrawal model' => 'app/Modules/v0/Assets/Models/Withdrawal.php',
                        ],
                        'endpoints' => [
                            $this->endpoint('GET', '/v0/assets/wallets', 'List asset wallets', 'app/Http/Controllers/v0/Users/AssetController.php@wallets'),
                            $this->endpoint('GET', '/v0/assets/withdrawals', 'List withdrawals', 'app/Http/Controllers/v0/Users/AssetController.php@withdrawals'),
                            $this->endpoint('POST', '/v0/assets/withdrawals/{id}/approve', 'Approve a withdrawal', 'app/Http/Controllers/v0/Users/AssetController.php@approve'),
                            $this->endpoint('GET', '/v0/assets/transactions', 'List wallet transactions', 'app/Http/Controllers/v0/Users/AssetController.php@transactions'),
                            $this->endpoint('GET', '/v0/assets/quotes', 'List wallet quotes', 'app/Http/Controllers/v0/Users/AssetController.php@quotes'),
                            $this->endpoint('GET', '/v0/assets/prices/current', 'Fetch current asset price', 'app/Http/Controllers/v0/Users/AssetController.php@currentPrice'),
                        ],
                    ],
                    [
                        'name' => 'Rates and Fees Modules',
                        'purpose' => 'Lets the platform user maintain the pricing inputs used by asset workflows.',
                        'references' => [
                            'Rate model' => 'app/Modules/v0/Rates/Models/Rate.php',
                            'Rate controller' => 'app/Http/Controllers/v0/Rates/RateController.php',
                            'Fee model' => 'app/Modules/v0/Fees/Models/Fee.php',
                            'Fee controller' => 'app/Http/Controllers/v0/Fees/FeeController.php',
                        ],
                        'endpoints' => [
                            $this->endpoint('GET', '/v0/rates', 'List rates', 'app/Http/Controllers/v0/Rates/RateController.php@index'),
                            $this->endpoint('POST', '/v0/rates', 'Create a rate', 'app/Http/Controllers/v0/Rates/RateController.php@store'),
                            $this->endpoint('GET', '/v0/fees', 'List fees', 'app/Http/Controllers/v0/Fees/FeeController.php@index'),
                            $this->endpoint('POST', '/v0/fees', 'Create a fee', 'app/Http/Controllers/v0/Fees/FeeController.php@store'),
                        ],
                    ],
                    [
                        'name' => 'Email Module',
                        'purpose' => 'Sends welcome and reset emails from the internal management flows.',
                        'references' => [
                            'Service' => 'app/Modules/v0/Emails/Services/EmailService.php',
                            'Welcome mail' => 'app/Modules/v0/Emails/Mail/WelcomeMail.php',
                            'Password reset mail' => 'app/Modules/v0/Emails/Mail/PasswordResetMail.php',
                        ],
                        'endpoints' => [],
                    ],
                ],
                'notes' => [
                    'Platform users are the internal operators. They are the people who manage the system, not the customers who use it.',
                ],
            ],
            [
                'key' => 'customer',
                'title' => 'Customer',
                'summary' => 'Customers are the external users of the system who sign in to manage their own balances and wallet actions.',
                'what_it_does' => [
                    'Authenticates with email or phone number through the customer auth module.',
                    'Uses wallet endpoints to check balances, request quotes, transfer assets, sell, purchase, and withdraw.',
                    'Stores profile, KYC, and card data in the customer record.',
                    'Automatically gets a wallet when the account is created.',
                ],
                'auth' => [
                    'description' => 'Customer authentication lives in the customer auth module. A customer can register, log in, request a password reset, reset the password, and log out. Login accepts either email or phone number, then returns a Sanctum token plus the customer profile payload.',
                    'references' => [
                        'Route file' => 'routes/api/customers.php',
                        'Controller' => 'app/Http/Controllers/v0/Auth/Customer/CustomerAuthController.php',
                        'Service' => 'app/Modules/v0/Auth/Services/Customer/CustomerAuthService.php',
                    ],
                    'endpoints' => [
                        $this->endpoint('POST', '/v0/customers/auth/login', 'Customer login', 'app/Http/Controllers/v0/Auth/Customer/CustomerAuthController.php@login', 'Accepts email or phone number, then returns a token and customer profile.'),
                        $this->endpoint('POST', '/v0/customers/auth/register', 'Customer registration', 'app/Http/Controllers/v0/Auth/Customer/CustomerAuthController.php@register', 'Creates the customer record and logs the new account in immediately.'),
                        $this->endpoint('POST', '/v0/customers/auth/password/request', 'Request customer password reset', 'app/Http/Controllers/v0/Auth/Customer/CustomerAuthController.php@requestPasswordReset', 'Generates a hashed reset token and sends the reset email.'),
                        $this->endpoint('POST', '/v0/customers/auth/password/reset', 'Reset customer password', 'app/Http/Controllers/v0/Auth/Customer/CustomerAuthController.php@resetPassword', 'Validates token and password confirmation before updating the password.'),
                        $this->endpoint('POST', '/v0/customers/auth/logout', 'Customer logout', 'app/Http/Controllers/v0/Auth/Customer/CustomerAuthController.php@logout', 'Deletes the current customer access token.'),
                    ],
                ],
                'modules' => [
                    [
                        'name' => 'Wallets / Assets Module',
                        'purpose' => 'This is the module a customer needs to check balance, see wallet state, request quotes, move assets, buy or sell, and request withdrawals.',
                        'references' => [
                            'Customer asset controller' => 'app/Http/Controllers/v0/Customers/AssetController.php',
                            'Asset service' => 'app/Modules/v0/Assets/Services/AssetService.php',
                            'Asset wallet model' => 'app/Modules/v0/Assets/Models/AssetWallet.php',
                            'Withdrawal model' => 'app/Modules/v0/Assets/Models/Withdrawal.php',
                        ],
                        'endpoints' => [
                            $this->endpoint('GET', '/v0/customers/assets/wallets', 'Customer wallets', 'app/Http/Controllers/v0/Customers/AssetController.php@wallets', 'Shows the customer\'s asset wallets and balances.'),
                            $this->endpoint('GET', '/v0/customers/assets/rate', 'Customer rate', 'app/Http/Controllers/v0/Customers/AssetController.php@rate'),
                            $this->endpoint('GET', '/v0/customers/assets/balance', 'Customer balance', 'app/Http/Controllers/v0/Customers/AssetController.php@balance', 'Useful for checking how much the customer can move or withdraw.'),
                            $this->endpoint('GET', '/v0/customers/assets/transactions', 'Customer transactions', 'app/Http/Controllers/v0/Customers/AssetController.php@transactions'),
                            $this->endpoint('GET', '/v0/customers/assets/transactions/{reference}', 'Show a customer transaction', 'app/Http/Controllers/v0/Customers/AssetController.php@transaction'),
                            $this->endpoint('GET', '/v0/customers/assets/prices/current', 'Customer current price', 'app/Http/Controllers/v0/Customers/AssetController.php@currentPrice'),
                            $this->endpoint('POST', '/v0/customers/assets/quotes', 'Create a customer quote', 'app/Http/Controllers/v0/Customers/AssetController.php@quote'),
                            $this->endpoint('GET', '/v0/customers/assets/quotes/{reference}', 'Show a customer quote', 'app/Http/Controllers/v0/Customers/AssetController.php@quoteShow'),
                            $this->endpoint('POST', '/v0/customers/assets/transfers/recipient-enquiry', 'Recipient enquiry', 'app/Http/Controllers/v0/Customers/AssetController.php@recipientEnquiry'),
                            $this->endpoint('POST', '/v0/customers/assets/transfers/quote', 'Transfer quote', 'app/Http/Controllers/v0/Customers/AssetController.php@transferQuote'),
                            $this->endpoint('POST', '/v0/customers/assets/transfers', 'Create transfer', 'app/Http/Controllers/v0/Customers/AssetController.php@transfer'),
                            $this->endpoint('POST', '/v0/customers/assets/sales/quote', 'Sale quote', 'app/Http/Controllers/v0/Customers/AssetController.php@saleQuote'),
                            $this->endpoint('POST', '/v0/customers/assets/sales', 'Create sale', 'app/Http/Controllers/v0/Customers/AssetController.php@sale'),
                            $this->endpoint('POST', '/v0/customers/assets/purchases/quote', 'Purchase quote', 'app/Http/Controllers/v0/Customers/AssetController.php@purchaseQuote'),
                            $this->endpoint('POST', '/v0/customers/assets/purchases', 'Create purchase', 'app/Http/Controllers/v0/Customers/AssetController.php@purchase'),
                            $this->endpoint('POST', '/v0/customers/assets/withdrawals', 'Create withdrawal request', 'app/Http/Controllers/v0/Customers/AssetController.php@store', 'Customers use this when they want to convert or cash out from their wallet.'),
                        ],
                    ],
                ],
                'notes' => [
                    'The wallet module is the part of the app customers actually use after signing in. It gives them visibility into balances and the ability to move value around without touching the admin side.',
                ],
            ],
        ];
    }

    private function endpoint(string $method, string $path, string $summary, string $controller, string $notes = '', bool $secured = false): array
    {
        return [
            'method' => $method,
            'path' => $path,
            'summary' => $summary,
            'controller' => $controller,
            'notes' => $notes,
            'secured' => $secured,
        ];
    }
}
