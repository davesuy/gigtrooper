<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Gigtrooper\Elements\UserElement;
use Gigtrooper\Fields\DropdownField;
use Gigtrooper\Services\FieldService;
use Gigtrooper\Services\ElementsService;
use Gigtrooper\Services\CriteriaService;

use \Mockery as m;

class ElementTest extends TestCase
{
    private $fieldData;
    private $fieldService;

    public function setUp()
    {
        parent::setUp();

        $this->fieldService = new FieldService;
        $fields = [];

        $fields[] = [
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

        $fields[] = [
            'handle' => 'category',
            'section' => ['writer'],
            'category' => ['music'],
            'params' => ['data-align' => 'left']
        ];

        $fields[] = [
            'handle' => 'email',
            'field' => 'PlaintextField',
            'rules' => 'required|email',
            'element' => true,
            'params' => ['disabled' => true]
        ];

        $fields[] = [
            'handle' => 'talent',
            'field' => 'PlaintextField',
            'rules' => 'required',
            'element' => false,
            'params' => ['disabled' => true]
        ];

        $this->fieldData = $fields;
    }

    public function testFieldHandle()
    {
        $service = $this->fieldService;

        $expected = $this->fieldData;

        $element = new UserElement;
        $element->setFieldData($expected);

        $service->setElement($element);
        $fieldArray = $element->getFieldByHandle('role');

        $this->assertEquals($expected[0], $fieldArray);
    }

    public function testMatches()
    {
        $fields = $this->fieldData;

        $this->fieldService->setSection('default');

        $bool = $this->fieldService->isMatchSection($fields[0]['section']);

        $this->assertTrue($bool);

        $this->fieldService->setSection('writer');

        $bool = $this->fieldService->isMatchSection($fields[1]['section']);

        $this->assertTrue($bool);

        $this->fieldService->setCategory('music');
        $bool = $this->fieldService->isMatchCategory($fields[1]['category']);

        $this->assertTrue($bool);
    }

    public function testSelectedValues()
    {
        $mockAll = m::mock('Gigtrooper\Models\User')
            ->shouldReceive('all')
            ->andReturn(['one', 'three'])
            ->mock();
        $model = m::mock('Gigtrooper\Models\User')
            ->shouldReceive('getFieldValuesByHandle')
            ->andReturn($mockAll)
            ->mock();
        $fieldData = $this->fieldData[0];

        $element = m::mock('Gigtrooper\Elements\UserElement')
            ->shouldReceive('getModel')
            ->andReturn($model)
            ->mock();
    }

    public function testGetOptionElements()
    {
        $data = $this->fieldData;

        $element = new UserElement;
        $element->setFieldData($data);
        $this->fieldService->setElement($element);
        $inElements = $element->getOptionElements('element');

        $expected = ['email'];

        $this->assertEquals($inElements, $expected);

        $result = $element->isFieldinElements('email');

        $this->assertTrue($result);

        $result = $element->isFieldinElements('category');

        $this->assertFalse($result);
    }

    public function testIsFieldParams()
    {
        $data = $this->fieldData;

        $element = new UserElement;
        $element->setFieldData($data);
        $this->fieldService->setElement($element);

        $result = $this->fieldService->isFieldParams('email', 'disabled');

        $this->assertTrue($result);

        $result = $this->fieldService->isFieldParams('role', 'disabled');

        $this->assertFalse($result);

        $result = $this->fieldService->isFieldParams('category', 'data-align');

        $this->assertTrue($result);
    }

    public function testElementsService()
    {
        $service = new ElementsService;

        $element = $service->getElement('User');

        $expected = new \Gigtrooper\Elements\UserElement;

        $this->assertEquals($expected, $element);
    }

    public function testOperatorValues()
    {
        $fields = [];
        $fields[] = [
            'handle' => 'email',
            'field' => 'PlaintextField',
            'rules' => 'required|email|unique:User,email',
            'element' => true,
            'disabled' => true,
            'params' => ['disabled' => true]
        ];

        $element = new UserElement;
        $element->setFieldData($fields);

        $user = new \Gigtrooper\Models\User;

        $elementLabel = $user->getLabel();

        $expected = "User";

        $this->assertEquals($expected, $elementLabel);

        $isElement = $element->isFieldinElements('email');


        $criteriaService = new CriteriaService;

        $options = [];
        $options['fields'][0]['handles'][0]['handle'] = "role";
        $options['fields'][0]['handles'][0]['value'] = "one";

        $criteriaService->setOptions($user, $options, $this->fieldData);


        $this->assertTrue($isElement);

        $valueKey = "{stringKey}";
        $value = "string";
        $operatorValues = $criteriaService->getOperatorValues($value, $valueKey);

        $expected = "= {stringKey}";

        $this->assertEquals($expected, $operatorValues);

        $value = "*";
        $operatorValues = $criteriaService->getOperatorValues($value);
        $expected = "IS NOT NULL";

        $this->assertEquals($expected, $operatorValues);

        $valueKey = "{arrayKey}";
        $value = ['one', 'two'];

        $operatorValues = $criteriaService->getOperatorValues($value, $valueKey);

        $expected = "IN {arrayKey}";

        $this->assertEquals($expected, $operatorValues);

        $value = true;
        $operatorValues = $criteriaService->getOperatorValues($value);
        $expected = '= true';
        $this->assertEquals($expected, $operatorValues);
    }

    public function testIsFieldinDefaultTrue()
    {
        $criteriaService = new CriteriaService;

        $this->expectException(\Exception::class);

        $criteriaService->isFieldinDefaultTrue('notexist', $this->fieldData);

        $result = $criteriaService->isFieldinDefaultTrue('email', $this->fieldData);

        $this->assertTrue($result);

        $result = $criteriaService->isFieldinDefaultTrue('talent', $this->fieldData);

        $this->assertFalse($result);

        $result = $criteriaService->isFieldinDefaultTrue('role', $this->fieldData);

        $this->assertFalse($result);
    }

    public function testField()
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

        $model = m::mock('Gigtrooper\Models\User')
            ->shouldReceive('getFieldsArray')
            ->andReturn(['two', 'three'])
            ->mock();

        $element = m::mock('Gigtrooper\Elements\UserElement')
            ->shouldReceive('getModel')
            ->andReturn($model)
            ->mock();

        $field = new DropdownField($settings);
        $field->setElement($element);

        $oldFields = ['role' => 'one'];
        $optionValues = $field->getFieldOptions($oldFields);

        $expected = ['one'];
        $this->assertEquals($expected, $optionValues);

        $field = new DropdownField($settings);
        $field->setElement($element);

        $oldFields = null;
        $optionValues = $field->getFieldOptions($oldFields);

        $expected = ['two', 'three'];
        $this->assertEquals($expected, $optionValues);

        $element = m::mock('Gigtrooper\Elements\UserElement')
            ->shouldReceive('getModel')
            ->andReturn(null)
            ->mock();

        $field = new DropdownField($settings);
        $field->setElement($element);
        $oldFields = null;
        $optionValues = $field->getFieldOptions($oldFields);

        $expected = ['two'];
        $this->assertEquals($expected, $optionValues);
    }
}