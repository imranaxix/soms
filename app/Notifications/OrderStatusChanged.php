<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Order;
use App\Models\User;

class OrderStatusChanged extends Notification
{
    use Queueable;

    public $order;
    public $manufacturer;
    public $newStatus;

    public function __construct(Order $order, User $manufacturer, string $newStatus)
    {
        $this->order = $order;
        $this->manufacturer = $manufacturer;
        $this->newStatus = $newStatus;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $manufacturerName = $this->manufacturer->business_name ?? $this->manufacturer->name;

        if ($this->newStatus === 'In Progress') {
            $message = 'Order Accepted';
            $details = $manufacturerName . ' accepted your order ' . $this->order->order_number . '. Production has started.';
        } elseif ($this->newStatus === 'Rejected') {
            $message = 'Order Rejected';
            $details = $manufacturerName . ' rejected your order ' . $this->order->order_number . '.';
        } elseif ($this->newStatus === 'Cancelled') {
            $message = 'Order Cancelled';
            // Here 'manufacturer' property might actually be the shop owner if the shop owner cancelled it and is notifying the manufacturer.
            // Let's use the actor name
            $actorName = auth()->user()->role === 'shop_owner' ? (auth()->user()->business_name ?? auth()->user()->name) : $manufacturerName;
            $details = $actorName . ' cancelled order ' . $this->order->order_number . '.';
        } elseif ($this->newStatus === 'Delivered') {
            $message = 'Order Delivered';
            $details = $manufacturerName . ' has finished production for order ' . $this->order->order_number . '. Please confirm receipt.';
        } elseif ($this->newStatus === 'Completed') {
            $message = 'Order Completed';
            $actorName = auth()->user()->role === 'shop_owner' ? (auth()->user()->business_name ?? auth()->user()->name) : $manufacturerName;
            $details = $actorName . ' confirmed delivery. Order ' . $this->order->order_number . ' is now finalized.';
        } elseif ($this->newStatus === 'Stage Updated') {
            $message = 'Production Stage Updated';
            $details = $manufacturerName . ' updated a production stage on order ' . $this->order->order_number . '. Progress: ' . $this->order->progress_percent . '%.';
        } else {
            $message = 'Order Updated';
            $details = 'Order ' . $this->order->order_number . ' status changed to ' . $this->newStatus . '.';
        }

        return [
            'message' => $message,
            'details' => $details,
            'order_id' => $this->order->id,
            'url' => $notifiable->role === 'manufacturer'
                ? route('manufacturer.orders.show', $this->order->id, false)
                : route('shop.orders.show', $this->order->id, false),
        ];
    }
}
