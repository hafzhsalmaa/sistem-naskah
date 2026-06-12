<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    public function redirect(Request $request, string $id): RedirectResponse
    {
        $notification = $this->findUserNotification($request, $id);

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        return redirect()->to(url($this->resolveNotificationPath($notification)));
    }

    public function markAsRead(Request $request, string $id): RedirectResponse
    {
        $notification = $this->findUserNotification($request, $id);

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        return back()->with('status', 'Notifikasi ditandai sudah dibaca.');
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('status', 'Semua notifikasi ditandai sudah dibaca.');
    }

    private function findUserNotification(Request $request, string $id): DatabaseNotification
    {
        return $request->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();
    }

    private function resolveNotificationPath(DatabaseNotification $notification): string
    {
        $url = $notification->data['url'] ?? route('dashboard', [], false);

        if (! is_string($url) || trim($url) === '') {
            return route('dashboard', [], false);
        }

        $url = trim($url);
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
