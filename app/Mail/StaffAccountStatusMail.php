<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StaffAccountStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $staffName;
    public bool $isLocked;

    /**
     * Create a new message instance.
     */
    public function __construct(string $staffName, bool $isLocked)
    {
        $this->staffName = $staffName;
        $this->isLocked = $isLocked;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thông báo trạng thái tài khoản - Hệ thống CRM VLXD',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.staff_account_status',
            with: [
                'staffName' => $this->staffName,
                'isLocked' => $this->isLocked,
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
