<?php

namespace App\Modules\v0\Emails\Mail;

use App\Utils\Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $cta_link;
    public string $cta_content;

    /**
     * Create a new message instance.
     */
    public function __construct(public string $email, public string $verification_code, public string $resetUrl)
    {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $from = config('mail.from.address');
        $name = config('mail.from.name');

        return new Envelope(
            from: new Address($from ?: 'hello@neutek.com', $name ?: config('app.name')),
            subject: config('app.name') . ": Password Reset",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $subject = config('app.name') . ": Password Reset"; 
        $intro = "Password Reset Request"; 
        
        $content = "<p style='margin-bottom: 24px;'>Please use the following unique verification code to authorize this request:</p>";
        $content .= Helper::formatVerificationCodeBoxes($this->verification_code);
        $content .= "<div style='margin-top: 24px; padding: 16px; background-color: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 4px; color: #92400e; font-size: 14px;'>";
        $content .= "<strong>Note:</strong> This security code is valid for 60 minutes. If you did not make this request, please ignore this email.";
        $content .= "</div>";

        $outro = "Best regards,"; 
        
        $this->cta_link = $this->resetUrl;
        $this->cta_content = "Reset Password";

        return new Content(
            view: 'mails.PasswordResetMail',
            with:[
                'subject'=>$subject,
                'intro'=>$intro,
                'cta_link'=>$this->cta_link,
                'cta_content'=>$this->cta_content,
                'content'=>$content,
                'outro'=>$outro,
                'receiver_email'=>$this->email
            ]
        );
    }
}
