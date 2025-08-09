<?php

namespace App\Traits;
use Filament\Notifications\Notification;

trait HasSimpleNotify
{
    /**
     * Simple notify wrapper: $this->notify('success', 'Title', 'Optional body');
     *
     * Levels accepted: success, danger, warning, info (defaults to info).
     */
    protected function notify(string $level, string $title, ?string $body = null): void
    {
        $notification = Notification::make()
            ->title($title);

        if ($body) {
            $notification->body($body);
        }

        // map "level" to Notification styling helpers
        switch (strtolower($level)) {
            case 'success':
                $notification->success();
                break;
            case 'danger':
            case 'error':
                // Filament uses ->danger() in some examples — fallback to warning if not available
                if (method_exists($notification, 'danger')) {
                    $notification->danger();
                } else {
                    $notification->warning();
                }
                break;
            case 'warning':
                $notification->warning();
                break;
            default:
                $notification->iconColor('primary'); // or leave default
                break;
        }

        $notification->send();
    }
}
