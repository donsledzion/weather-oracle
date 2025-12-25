<?php

namespace App\Mail;

use App\Models\MonitoringRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FinalSummary extends Mailable
{
    use Queueable, SerializesModels;

    public MonitoringRequest $request;
    public string $notificationToken;
    public array $providerStats;

    /**
     * Create a new message instance.
     */
    public function __construct(MonitoringRequest $request, string $notificationToken)
    {
        $this->request = $request;
        $this->notificationToken = $notificationToken;
        $this->providerStats = $this->calculateProviderStats();
    }

    /**
     * Calculate statistics per provider
     */
    protected function calculateProviderStats(): array
    {
        $snapshots = $this->request->forecastSnapshots()
            ->with('weatherProvider')
            ->orderBy('created_at')
            ->get();

        $stats = [];

        foreach ($snapshots->groupBy('weather_provider_id') as $providerId => $providerSnapshots) {
            $provider = $providerSnapshots->first()->weatherProvider;

            $stats[] = [
                'provider_name' => $provider->name,
                'snapshot_count' => $providerSnapshots->count(),
                'first_forecast' => $providerSnapshots->first(),
                'last_forecast' => $providerSnapshots->last(),
                'avg_temp' => round($providerSnapshots->avg('avg_temp'), 1),
                'min_temp' => round($providerSnapshots->min('min_temp'), 1),
                'max_temp' => round($providerSnapshots->max('max_temp'), 1),
            ];
        }

        return $stats;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('app.email_final_summary_subject', [
                'location' => $this->request->location,
                'date' => $this->request->target_date->format('Y-m-d')
            ]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.final-summary',
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
