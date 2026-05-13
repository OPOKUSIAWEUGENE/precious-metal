<?php

namespace App\Http\Controllers\v0\Debug;

use App\Http\Controllers\Controller;
use App\Modules\v0\Emails\Mail\PasswordResetMail;
use App\Modules\v0\Emails\Mail\WelcomeMail;
use App\Modules\v0\Emails\Services\EmailService;
use Illuminate\Http\Request;

class MailPreviewController extends Controller
{
    public function __construct(protected EmailService $emailService)
    {
    }

    public function index()
    {
        $previews = [
            [
                'id' => 'welcome',
                'name' => 'Welcome Email',
                'description' => 'Sent to new users when their account is created.',
                'preview_url' => url('/mail-previews/welcome'),
            ],
            [
                'id' => 'password-reset',
                'name' => 'Password Reset Email',
                'description' => 'Sent when a user requests a password reset.',
                'preview_url' => url('/mail-previews/password-reset'),
            ],
        ];

        return view('mail-preview.index', compact('previews'));
    }

    public function previewWelcome(Request $request)
    {
        $email = $request->query('email', 'test@example.com');
        $password = $request->query('password', 'SamplePass123!');
        $org = $request->query('org', config('app.name'));

        return new WelcomeMail($email, $password, $org);
    }

    public function previewPasswordReset(Request $request)
    {
        $email = $request->query('email', 'test@example.com');
        $token = $request->query('token', '123456');
        $resetUrl = url("/auth/password/reset?token={$token}&email=" . urlencode($email));

        return new PasswordResetMail($email, $token, $resetUrl);
    }

    public function send(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'type' => 'required|in:welcome,password-reset',
        ]);

        $type = $request->input('type');
        $email = $request->input('email');

        if ($type === 'welcome') {
            $this->emailService->sendWelcomeMail($email, 'SamplePass123!', config('app.name'));
        } else {
            $token = '123456';
            $resetUrl = url("/auth/password/reset?token={$token}&email=" . urlencode($email));
            $this->emailService->sendPasswordResetMail($email, $token, $resetUrl);
        }

        return back()->with('success', "Sample {$type} email sent to {$email}!");
    }
}
