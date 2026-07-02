<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Order;
use App\Models\User;

class NewOrderReceived extends Notification
{
    use Queueable;

    public $order;
    public $shopOwner;

    public function __construct(Order $order, User $shopOwner)
    {
        $this->order = $order;
        $this->shopOwner = $shopOwner;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => 'New Order Received',
            'details' => ($this->shopOwner->business_name ?? $this->shopOwner->name)
                . ' placed order ' . $this->order->order_number
                . ' — Rs ' . number_format($this->order->total_amount),
            'order_id' => $this->order->id,
            'url' => route('manufacturer.orders.show', $this->order->id),
        ];
    }
}
