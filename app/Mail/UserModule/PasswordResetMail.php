<?php

namespace App\Mail\UserModule;

use App\Utils\Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $cta_link;
    public string $cta_content;

    /**
     * Create a new message instance.
     */
    public function __construct(public string $email, public string $verification_code)
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
            from: new Address($from ?: 'hello@neutek.com', $name ?: 'Neutek Solutions'),
            subject: "Neutek Solutions: Password Reset",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $loginUrl = config('app.url') . '/password-reset';
        
        $subject = "Neutek Solutions: Password Reset"; 
        $intro = "Password Reset Request"; 
        
        $content = "<p style='margin-bottom: 24px;'>Please use the following unique verification code to authorize this request:</p>";
        $content .= Helper::formatVerificationCodeBoxes($this->verification_code);
        $content .= "<div style='margin-top: 24px; padding: 16px; background-color: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 4px; color: #92400e; font-size: 14px;'>";
        $content .= "<strong>Note:</strong> This security code is valid for 10 minutes. If you did not make this request, please ignore this email.";
        $content .= "</div>";

        $outro = "Neutek Support Team"; 
        
        // Building the signed link
        $params = [
            'email' => $this->email,
            'code' => $this->verification_code,
            'token' => $this->verification_code,
            'signature' => hash_hmac('sha256', $this->email . $this->verification_code, config('app.key'))
        ];
        $this->cta_link = $loginUrl . '?' . http_build_query($params);

        $content .= "<p style='margin-top: 30px; margin-bottom: 8px; font-size: 14px; color: #64748b;'>Alternatively, you can copy and paste the following link into your browser:</p>";
        $content .= "<p style='background-color: #f8f9fa; padding: 10px 14px; border-radius: 6px; margin: 0 0 -10px 0; word-break: break-all; font-size: 14px;'><a href='{$this->cta_link}' style='color: #1a2b5f; text-decoration: underline;'>{$this->cta_link}</a></p>";

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
