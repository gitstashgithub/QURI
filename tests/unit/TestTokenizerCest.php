<?php

use \UnitTester;
use Wholemeal\QueryFilter\Parser;

class TestTokenizerCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    // tests
    public function tryToTest(UnitTester $I)
    {
        $test_str = 'my_field.in(1111,2222)';
//        $test_str = 'eq,select';

        $lexer = new \Wholemeal\QueryFilter\Lexer($test_str);

//        $parser =
            (new Parser($lexer))->getResults();

        die();
    }
}