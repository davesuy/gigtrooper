<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Gigtrooper\Services\FieldService;
use Gigtrooper\Services\FileUploadService;
use Gigtrooper\Fields\DropdownField;
use Gigtrooper\Fields\PlaintextField;
use Gigtrooper\Services\DateService;
use Gigtrooper\Services\FieldTypes;

use \Mockery as m;

class FieldTest extends TestCase
{
    private $fieldService;

    public function setUp()
    {
        parent::setUp();
        $this->fieldService = new FieldService;
    }

    public function testFriendlyNames()
    {
        $fields = [
            'firstname' => 'dale',
            'lastname' => 'ramirez'
        ];

        $friendlyNames = $this->fieldService->getFriendlyNames($fields);

        $expected = [
            'fields.firstname' => 'Firstname',
            'fields.lastname' => 'Lastname'
        ];

        $this->assertEquals($expected, $friendlyNames);
    }

    public function testAssetStorage()
    {
        $assetJsonValues = '{"metcon2.jpg":{"value":"metcon2.jpg","url":"\/images\/avatar\/1\/avatar\/metcon2.jpg"},"red.png":{"value":"red.png","url":"\/images\/avatar\/1\/avatar\/red.png"},"tisha.jpg":{"value":"tisha.jpg","url":"\/images\/avatar\/1\/avatar\/tisha.jpg"}}';

        $uploadService = new FileUploadService;

        $result = $uploadService->removeAssetValue("metcon2.jpg", $assetJsonValues);

        $expected = '{"red.png":{"value":"red.png","url":"\/images\/avatar\/1\/avatar\/red.png"},"tisha.jpg":{"value":"tisha.jpg","url":"\/images\/avatar\/1\/avatar\/tisha.jpg"}}';

        $this->assertEquals($expected, $result);
    }

    public function testFieldClass()
    {
        $settings = [
            'section' => ['default'],
            'handle' => 'role',
            'field' => 'DropdownField',
            'options' => [
                [
                    'value' => 'one',
                    'label' => 'One'
                ],
                [
                    'value' => 'two',
                    'label' => 'Two'
                ],
                [
                    'value' => 'three',
                    'label' => 'Three'
                ]
            ],
            'default' => [
                'two'
            ]
        ];

        $class = \Field::getFieldClass($settings);
        $expected = new DropdownField($settings);

        $this->assertEquals($expected, $class);
    }

    public function testDateException()
    {
        $dateService = new DateService;
        $this->expectException(\Exception::class);
        $dateService->getNumberShortMonth('March');
    }

    public function testDate()
    {
        $dateService = new DateService;
        $number = $dateService->getNumberShortMonth('Apr');
        $expected = 4;
        $this->assertEquals($expected, $number);

        $number = $dateService->getNumberShortMonth('Dec');
        $expected = 12;
        $this->assertEquals($expected, $number);
    }

    public function testDateFormat()
    {
        $settings = [];
        $dateService = new DateService;

        $dateFormat = $dateService->getDateFormat($settings);
        $expected = 'd M yy';
        $this->assertEquals($expected, $dateFormat['dateFormat']);

        $expected = 'inst.selectedYear, inst.selectedMonth, inst.selectedDay';
        $this->assertEquals($expected, $dateFormat['dateJs']);

        $dateFormat = $dateService->getDateFormat(['hideDay' => true]);
        $expected = 'M yy';
        $this->assertEquals($expected, $dateFormat['dateFormat']);

        $expected = 'inst.selectedYear, inst.selectedMonth';
        $this->assertEquals($expected, $dateFormat['dateJs']);

        $dateFormat = $dateService->getDateFormat(['hideDay' => true, 'hideMonth' => true]);
        $expected = 'yy';
        $this->assertEquals($expected, $dateFormat['dateFormat']);

        $expected = 'inst.selectedYear';
        $this->assertEquals($expected, $dateFormat['dateJs']);

        $dateFormat = $dateService->getDateFormat(['hideDay' => true, 'hideYear' => true]);
        $expected = 'M';
        $this->assertEquals($expected, $dateFormat['dateFormat']);

        $expected = 'inst.selectedMonth';
        $this->assertEquals($expected, $dateFormat['dateJs']);

        $dateFormat = $dateService->getDateFormat(['hideYear' => true]);
        $expected = 'd M';
        $this->assertEquals($expected, $dateFormat['dateFormat']);

        $expected = 'inst.selectedMonth, inst.selectedDay';
        $this->assertEquals($expected, $dateFormat['dateJs']);
    }

    public function testFieldTypes()
    {
        $service = new FieldTypes();

        $types = $service->getFieldsByHandles(['name', 'email']);

        $expected = [];

        $expected['name'] = [
            'handle' => 'name',
            'field' => 'PlaintextField',
            'rules' => 'required'
        ];

        $expected['email'] = [
            'handle' => 'email',
            'field' => 'PlaintextField',
            'rules' => 'required|email|unique:User,email',
            'disabled' => true,
            'params' => ['disabled' => true]
        ];

        $this->assertEquals($expected, $types);

        $service = new FieldTypes();
        $fields = [];

        $fields['name'] = [
            'handle' => 'name',
            'field' => 'PlaintextField',
            'rules' => 'required'
        ];
        $service->setFieldTypes($fields);

        $types = $service->getFieldsByHandles(['name']);

        $expected = [];

        $expected['name'] = [
            'handle' => 'name',
            'field' => 'PlaintextField',
            'rules' => 'required'
        ];

        $this->assertEquals($expected, $types);

        $service = new FieldTypes();

        $field = $service->getFieldByHandle('name');

        $expected = [
            'handle' => 'name',
            'field' => 'PlaintextField',
            'rules' => 'required'
        ];

        $this->assertEquals($expected, $field);
    }

    public function testOptionExist()
    {
        $service = new FieldTypes();

        $options = [];

        $options['Status'] = [
            'handle' => 'Status',
            'generate' => true,
            'field' => 'DropdownField',
            'options' => [
                [
                    'value' => 'live',
                    'label' => 'Live'
                ],
                [
                    'value' => 'pending',
                    'label' => 'Pending'
                ],
                [
                    'value' => 'disabled',
                    'label' => 'Disabled'
                ],
                [
                    'value' => 'expired',
                    'label' => 'Expired'
                ]
            ]
        ];

        $result = $service->isOptionExist($options['Status']['options'], 'test');

        $this->assertFalse($result);

        $result = $service->isOptionExist($options['Status']['options'], 'disabled');

        $this->assertTrue($result);
    }
}