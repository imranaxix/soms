<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\User;

class ConnectionRequestReceived extends Notification
{
    use Queueable;

    public $sender;

    public function __construct(User $sender)
    {
        $this->sender = $sender;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => 'New Connection Request',
            'details' => $this->sender->business_name . ' wants to connect.',
            'sender_id' => $this->sender->id,
            'url' => route($notifiable->role === 'manufacturer' ? 'manufacturer.connections.index' : 'shop.connections', [], false)
        ];
    }
}
