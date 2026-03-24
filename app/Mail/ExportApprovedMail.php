<?php

namespace App\Mail;

use App\Models\Export;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExportApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $export;

    /**
     * Create a new message instance.
     */
    public function __construct(Export $export)
    {
        $this->export = $export;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Xác nhận đơn hàng: ' . $this->export->code,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.export_approved',
            with: [
                'export' => $this->export,
                'customer' => $this->export->customer,
                'details' => $this->export->details()->with('product')->get(),
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
