<?php namespace Wholemeal\QueryFilter;

use Wholemeal\QueryFilter\Exceptions\ValidationException;

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

        $current_indentation = 0;
        $previous_context = null;


        while ($token = $this->_tokens->peek()) {
            // check the context

            try {
                $context = $this->getContext($token['type'], $previous_context, $token['value']);
            } catch (ValidationException $e){
                throw new ValidationException("Parse error. Invalid input around '{$token['value']}'");
            }

            $current_indentation = $this->getIndentation($current_indentation, $context);

            //todo: use the context to build either an expression or field class

            $previous_context = $context;
        }

        // todo: check that it ended on a bracket too
        if ($current_indentation != 0) {
            //todo: throw error
        }

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

    /**
     * Returns the context (a const in this class) from a token type (a Lexer const)
     * @param int $token_type A token type which is a const on the Lexer class
     * @param int|null $previous_context The last context type which is a const in this class. If null it's assumed to be the first token
     * @return int Returns the current context, will throw exception if it fails
     * @throws ValidationException
     */
    public function getContext($token_type, $previous_context)
    {
        $possible_contexts = $this->getPossibleContext($token_type);
        $allowed_contexts = $this->getAllowedContexts($previous_context);
        $intersected = array_values(array_intersect($possible_contexts, $allowed_contexts));

        if(!count($intersected)){
            throw new ValidationException("");
        }

        return $intersected[0];
    }

    /**
     * Gets an array of possible contexts
     * @param int $token_type A token type which is a const on the Lexer class
     * @return
     * @throws \Exception
     */
    public function getPossibleContext($token_type)
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
        if(array_key_exists($token_type, $available_contexts)) {
            return $available_contexts[$token_type];
        }
        //todo: make better exception
        throw new \Exception("Token type is invalid");
    }

    /**
     * Returns an array of allowed contexts
     * @param int|null $previous_context The last context type which is a const in this class. If null it's assumed to be the first token
     * @return array
     * @throws \Exception
     */
    public function getAllowedContexts($previous_context)
    {
        $allowed_context_map = [
            null => [self::OPEN_BRACKET, self::FIELD_NAME],
            self::OPEN_BRACKET => [self::FIELD_NAME, self::VALUE],
            self::CLOSE_BRACKET => [self::AND_OR],
            self::FIELD_NAME => [self::DOT],
            self::CONDITIONAL => [self::OPEN_BRACKET],
            self::AND_OR => [self::FIELD_NAME, self::OPEN_BRACKET],
            self::VALUE => [self::VALUE_SEPARATOR, self::CLOSE_BRACKET],
            self::VALUE_SEPARATOR => [self::VALUE],
            self::DOT => [self::CONDITIONAL],
        ];

        if(array_key_exists($previous_context, $allowed_context_map)){
            return $allowed_context_map[$previous_context];
        }

        throw new ValidationException("");
    }

    /**
     * Gets the indentation from the context
     * @param int $current_indentation
     * @param int $context A const on this classs
     * @return int
     */
    public function getIndentation($current_indentation, $context)
    {
        if($context == self::OPEN_BRACKET){
            $current_indentation++;
        } else  if($context == self::CLOSE_BRACKET){
            $current_indentation--;
        }

        return $current_indentation;
    }
}