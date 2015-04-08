<?php
use Wholemeal\QueryFilter\Parser;

ini_set('display_errors', 1);

include_once "../vendor/autoload.php";


$test_str = 'my_field_1.in(1111,2222)';

$lexer = new \Wholemeal\QueryFilter\Lexer($test_str);

(new Parser($lexer))->getResults();