<?php namespace Wholemeal\QueryFilter\Parsed;

class Field {
    /** @var string _operator The comparison operator  */
    protected $_operator; // eq, neq, in, etc..
    protected $_name; // my_field_x
    protected $_value; // 1234, '1234', [1,2,3,4]
}