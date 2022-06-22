<?php namespace Gigtrooper\Facades;

use Illuminate\Support\Facades\Facade;

class Neo4jRelationFacade extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'neo4jRelation'; }

}