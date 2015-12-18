<?php namespace BkvFoundry\Quri\Parsed;

/**
 * Class Operation Stores a field name and values for that belong to a single operation
 * @package BkvFoundry\Quri\Parsed
 */
class Operation {
    protected $_field_name;
    protected $_operator;
    protected $_values;
    protected $_parent;

    public function setFieldName($field_name)
    {
        $this->_field_name = $field_name;
    }

    public function getFieldName()
    {
        return $this->_field_name;
    }

    public function setOperator($operator)
    {
        $this->_operator = $operator;
    }

    public function getOperator()
    {
        return $this->_operator;
    }

    public function setParent($parent)
    {
        $this->_parent = $parent;
    }

    public function getParent()
    {
        return $this->_parent;
    }

    public function addValue($value)
    {
        $this->_values[] = $value;
    }

    public function getValues()
    {
        return $this->_values;
    }

    public function toArray()
    {
        return [
            'type' => 'operation',
            'field_name' => $this->getFieldName(),
            'operator' => $this->getOperator(),
            'values' => $this->getValues(),
        ];
    }
}