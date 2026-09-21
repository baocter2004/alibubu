<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPlaced extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order->loadMissing('items');
        $this->locale($order->locale ?: config('app.locale'));
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('client.mail.order.subject', ['code' => $this->order->code]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'components.mails.order-placed',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
