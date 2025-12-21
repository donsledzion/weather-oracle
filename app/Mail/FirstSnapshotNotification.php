<?php

namespace App\Mail;

use App\Models\MonitoringRequest;
use App\Models\ForecastSnapshot;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FirstSnapshotNotification extends Mailable
{
    use Queueable, SerializesModels;

    public MonitoringRequest $request;
    public ForecastSnapshot $snapshot;
    public string $providerName;
    public string $notificationToken;

    /**
     * Create a new message instance.
     */
    public function __construct(MonitoringRequest $request, ForecastSnapshot $snapshot, string $notificationToken)
    {
        $this->request = $request;
        $this->snapshot = $snapshot;
        $this->providerName = $snapshot->provider;
        $this->notificationToken = $notificationToken;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('app.email_first_snapshot_subject', [
                'location' => $this->request->location,
                'provider' => $this->providerName
            ]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.first-snapshot-notification',
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
