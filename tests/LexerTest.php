<?php

use BkvFoundry\QueryFilter\Lexer;

class LexerTest extends PHPUnit_Framework_TestCase
{
    protected function mintToken($value, $type, $position)
    {
        return [
            'value' => $value,
            'type' => $type,
            'position' => $position,
        ];
    }

    public function testPlainString()
    {
        $lexer = new Lexer('field_1');
        $this->assertEquals($this->mintToken('field_1', Lexer::STRING, 0), $lexer->peek());
    }

    public function testNone()
    {
        $lexer = new Lexer('');
        $this->assertNull($lexer->peek());
    }

    public function testInteger()
    {
        $lexer = new Lexer('101');
        $this->assertEquals($this->mintToken('101', Lexer::INTEGER, 0), $lexer->peek());
    }

    public function testInputParameter()
    {
        $lexer = new Lexer('?');
        $this->assertEquals($this->mintToken('?', Lexer::INPUT_PARAMETER, 0), $lexer->peek());
    }

    public function testFloat()
    {
        $lexer = new Lexer('10.101');
        $this->assertEquals($this->mintToken('10.101', Lexer::FLOAT, 0), $lexer->peek());
    }

    public function testCloseBrackets()
    {
        $lexer = new Lexer(')');
        $this->assertEquals($this->mintToken(')', Lexer::CLOSE_BRACKETS, 0), $lexer->peek());
    }

    public function testOpenBrackets()
    {
        $lexer = new Lexer('(');
        $this->assertEquals($this->mintToken('(', Lexer::OPEN_BRACKETS, 0), $lexer->peek());
    }

    public function testComma()
    {
        $lexer = new Lexer(',');
        $this->assertEquals($this->mintToken(',', Lexer::COMMA, 0), $lexer->peek());
    }

    public function testPipe()
    {
        $lexer = new Lexer('|');
        $this->assertEquals($this->mintToken('|', Lexer::PIPE, 0), $lexer->peek());
    }

    public function testDot()
    {
        $lexer = new Lexer('.');
        $this->assertEquals($this->mintToken('.', Lexer::DOT, 0), $lexer->peek());
    }

    public function testNin()
    {
        $lexer = new Lexer('nin');
        $this->assertEquals($this->mintToken('nin', Lexer::NIN, 0), $lexer->peek());
    }

    public function testQuotedString()
    {
        $lexer = new Lexer("'te\'st'");
        $this->assertEquals($this->mintToken("te'st", Lexer::STRING, 0), $lexer->peek());
    }

    public function testQuotedStringWithSpaces()
    {
        $lexer = new Lexer("'test test'");
        $this->assertEquals($this->mintToken('test test', Lexer::STRING, 0), $lexer->peek());
    }

    public function testAcceptableString()
    {
        $lexer = new Lexer('field_1.in(test)');
        $this->assertEquals($this->mintToken('field_1', Lexer::STRING, 0), $lexer->peek());
        $this->assertEquals($this->mintToken('.', Lexer::DOT, 7), $lexer->peek());
        $this->assertEquals($this->mintToken('in', Lexer::IN, 8), $lexer->peek());
        $this->assertEquals($this->mintToken('(', Lexer::OPEN_BRACKETS, 10), $lexer->peek());
        $this->assertEquals($this->mintToken('test', Lexer::STRING, 11), $lexer->peek());
        $this->assertEquals($this->mintToken(')', Lexer::CLOSE_BRACKETS, 15), $lexer->peek());
    }

    public function testAcceptableStringWithSpaces()
    {
        $lexer = new Lexer('field_1.in(\'test test\')');
        $this->assertEquals($this->mintToken('field_1', Lexer::STRING, 0), $lexer->peek());
        $this->assertEquals($this->mintToken('.', Lexer::DOT, 7), $lexer->peek());
        $this->assertEquals($this->mintToken('in', Lexer::IN, 8), $lexer->peek());
        $this->assertEquals($this->mintToken('(', Lexer::OPEN_BRACKETS, 10), $lexer->peek());
        $this->assertEquals($this->mintToken('test test', Lexer::STRING, 11), $lexer->peek());
        $this->assertEquals($this->mintToken(')', Lexer::CLOSE_BRACKETS, 22), $lexer->peek());
    }
}
