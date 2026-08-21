<?php

namespace App\Notifications;

use App\Models\Connection;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewChatMessage extends Notification
{
    use Queueable;

    public function __construct(
        protected User $sender,
        protected Connection $chatConnection
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $senderName = $this->sender->business_name ?? $this->sender->name;

        return [
            'title'   => 'New message from ' . $senderName,
            'body'    => $senderName . ' sent you a message.',
            'url'     => '/chat/' . $this->chatConnection->id,
        ];
    }
}
