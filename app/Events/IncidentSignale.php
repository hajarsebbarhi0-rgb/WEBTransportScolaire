<?php

namespace App\Events;

use App\Models\Incident;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IncidentSignale implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $incident;

    public function __construct(Incident $incident)
    {
        $this->incident = $incident;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('trajet.' . $this->incident->trajet_id);
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->incident->id,
            'description' => $thisent->description,
            'type' => $this->incident->type,
            'created_by' => $this->incident->created_by,
            'status' => $this->incident->status,
        ];
    }
}
