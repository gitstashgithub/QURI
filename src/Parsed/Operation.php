<?php

namespace BkvFoundry\Quri\Parsed;

use BkvFoundry\Quri\Exceptions\ParseException;

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
    public function fieldName()
    {
        return $this->field_name;
    }

    /**
     * @param $operator
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
        $this->validate();
    }

    /**
     * @return mixed
     */
    public function operator()
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
        $this->validate();
    }

    /**
     * @return mixed
     */
    public function values()
    {
        return $this->values;
    }

    /**
     * @return mixed
     */
    public function firstValue()
    {
        return current($this->values);
    }

    /**
     * Validates the amount of values for a given operation type
     */
    public function validate()
    {
        $value_limit = 1;

        if (in_array($this->operator(), ['between'])) {
            $value_limit = 2;
        }
        if (in_array($this->operator(), ['in','nin', null])) {
            $value_limit = 9999;
        }
        if (count($this->values) > $value_limit) {
            throw new ParseException("QURI string could not be parsed. Too many values supplied for the '$this->operator' operator.");
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'type' => 'operation',
            'field_name' => $this->fieldName(),
            'operator' => $this->operator(),
            'values' => $this->values(),
        ];
    }
}