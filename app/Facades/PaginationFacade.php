<?php

namespace Gigtrooper\Facades;

use Illuminate\Support\Facades\Facade;

class PaginationFacade extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'pagination'; }

}