<?php

namespace BkvFoundry\Quri\Parsed;

use BkvFoundry\Quri\Exceptions\ValidationException;

class Expression
{
    /** @var string $type String to show the expression type, can be 'and' or 'or' */
    protected $type = "and";
    /** @var Expression[] $nested_expressions An array of nexted expressions  */
    protected $nested_expressions = [];
    /** @var Expression|null A parent expression used to traverse the expression tree */
    protected $parent;
    /** @var Operation[] An array of operations attached to this expression */
    protected $operations = [];

    /**
     * @param string $type and|or
     * @throws ValidationException
     */
    public function setType($type)
    {
        if (!in_array($type, ['and', 'or'])) {
            throw new ValidationException("QURI Expression can only be set to 'and' or 'or'");
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
    public function createNestedExpression()
    {
        $expression = new Expression();
        $this->nested_expressions[] = $expression;
        $expression->setParent($this);
        return $expression;
    }

    /**
     * @return Expression[]
     */
    public function nestedExpressions()
    {
        return $this->nested_expressions;
    }

    /**
     * @return Operation
     */
    public function createOperation()
    {
        $operation = new Operation();
        $operation->setParent($this);
        $this->operations[] = $operation;
        return $operation;
    }

    /**
     * @return Operation[]
     */
    public function operations()
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
            'nested_expressions' => $this->nestedExpressionsToArray(),
            'operations' => $this->operationsToArray()
        ];
    }

    /**
     * @return array
     */
    public function operationsToArray()
    {
        $results = [];
        foreach ($this->operations() as $operation) {
            $results[] = $operation->toArray();
        }
        return $results;
    }

    /**
     * @return array
     */
    public function nestedExpressionsToArray()
    {
        $results = [];
        foreach ($this->nestedExpressions() as $expression) {
            $results[] = $expression->toArray();
        }
        return $results;
    }
}