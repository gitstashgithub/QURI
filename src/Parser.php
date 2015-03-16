<?php namespace Wholemeal\QueryFilter;

class Parser
{
    protected $_tokens;

    const OPEN_BRACKET = 1;
    const CLOSE_BRACKET = 2;
    const FIELD_NAME = 3;
    const CONDITIONAL = 4; // eq, neq, etc..
    const AND_OR = 5;
    const VALUE = 6;
    const VALUE_SEPARATOR = 7;
    const DOT = 8;

    public function __construct(Lexer $tokens)
    {
        $this->_tokens = $tokens;
    }

    protected function parse()
    {
        // there's 2 major contexts, expressions and values
        // values are the final value that proceeds an operator like eq followed by a bracket
        // first let's make a flag to show if we are in the value scope or an operator has just occurred
        $recent_operator = null;

        $field_names = [];

        $current_indentation = 0;

        $available_contexts = [self::OPEN_BRACKET, self::FIELD_NAME];

        while ($token = $this->_tokens->peek()) {
            // check the context


            // get the next context

        }

        if ($current_indentation != 0) {
            //todo: throw error
        }

        die();
    }

    public function getResults()
    {
        $this->parse();

        //(field_1.eq(1)|field_2.in(2,3,4)),field_3.eq(5)
        /*

        field_1.eq(1)|field_2.in(2,3,4)
        and
        field_3.eq(5)


        field_1.eq(1)
        or
        field_2.in(2,3,4)

         */
    }

    protected function validateContext(array $contexts, $type)
    {
        $available_contexts = [
            Lexer::INTEGER => [self::VALUE],
            Lexer::STRING => [self::FIELD_NAME, self::VALUE],
            Lexer::FLOAT => [self::VALUE],
            Lexer::CLOSE_BRACKETS => [self::CLOSE_BRACKET],
            Lexer::OPEN_BRACKETS => [self::OPEN_BRACKET],
            Lexer::COMMA => [self::AND_OR, self::VALUE_SEPARATOR],
            Lexer::DOT => [self::DOT],
            Lexer::EQ => [self::CONDITIONAL],
            Lexer::NEQ => [self::CONDITIONAL],
            Lexer::IN => [self::CONDITIONAL],
            Lexer::OUT => [self::CONDITIONAL],
            Lexer::LIKE => [self::CONDITIONAL],
            Lexer::BETWEEN => [self::CONDITIONAL],
            Lexer::GT => [self::CONDITIONAL],
            Lexer::GTE => [self::CONDITIONAL],
            Lexer::LT => [self::CONDITIONAL],
            Lexer::LTE => [self::CONDITIONAL],
        ];

//        foreach($contexts as $context) {
            if (!in_array($context, array_keys($available_contexts))) {
                // todo: throw exception
                // this context is totally invalid, use one of the statics in the class
            }

            if (!in_array($type, $available_contexts[$context])) {
                // todo: throw exception
                // this type is not allowed in this context
            }
//        }
    }
}