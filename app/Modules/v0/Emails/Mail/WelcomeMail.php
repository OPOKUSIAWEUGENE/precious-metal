<?php

namespace App\Modules\v0\Emails\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class WelcomeMail extends Mailable
{
    use Queueable;

    public string $email;
    public string $password;
    public ?string $organizationName;
    public string $cta_link;
    public string $cta_content;

    /**
     * Create a new message instance.
     */
    public function __construct(string $email, string $password, ?string $organizationName = null)
    {
        $this->email = $email;
        $this->password = $password;
        $this->organizationName = $organizationName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $from = config('mail.from.address') ?: 'hello@neutek.com';
        $name = config('mail.from.name') ?: config('app.name');

        return new Envelope(
            from: new Address($from, $name),
            subject: config('app.name') . ": Account Setup",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $portalUrl = config('app.url') . '/login';
        
        $subject = config('app.name') . ": Account Setup"; 
        $intro = "Welcome to " . config('app.name') . "!";
        
        $content = "<p>Your account has been successfully created";
        if ($this->organizationName) {
            $content .= " for <strong>" . htmlspecialchars($this->organizationName) . "</strong>";
        }
        $content .= ". Use the credentials below to access the portal:</p>";
        
        $content .= "<p style='background-color: #f8f9fa; padding: 16px; border-radius: 6px; margin: 16px 0;'>";
        $content .= "<strong>Email:</strong> " . htmlspecialchars($this->email) . "<br>";
        $content .= "<strong>Password:</strong> " . htmlspecialchars($this->password);
        $content .= "</p>";
        
        $content .= "<p style='margin: 16px 0; padding: 16px; background-color: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 4px; font-size: 14px;'>";
        $content .= "<strong>Important:</strong> Please change your password immediately after your first login for security purposes.</p>";
        
        $outro = "Welcome aboard!"; 
        $this->cta_link = $portalUrl;
        $this->cta_content = "Access Account";

        return new Content(
            view: 'mails.welcome',
            with:[
                'subject'=>$subject,
                'intro'=>$intro,
                'cta_link'=>$this->cta_link,
                'cta_content'=>$this->cta_content,
                'content'=>$content,
                'outro'=>$outro,
                'email'=>$this->email,
                'password'=>$this->password,
                'receiver_email'=>$this->email
            ]
        );
    }
}
