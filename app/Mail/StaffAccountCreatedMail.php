<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StaffAccountCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $staffName;
    public string $staffEmail;
    public string $plainPassword;

    /**
     * Create a new message instance.
     */
    public function __construct(string $staffName, string $staffEmail, string $plainPassword)
    {
        $this->staffName     = $staffName;
        $this->staffEmail    = $staffEmail;
        $this->plainPassword = $plainPassword;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thông tin tài khoản nhân viên - Hệ thống CRM VLXD',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.staff_account_created',
            with: [
                'staffName'     => $this->staffName,
                'staffEmail'    => $this->staffEmail,
                'plainPassword' => $this->plainPassword,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
