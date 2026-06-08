<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NaskahWorkflowEmailNotification extends Notification
{
    use Queueable;

    /**
     * @param  array<string, string|null>  $details
     */
    public function __construct(
        private readonly string $subject,
        private readonly string $greeting,
        private readonly string $opening,
        private readonly array $details,
        private readonly string $bodyMessage,
        private readonly string $closing,
        private readonly ?string $actionText = null,
        private readonly ?string $actionUrl = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->subject)
            ->view('emails.naskah-workflow', [
                'subject' => $this->subject,
                'greeting' => $this->greeting,
                'opening' => $this->opening,
                'details' => $this->details,
                'bodyMessage' => $this->bodyMessage,
                'closing' => $this->closing,
                'actionText' => $this->actionText,
                'actionUrl' => $this->actionUrl,
            ]);
    }
}
