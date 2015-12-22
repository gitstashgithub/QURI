<?php

use BkvFoundry\Quri\Parsed\Expression;

class ExpressionTest extends PHPUnit_Framework_TestCase
{
    public function testExpressionType()
    {
        $expression = new Expression();
        $expression->setType("and");
        $this->assertEquals("and", $expression->getType());
    }

    public function testSetParent()
    {
        $expression = new Expression();
        $expression->setType("and"); // so the class is unique
        $child = new Expression();
        $child->setType("or");

        $child->setParent($expression);
        $this->assertEquals($child->getParent(), $expression);
    }

    public function testCreateChildExpression()
    {
        $expression = new Expression();
        $child = $expression->createNestedExpression();
        $child->setType("or");

        $this->assertEquals($child->getParent(), $expression);
    }


    public function testCreateChildOperation()
    {
        $expression = new Expression();
        $operation = $expression->createOperation();
        $this->assertEquals($expression->operations()[0], $operation);
    }

    public function testChildOperationHasParent()
    {
        $expression = new Expression();
        $operation = $expression->createOperation();

        $this->assertEquals($operation->getParent(), $expression);
    }

    public function testToArray()
    {
        $expression = new Expression();
        $expression->setType("and");

        $expected = [
            'type' => 'expression',
            'and_or' => 'and',
            'nested_expressions' => [],
            'operations' => []
        ];

        $this->assertEquals($expression->toArray(), $expected);
    }

    public function testToArrayWithChildren()
    {
        $expression = new Expression();
        $expression->setType("and");
        $child = $expression->createNestedExpression();
        $child->setType("or");

        $expected = [
            'type' => 'expression',
            'and_or' => 'and',
            'nested_expressions' => [
                [
                    'type' => 'expression',
                    'and_or' => 'or',
                    'nested_expressions' => [],
                    'operations' => []
                ]
            ],
            'operations' => []
        ];

        $this->assertEquals($expression->toArray(), $expected);
    }

    public function testToArrayWithChildExpressions()
    {
        $expression = new Expression();
        $expression->setType("and");
        $operation = $expression->createOperation();
        $operation->setFieldName("field_name");
        $operation->setOperator("eq");
        $operation->addValue("val_1");

        $expected = [
            'type' => 'expression',
            'and_or' => 'and',
            'nested_expressions' => [],
            'operations' => [
                [
                    'type' => 'operation',
                    'field_name' => 'field_name',
                    'operator' => 'eq',
                    'values' => [
                        'val_1'
                    ]
                ]
            ]
        ];

        $this->assertEquals($expression->toArray(), $expected);
    }
}