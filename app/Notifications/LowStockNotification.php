<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    public function __construct(public Product $product)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Low stock alert: '.$this->product->name)
            ->line('The product '.$this->product->name.' is running low.')
            ->line('Current quantity: '.$this->product->qty)
            ->line('Reorder level: '.$this->product->reorder_level)
            ->action('Review Product', url('/products/'.$this->product->id.'/edit'))
            ->line('Please place a reorder request as soon as possible.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'sku' => $this->product->sku,
            'quantity' => $this->product->qty,
            'reorder_level' => $this->product->reorder_level,
            'message' => 'Low stock alert for '.$this->product->name.'. Current quantity: '.$this->product->qty.', reorder level: '.$this->product->reorder_level.'.',
        ];
    }
}
