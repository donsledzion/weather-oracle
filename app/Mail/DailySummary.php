<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class DailySummary extends Mailable
{
    use Queueable, SerializesModels;

    public Collection $requests;
    public string $recipientEmail;
    public string $notificationToken;
    public int $totalSnapshots;

    /**
     * Create a new message instance.
     */
    public function __construct(Collection $requests, string $recipientEmail, string $notificationToken)
    {
        $this->requests = $requests;
        $this->recipientEmail = $recipientEmail;
        $this->notificationToken = $notificationToken;
        $this->totalSnapshots = $requests->sum(function ($request) {
            return $request->snapshots()->whereDate('created_at', today())->count();
        });
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('app.email_daily_summary_subject', [
                'count' => $this->requests->count()
            ]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.daily-summary',
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
