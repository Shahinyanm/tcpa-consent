<?php

namespace App\Events;


use App\Models\Consent;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConsentStatusUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Consent $consent) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('consent.' . $this->consent->id),
        ];
    }
}
