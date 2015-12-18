<?php

namespace BkvFoundry\Quri\Parsed;

use BkvFoundry\Quri\Exceptions\ParseException;
use BkvFoundry\Quri\Exceptions\ValidationException;

class Expression
{
    /** @var string $type String to show the expression type, can be 'and' or 'or' */
    protected $type = "and";
    protected $nested_expressions = [];
    protected $parent;
    protected $operations = [];

    /**
     * @param string $type and|or
     * @throws ValidationException
     */
    public function setType($type)
    {
        if (!in_array($type, ['and', 'or'])) {
            throw new ValidationException("Expression can only be set to 'and' or 'or'");
        }
        $this->type = $type;
    }

    /**
     * @return string and|or
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param Expression $parent
     */
    public function setParent(Expression $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return Expression|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return Expression
     */
    public function createChildExpression()
    {
        $expression = new Expression();
        $this->nested_expressions[] = $expression;
        $expression->setParent($this);
        return $expression;
    }

    /**
     * @return Expression[]
     */
    public function getChildExpressions()
    {
        return $this->nested_expressions;
    }

    /**
     * @return Operation
     */
    public function createChildOperation()
    {
        $operation = new Operation();
        $operation->setParent($this);
        $this->operations[] = $operation;
        return $operation;
    }

    /**
     * @return array
     */
    public function getChildOperations()
    {
        return $this->operations;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'type' => 'expression',
            'and_or' => $this->getType(),
            'nested_expressions' => $this->childExpressionsToArray(),
            'operations' => $this->childOperationsToArray()
        ];
    }

    /**
     * @return array
     */
    public function childOperationsToArray()
    {
        $results = [];
        foreach ($this->getChildOperations() as $operation) {
            $results[] = $operation->toArray();
        }
        return $results;
    }

    /**
     * @return array
     */
    public function childExpressionsToArray()
    {
        $results = [];
        foreach ($this->getChildExpressions() as $expression) {
            $results[] = $expression->toArray();
        }
        return $results;
    }
}