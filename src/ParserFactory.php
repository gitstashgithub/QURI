<?php namespace BkvFoundry\Quri;

class ParserFactory
{
    public static function make($str)
    {
        $lexer = new Lexer($str);
        $parser = new Parser($lexer);
        return $parser;
    }
}