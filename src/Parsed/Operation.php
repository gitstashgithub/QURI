<?php

namespace BkvFoundry\Quri\Parsed;

/**
 * Class Operation
 *
 * Stores a field name and values for that belong to a single operation
 *
 * @package BkvFoundry\Quri\Parsed
 */
class Operation
{
    protected $field_name;
    protected $operator;
    protected $values;
    protected $parent;

    /**
     * @param $field_name
     */
    public function setFieldName($field_name)
    {
        $this->field_name = $field_name;
    }

    /**
     * @return mixed
     */
    public function getFieldName()
    {
        return $this->field_name;
    }

    /**
     * @param $operator
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
    }

    /**
     * @return mixed
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param $value
     */
    public function addValue($value)
    {
        $this->values[] = $value;
    }

    /**
     * @return mixed
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @return array
     */
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