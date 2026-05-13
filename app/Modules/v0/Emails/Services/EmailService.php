<?php

namespace App\Modules\v0\Emails\Services;

use App\Modules\BaseService;
use App\Modules\v0\Emails\Mail\PasswordResetMail;
use App\Modules\v0\Emails\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;

class EmailService extends BaseService
{
    /**
     * Send password reset email
     *
     * @param string $email
     * @param string $token
     * @param string $resetUrl
     * @param string $userType
     * @return void
     */
    public function sendPasswordResetMail(string $email, string $token, string $resetUrl, string $userType = ''): void
    {
        Mail::to($email)->send(new PasswordResetMail($email, $token, $resetUrl, $userType));
    }

    /**
     * Send welcome email to new users
     *
     * @param string $email
     * @param string $password
     * @param string|null $organizationName
     * @param string $userType
     * @return void
     */
    public function sendWelcomeMail(string $email, string $password, ?string $organizationName = null, string $userType = ''): void
    {
        Mail::to($email)->send(new WelcomeMail($email, $password, $organizationName, $userType));
    }
}
