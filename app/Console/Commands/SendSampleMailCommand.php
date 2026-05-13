<?php

namespace App\Console\Commands;

use App\Modules\v0\Emails\Services\EmailService;
use Illuminate\Console\Command;

class SendSampleMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:send-sample';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a sample email for testing purposes';

    /**
     * Execute the console command.
     */
    public function handle(EmailService $emailService)
    {
        $types = [
            'Welcome' => 'sendWelcomeMail',
            'Password Reset' => 'sendPasswordResetMail',
        ];

        $choice = $this->choice(
            'Which sample email would you like to send?',
            array_keys($types),
            0
        );

        $email = $this->ask('Enter the recipient email address');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address provided.');
            return 1;
        }

        $this->info("Sending {$choice} email to {$email}...");

        try {
            if ($choice === 'Welcome') {
                $emailService->sendWelcomeMail($email, 'SamplePassword123!', 'Neutek Sample Org');
            } elseif ($choice === 'Password Reset') {
                $token = '123456';
                $resetUrl = config('app.url') . "/auth/password/reset?token={$token}&email=" . urlencode($email);
                $emailService->sendPasswordResetMail($email, $token, $resetUrl);
            }

            $this->info('Email sent successfully! (Check your logs if using log driver)');
        } catch (\Exception $e) {
            $this->error('Failed to send email: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
