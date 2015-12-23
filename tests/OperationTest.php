<?php

use BkvFoundry\Quri\Parsed\Expression;
use BkvFoundry\Quri\Parsed\Operation;

class OperationTest extends PHPUnit_Framework_TestCase
{
    public function testFieldName()
    {
        $operation = new Operation();
        $operation->setFieldName("test_name");
        $this->assertEquals("test_name", $operation->fieldName());
    }

    public function testOperator()
    {
        $operation = new Operation();
        $operation->setOperator("like");
        $this->assertEquals("like", $operation->operator());
    }

    public function testValues()
    {
        $operation = new Operation();
        $operation->setOperator("in");
        $operation->addValue("one");
        $operation->addValue("two");
        $this->assertEquals(["one", "two"], $operation->values());
    }

    public function testFirstValues()
    {
        $operation = new Operation();
        $operation->setOperator("eq");
        $operation->addValue("one");
        $this->assertEquals("one", $operation->firstValue());
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
        $operation->setOperator("between");
        $operation->addValue("one");
        $operation->addValue("two");

        $expected = [
            'field_name' => 'name',
            'operator' => 'between',
            'type' => 'operation',
            'values' => [
                'one', 'two'
            ]
        ];

        $this->assertEquals($expected, $operation->toArray());
    }
}