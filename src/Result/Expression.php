<?php namespace Wholemeal\QueryFilter\Result;

class Expression {
    /** @var boolean _is_or Shows whether it's an or, or an and */
    protected $_is_or;
    /** @var Field[] _fields The fields that sit beneath this expression. */
    protected $_fields;


}