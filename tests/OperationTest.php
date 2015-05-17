<?php

//todo fix this madness
require_once "vendor/autoload.php";

use Wholemeal\QueryFilter\Parsed\Expression;
use Wholemeal\QueryFilter\Parsed\Operation;

class OperationTest extends PHPUnit_Framework_TestCase
{
    public function testFieldName()
    {
        $operation = new Operation();
        $operation->setFieldName("test_name");
        $this->assertEquals("test_name", $operation->getFieldName());
    }

    public function testOperator()
    {
        $operation = new Operation();
        $operation->setOperator("like");
        $this->assertEquals("like", $operation->getOperator());
    }

    public function testValues()
    {
        $operation = new Operation();
        $operation->addValue("one");
        $operation->addValue("two");
        $this->assertEquals(["one", "two"], $operation->getValues());
    }

    public function testHasCorrectExpression()
    {
        $parent_expression = new Expression();
        $operation = new Operation();
        $operation->setParent($parent_expression);
        $this->assertEquals($parent_expression, $operation->getParent());
    }

    public function testAsArray()
    {
        $operation = new Operation();
        $operation->setFieldName("name");
        $operation->setOperator("eq");
        $operation->addValue("one");
        $operation->addValue("two");

        $expected = [
            'field_name' => 'name',
            'operator' => 'eq',
            'values' => [
                'one', 'two'
            ]
        ];

        $this->assertEquals($expected, $operation->toArray());
    }
}