<?php

namespace Gigtrooper\Events;

use Gigtrooper\Elements\BaseElement;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class OnElementDelete
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $element;

    public $ids;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(BaseElement $element, $ids = array())
    {
    	$this->element = $element;

    	$this->ids = $ids;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
