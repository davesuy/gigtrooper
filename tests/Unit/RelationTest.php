<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Gigtrooper\Services\Neo4jRelationService;
use Gigtrooper\Models\User;
use Gigtrooper\Models\Role;


class RelationTest extends TestCase
{

    public function testArrayLabel()
    {
        $service = new Neo4jRelationService;

        $tos = [];
        $tos[] = new User;
        $tos[] = new User;
        $tos[] = new Role;

        $result = $service->areSameLabel($tos);

        $this->assertFalse($result);

        $tos = [];
        $tos[] = new User;
        $tos[] = new User;
        $tos[] = new Role;

        $result = $service->areSameLabel($tos);

        $this->assertFalse($result);
    }
}