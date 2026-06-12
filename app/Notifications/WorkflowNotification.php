<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WorkflowNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $title,
        private readonly string $message,
        private readonly string $url
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->normalizeUrl($this->url),
        ];
    }

    private function normalizeUrl(string $url): string
    {
        $url = trim($url);

        if ($url === '') {
            return route('dashboard', [], false);
        }

        $parts = parse_url($url);

        if ($parts === false) {
            return route('dashboard', [], false);
        }

        if (isset($parts['scheme']) || isset($parts['host'])) {
            $path = $parts['path'] ?? '/';

            if (! empty($parts['query'])) {
                $path .= '?'.$parts['query'];
            }

            return $path;
        }

        return str_starts_with($url, '/') ? $url : '/'.ltrim($url, '/');
    }
}
