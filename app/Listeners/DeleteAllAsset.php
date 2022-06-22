<?php

namespace Gigtrooper\Listeners;

use Gigtrooper\Events\OnElementDelete;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeleteAllAsset
{
	public $ids;

	public $element;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OnElementDelete  $event
     * @return void
     */
    public function handle(OnElementDelete $event)
    {
    	$ids = $event->ids;

	    $label = $event->element->initModel()->getLabel();

	    $label = strtolower($label);

	    if (!empty($ids))
	    {
		    foreach ($ids as $id)
		    {
			    $directory = config('filesystems.prefix') . '/' . $label . '/' . $id . '/';
					
			    \Storage::deleteDirectory($directory);
		    }
	    }
    }
}
