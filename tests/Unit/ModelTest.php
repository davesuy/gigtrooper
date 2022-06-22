<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Gigtrooper\Models\User;


class ModelTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        $this->visit('/')
            ->see('Laravel 5');
    }

    public function testModel()
    {
        $name = 'xxtestxx';
        $email = 'xxtestxx@testemail.com';

        $model = new User;
        $model->name = $name;
        $model->email = $email;
        $model->password = null;
        $model->remember_token = null;

        $attributes = $model->getAttributes();

        $expected = ['id' => null, 'name' => $name, 'email' => $email, 'password' => null, 'remember_token' => null];

        $this->assertEquals($expected, $attributes);

        $expected = 'User';
        $this->assertEquals($expected, $model->getLabel());
    }

    public function testMock()
    {
        $testClass = \Mockery::mock('Gigtrooper\TestClass')
            ->shouldReceive('testFunc')
            ->andReturn('resultni')
            ->mock();
    }

    public function testSaveFindUpdateDelete()
    {

        $name = 'xxtestxx';
        $email = 'xxtestxx@testemail.com';

        $model = new User;
        $model->name = $name;
        $model->email = $email;
        $model->save();

        $expected = 'xxtestxx@testemail.com';

        $userResult = User::findByAttribute('name', $name);
        $this->assertEquals($expected, $userResult->email);


        $expected = true;
        $deleteUser = new User;
        $deleteResult = $deleteUser->deleteByAttribute('name', $name);
        $this->assertEquals($expected, $deleteResult);
    }

}
