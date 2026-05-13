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
     * @return void
     */
    public function sendPasswordResetMail(string $email, string $token, string $resetUrl): void
    {
        Mail::to($email)->send(new PasswordResetMail($email, $token, $resetUrl));
    }

    /**
     * Send welcome email to new users
     *
     * @param string $email
     * @param string $password
     * @param string|null $organizationName
     * @return void
     */
    public function sendWelcomeMail(string $email, string $password, ?string $organizationName = null): void
    {
        Mail::to($email)->send(new WelcomeMail($email, $password, $organizationName));
    }
}
