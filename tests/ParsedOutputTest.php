<?php

use BkvFoundry\Quri\ParserFactory;

class ParsedOutputTest extends PHPUnit_Framework_TestCase
{
    protected function performTest($str, array $expected)
    {
        $parser = ParserFactory::make($str);
        $this->assertEquals($parser->getResults()->toArray(), $expected);
    }

    public function testEquals()
    {
        $this->performTest(
            'field_1.eq(1111)',
            [
                "type" => "expression",
                "and_or" => "and",
                "nested_expressions" => [],
                "operations" => [
                    [
                        "type" => "operation",
                        "field_name" => "field_1",
                        "operator" => "eq",
                        "values" => [
                            "1111"
                        ]
                    ]
                ]
            ]
        );
    }

    public function testNotEquals()
    {
        $this->performTest(
            'field_1.neq(1111)',
            [
                "type" => "expression",
                "and_or" => "and",
                "nested_expressions" => [],
                "operations" => [
                    [
                        "type" => "operation",
                        "field_name" => "field_1",
                        "operator" => "neq",
                        "values" => [
                            "1111"
                        ]
                    ]
                ]
            ]
        );
    }

    public function testIn()
    {
        $this->performTest(
            'field_1.in(1111)',
            [
                "type" => "expression",
                "and_or" => "and",
                "nested_expressions" => [],
                "operations" => [
                    [
                        "type" => "operation",
                        "field_name" => "field_1",
                        "operator" => "in",
                        "values" => [
                            "1111"
                        ]
                    ]
                ]
            ]
        );
    }

    public function testOut()
    {
        $this->performTest(
            'field_1.out(1111)',
            [
                "type" => "expression",
                "and_or" => "and",
                "nested_expressions" => [],
                "operations" => [
                    [
                        "type" => "operation",
                        "field_name" => "field_1",
                        "operator" => "out",
                        "values" => [
                            "1111"
                        ]
                    ]
                ]
            ]
        );
    }

    public function testLike()
    {
        $this->performTest(
            'field_1.like(1111)',
            [
                "type" => "expression",
                "and_or" => "and",
                "nested_expressions" => [],
                "operations" => [
                    [
                        "type" => "operation",
                        "field_name" => "field_1",
                        "operator" => "like",
                        "values" => [
                            "1111"
                        ]
                    ]
                ]
            ]
        );
    }

    public function testBetween()
    {
        $this->performTest(
            'field_1.between(1111)',
            [
                "type" => "expression",
                "and_or" => "and",
                "nested_expressions" => [],
                "operations" => [
                    [
                        "type" => "operation",
                        "field_name" => "field_1",
                        "operator" => "between",
                        "values" => [
                            "1111"
                        ]
                    ]
                ]
            ]
        );
    }

    public function testGt()
    {
        $this->performTest(
            'field_1.gt(1111)',
            [
                "type" => "expression",
                "and_or" => "and",
                "nested_expressions" => [],
                "operations" => [
                    [
                        "type" => "operation",
                        "field_name" => "field_1",
                        "operator" => "gt",
                        "values" => [
                            "1111"
                        ]
                    ]
                ]
            ]
        );
    }

    public function testGte()
    {
        $this->performTest(
            'field_1.gte(1111)',
            [
                "type" => "expression",
                "and_or" => "and",
                "nested_expressions" => [],
                "operations" => [
                    [
                        "type" => "operation",
                        "field_name" => "field_1",
                        "operator" => "gte",
                        "values" => [
                            "1111"
                        ]
                    ]
                ]
            ]
        );
    }

    public function testLt()
    {
        $this->performTest(
            'field_1.lt(1111)',
            [
                "type" => "expression",
                "and_or" => "and",
                "nested_expressions" => [],
                "operations" => [
                    [
                        "type" => "operation",
                        "field_name" => "field_1",
                        "operator" => "lt",
                        "values" => [
                            "1111"
                        ]
                    ]
                ]
            ]
        );
    }

    public function testLte()
    {
        $this->performTest(
            'field_1.lte(1111)',
            [
                "type" => "expression",
                "and_or" => "and",
                "nested_expressions" => [],
                "operations" => [
                    [
                        "type" => "operation",
                        "field_name" => "field_1",
                        "operator" => "lte",
                        "values" => [
                            "1111"
                        ]
                    ]
                ]
            ]
        );
    }

    public function testMultiLevelExpression()
    {
        $this->performTest(
            '(field_1.neq(1111))',
            [
                "type" => "expression",
                "and_or" => "and",
                "nested_expressions" => [
                    [
                        "type" => "expression",
                        "and_or" => "and",
                        "nested_expressions" => [],
                        "operations" => [
                            [
                                "type" => "operation",
                                "field_name" => "field_1",
                                "operator" => "neq",
                                "values" => [
                                    "1111"
                                ]
                            ]
                        ]
                    ]
                ],
                "operations" => []
            ]
        );
    }

    public function testMultiLevelExpressionWithOr()
    {
        $this->performTest(
            '(field_1.neq(1111)|field_2.eq(2222))',
            [
                "type" => "expression",
                "and_or" => "and",
                "nested_expressions" => [
                    [
                        "type" => "expression",
                        "and_or" => "or",
                        "nested_expressions" => [],
                        "operations" => [
                            [
                                "type" => "operation",
                                "field_name" => "field_1",
                                "operator" => "neq",
                                "values" => [
                                    "1111"
                                ]
                            ],
                            [
                                "type" => "operation",
                                "field_name" => "field_2",
                                "operator" => "eq",
                                "values" => [
                                    "2222"
                                ]
                            ]
                        ]
                    ]
                ],
                "operations" => []
            ]
        );
    }

    public function testSubExpressionWithExpression()
    {
        $this->performTest(
            '(field_1.neq(1111)|field_2.eq(2222))|field_3.like(3333)',
            [
                "type" => "expression",
                "and_or" => "or",
                "nested_expressions" => [
                    [
                        "type" => "expression",
                        "and_or" => "or",
                        "nested_expressions" => [],
                        "operations" => [
                            [
                                "type" => "operation",
                                "field_name" => "field_1",
                                "operator" => "neq",
                                "values" => [
                                    "1111"
                                ]
                            ],
                            [
                                "type" => "operation",
                                "field_name" => "field_2",
                                "operator" => "eq",
                                "values" => [
                                    "2222"
                                ]
                            ]
                        ]
                    ]
                ],
                "operations" => [
                    [
                        "type" => "operation",
                        "field_name" => "field_3",
                        "operator" => "like",
                        "values" => [
                            "3333"
                        ]
                    ]
                ]
            ]
        );
    }

    public function testStringInput()
    {
        $this->performTest(
            ' field_1 . nin( \'tester\\\'s worst nightmare\' ) ',
            [
                "type" => "expression",
                "and_or" => "and",
                "nested_expressions" => [],
                "operations" => [
                    [
                        "type" => "operation",
                        "field_name" => "field_1",
                        "operator" => "nin",
                        "values" => [
                            "tester's worst nightmare"
                        ]
                    ]
                ]
            ]
        );
    }
}