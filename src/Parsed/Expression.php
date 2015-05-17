<?php namespace Wholemeal\QueryFilter\Parsed;

use Wholemeal\QueryFilter\Exceptions\ParseException;
use Wholemeal\QueryFilter\Exceptions\ValidationException;

class Expression
{
    /** @var string $_type String to show the expression type, can be 'and' or 'or' */
    protected $_type = "and";
    protected $_nested_expressions = [];
    protected $_parent;
    protected $_operations = [];

    public function setType($type)
    {
        if (!in_array($type, ['and', 'or'])) {
            throw new ValidationException("Expression can only be set to 'and' or 'or'");
        }
        $this->_type = $type;
    }

    public function getType()
    {
        return $this->_type;
    }

    public function setParent(Expression $parent)
    {
        $this->_parent = $parent;
    }

    public function getParent()
    {
        return $this->_parent;
    }

    public function createChildExpression()
    {
        $expression = new Expression();
        $this->_nested_expressions[] = $expression;
        $expression->setParent($this);
        return $expression;
    }

    public function getChildExpressions()
    {
        return $this->_nested_expressions;
    }

    public function createChildOperation()
    {
        $operation = new Operation();
        $operation->setParent($this);
        $this->_operations[] = $operation;
        return $operation;
    }

    public function getChildOperations()
    {
        return $this->_operations;
    }

    public function toArray()
    {
        return [
            'type' => 'expression',
            'and_or' => $this->getType(),
            'nested_expressions' => $this->childExpressionsToArray(),
            'operations' => $this->childOperationsToArray()
        ];
    }

    public function childOperationsToArray()
    {
        $results = [];
        foreach($this->getChildOperations() as $operation)
        {
            $results[] = $operation->toArray();
        }
        return $results;
    }

    public function childExpressionsToArray()
    {
        $results = [];
        foreach($this->getChildExpressions() as $expression)
        {
            $results[] = $expression->toArray();
        }
        return $results;
    }
}