<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class StockAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $products;

    public function __construct($products)
    {
        $this->products = $products;
    }

    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🔔 Alerte Stock Bas')
            ->greeting('Bonjour ' . $notifiable->first_name)
            ->line('Plusieurs produits sont en stock bas:')
            ->line('')
            ->line('**Produits concernés:**')
            ->line($this->products->map(function($product) {
                return "- {$product->name}: {$product->quantity} / {$product->min_quantity}";
            })->implode("\n"))
            ->line('')
            ->action('Voir l\'inventaire', url('/stock/inventory'))
            ->line('Merci de vérifier rapidement!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Alerte Stock Bas',
            'message' => count($this->products) . ' produits sont en stock bas',
            'products' => $this->products->pluck('id'),
            'action_url' => '/stock/inventory',
            'icon' => 'exclamation-triangle',
            'color' => 'yellow',
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => 'Alerte Stock Bas',
            'message' => count($this->products) . ' produits sont en stock bas',
            'time' => now()->toDateTimeString(),
        ]);
    }
}