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
            'audiences' => $this->audienceDocumentation(),
        ]);
    }

    private function audienceDocumentation(): array
    {
        $endpoints = $this->endpointCatalog();

        return [
            [
                'key' => 'platform-user',
                'title' => 'Platform User',
                'summary' => 'The platform user is the internal operator account used by admins and staff to manage the system.',
                'base_url' => url('/api'),
                'references' => [
                    'Route file' => 'routes/api/platformManagers.php',
                    'Platform auth controller' => 'app/Http/Controllers/v0/Auth/PlatformManager/AuthController.php',
                    'User model' => 'app/Modules/v0/Users/Models/User.php',
                ],
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
                        $this->resolvedEndpoint($endpoints, 'POST', '/v0/auth/platform-managers/login', 'Validates `email` and `password`, then issues a Sanctum token.'),
                        $this->resolvedEndpoint($endpoints, 'POST', '/v0/auth/platform-managers/password/request', 'Generates a reset token and emails a reset link.'),
                        $this->resolvedEndpoint($endpoints, 'POST', '/v0/auth/platform-managers/password/reset', 'Checks the token and updates the password after validation.'),
                        $this->resolvedEndpoint($endpoints, 'POST', '/v0/auth/platform-managers/logout', 'Deletes the current Sanctum token.'),
                        $this->resolvedEndpoint($endpoints, 'POST', '/v0/auth/platform-managers/change-password', 'Requires the current password from an authenticated session.'),
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
                            $this->resolvedEndpoint($endpoints, 'GET', '/v0/users', 'Protected platform management endpoint.'),
                            $this->resolvedEndpoint($endpoints, 'POST', '/v0/users', 'Creates a user, generates a password, and sends a welcome email.'),
                            $this->resolvedEndpoint($endpoints, 'GET', '/v0/users/{id}'),
                            $this->resolvedEndpoint($endpoints, 'PUT', '/v0/users/{id}'),
                            $this->resolvedEndpoint($endpoints, 'POST', '/v0/users/{id}/password/request'),
                            $this->resolvedEndpoint($endpoints, 'DELETE', '/v0/users/{id}'),
                            $this->resolvedEndpoint($endpoints, 'GET', '/v0/users/email/{email}'),
                            $this->resolvedEndpoint($endpoints, 'PUT', '/v0/users/{id}/deactivate'),
                            $this->resolvedEndpoint($endpoints, 'PUT', '/v0/users/{id}/activate'),
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
                            $this->resolvedEndpoint($endpoints, 'GET', '/v0/customers', 'Platform users use this to manage customer records.'),
                            $this->resolvedEndpoint($endpoints, 'POST', '/v0/customers', 'Platform users create customer records from here.'),
                            $this->resolvedEndpoint($endpoints, 'GET', '/v0/customers/{id}'),
                            $this->resolvedEndpoint($endpoints, 'PUT', '/v0/customers/{id}'),
                            $this->resolvedEndpoint($endpoints, 'POST', '/v0/customers/{id}/password/request'),
                            $this->resolvedEndpoint($endpoints, 'DELETE', '/v0/customers/{id}'),
                            $this->resolvedEndpoint($endpoints, 'GET', '/v0/customers/email/{email}'),
                            $this->resolvedEndpoint($endpoints, 'PUT', '/v0/customers/{id}/deactivate'),
                            $this->resolvedEndpoint($endpoints, 'PUT', '/v0/customers/{id}/activate'),
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
                            $this->resolvedEndpoint($endpoints, 'GET', '/v0/assets/wallets'),
                            $this->resolvedEndpoint($endpoints, 'GET', '/v0/assets/withdrawals'),
                            $this->resolvedEndpoint($endpoints, 'POST', '/v0/assets/withdrawals/{id}/approve'),
                            $this->resolvedEndpoint($endpoints, 'GET', '/v0/assets/transactions'),
                            $this->resolvedEndpoint($endpoints, 'GET', '/v0/assets/quotes'),
                            $this->resolvedEndpoint($endpoints, 'GET', '/v0/assets/prices/current'),
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
                            $this->resolvedEndpoint($endpoints, 'GET', '/v0/rates'),
                            $this->resolvedEndpoint($endpoints, 'POST', '/v0/rates'),
                            $this->resolvedEndpoint($endpoints, 'GET', '/v0/fees'),
                            $this->resolvedEndpoint($endpoints, 'POST', '/v0/fees'),
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
                'base_url' => url('/api'),
                'references' => [
                    'Route file' => 'routes/api/customers.php',
                    'Customer auth controller' => 'app/Http/Controllers/v0/Auth/Customer/CustomerAuthController.php',
                    'Customer model' => 'app/Modules/v0/Customers/Models/Customer.php',
                ],
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
                        $this->resolvedEndpoint($endpoints, 'POST', '/v0/customers/auth/login', 'Accepts email or phone number, then returns a token and customer profile.'),
                        $this->resolvedEndpoint($endpoints, 'POST', '/v0/customers/auth/register', 'Creates the customer record and logs the new account in immediately.'),
                        $this->resolvedEndpoint($endpoints, 'POST', '/v0/customers/auth/password/request', 'Generates a hashed reset token and sends the reset email.'),
                        $this->resolvedEndpoint($endpoints, 'POST', '/v0/customers/auth/password/reset', 'Validates token and password confirmation before updating the password.'),
                        $this->resolvedEndpoint($endpoints, 'POST', '/v0/customers/auth/logout', 'Deletes the current customer access token.'),
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
                            $this->resolvedEndpoint($endpoints, 'GET', '/v0/customers/assets/wallets', 'Shows the customer\'s asset wallets and balances.'),
                            $this->resolvedEndpoint($endpoints, 'GET', '/v0/customers/assets/rate'),
                            $this->resolvedEndpoint($endpoints, 'GET', '/v0/customers/assets/balance', 'Useful for checking how much the customer can move or withdraw.'),
                            $this->resolvedEndpoint($endpoints, 'GET', '/v0/customers/assets/transactions'),
                            $this->resolvedEndpoint($endpoints, 'GET', '/v0/customers/assets/transactions/{reference}'),
                            $this->resolvedEndpoint($endpoints, 'GET', '/v0/customers/assets/prices/current'),
                            $this->resolvedEndpoint($endpoints, 'POST', '/v0/customers/assets/quotes'),
                            $this->resolvedEndpoint($endpoints, 'GET', '/v0/customers/assets/quotes/{reference}'),
                            $this->resolvedEndpoint($endpoints, 'POST', '/v0/customers/assets/transfers/recipient-enquiry'),
                            $this->resolvedEndpoint($endpoints, 'POST', '/v0/customers/assets/transfers/quote'),
                            $this->resolvedEndpoint($endpoints, 'POST', '/v0/customers/assets/transfers'),
                            $this->resolvedEndpoint($endpoints, 'POST', '/v0/customers/assets/sales/quote'),
                            $this->resolvedEndpoint($endpoints, 'POST', '/v0/customers/assets/sales'),
                            $this->resolvedEndpoint($endpoints, 'POST', '/v0/customers/assets/purchases/quote'),
                            $this->resolvedEndpoint($endpoints, 'POST', '/v0/customers/assets/purchases'),
                            $this->resolvedEndpoint($endpoints, 'POST', '/v0/customers/assets/withdrawals', 'Customers use this when they want to convert or cash out from their wallet.'),
                        ],
                    ],
                ],
                'notes' => [
                    'The wallet module is the part of the app customers actually use after signing in. It gives them visibility into balances and the ability to move value around without touching the admin side.',
                ],
            ],
        ];
    }

    private function endpointCatalog(): array
    {
        $catalog = [];

        foreach ($this->documentations->all() as $documentation) {
            foreach ($documentation['endpoints'] as $endpoint) {
                $catalog[$this->endpointKey($endpoint['method'], $endpoint['path'])] = array_merge($endpoint, [
                    'docs_title' => $documentation['title'],
                    'docs_route' => $documentation['route'],
                ]);
            }
        }

        return $catalog;
    }

    private function resolvedEndpoint(array $catalog, string $method, string $path, string $notes = ''): array
    {
        $key = $this->endpointKey($method, $path);
        $endpoint = $catalog[$key] ?? [
            'id' => $this->endpointId($method, $path),
            'method' => strtoupper($method),
            'path' => $path,
            'summary' => $path,
            'description' => '',
            'operation_id' => null,
            'tags' => [],
            'primary_tag' => 'Untagged',
            'secured' => false,
            'parameters' => [],
            'request' => null,
            'responses' => [],
            'docs_title' => null,
            'docs_route' => null,
        ];

        return array_merge($endpoint, [
            'notes' => $notes,
        ]);
    }

    private function endpointKey(string $method, string $path): string
    {
        return strtolower($method) . '|' . $this->normalizePath($path);
    }

    private function normalizePath(string $path): string
    {
        return '/' . ltrim(trim(preg_replace('#/+#', '/', $path)), '/');
    }

    private function endpointId(string $method, string $path): string
    {
        return \Illuminate\Support\Str::slug(strtolower($method) . ' ' . $path);
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
