<?php

namespace App\Mail\UserModule;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrganizationLinkedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $email;
    public ?string $organizationName;
    public string $cta_link;
    public string $cta_content;

    /**
     * Create a new message instance.
     */
    public function __construct(string $email, ?string $organizationName = null)
    {
        $this->email = $email;
        $this->organizationName = $organizationName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $from = config('mail.from.address') ?: 'hello@neutek.com';
        $name = config('mail.from.name') ?: 'Neutek Solutions';

        return new Envelope(
            from: new Address($from, $name),
            subject: "Neutek Solutions: New Organization Access",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $portalUrl = config('app.url') . '/login';
        
        $subject = "Neutek Solutions: New Organization Access"; 
        $intro = "Hello!";
        
        $content = "<p>Your account has been successfully linked to";
        if ($this->organizationName) {
            $content .= " <strong>" . htmlspecialchars($this->organizationName) . "</strong>";
        }
        $content .= " on the Neutek Portal.</p>";
        
        $content .= "<p>You can now access their dashboard using your existing login credentials for the email: <strong>" . htmlspecialchars($this->email) . "</strong>.</p>";
        
        $content .= "<p>If you don't remember your password, you can use the 'Forgot Password' option on the login page.</p>";
        
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
                'receiver_email'=>$this->email
            ]

        );
    }
}
