<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Alerte;
use App\Models\Categorie;

class AlertNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * L'instance de l'alerte.
     *
     * @var \App\Models\Alerte
     */
    public $alert;

    /**
     * Create a new message instance.
     */
    public function __construct(Alerte $alerte)
    {
        $this->alert = $alerte->load('categorie');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = 'Alerte système - ';
        $alertTypes = [
            'stock_bas' => 'Stock Bas',
            'peremption' => 'Date de Péremption Proche',
            'mouvement_important' => 'Mouvement de Stock Important',
        ];

        $subject .= $alertTypes[$this->alert->type] ?? 'Notification Générique';

        if ($this->alert->categorie) {
             $subject .= ' pour la catégorie "' . $this->alert->categorie->nom . '"';
         } else {
             $subject .= ' (Toutes catégories)';
         }

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.alerts.notification',
            with: [
                'alert' => $this->alert,
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
