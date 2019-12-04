<?php

namespace BkvFoundry\Quri;

use BkvFoundry\Quri\Exceptions\ParseException;
use BkvFoundry\Quri\Exceptions\ValidationException;
use BkvFoundry\Quri\Parsed\Expression;
use BkvFoundry\Quri\Parsed\Operation;

class Parser
{
    protected $tokens;

    const OPEN_BRACKET = 1;
    const CLOSE_BRACKET = 2;
    const FIELD_NAME = 3;
    const CONDITIONAL = 4; // eq, neq, etc..
    const AND_OR = 5; // , |
    const VALUE = 6;
    const VALUE_SEPARATOR = 7;
    const DOT = 8;
    const NEW_EXPRESSION = 9; // an open bracket but when a new expression is about to occur

    /**
     * @param Lexer $tokens
     */
    public function __construct(Lexer $tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * Quickly initialize the Lexer and parse the string
     *
     * @param $string
     * @return Expression
     * @throws ParseException
     * @throws ValidationException
     */
    public static function initAndParse($string)
    {
        $new = new static(new Lexer($string));
        return $new->parse();
    }

    /**
     * Parses the Lexer which is passed into the constructor and returns an Expression object
     *
     * There's two contexts of object in this library. An Expression and a Value.
     * An expression is the context of the query information where the value is simply values in that context
     *
     * Here's an example. field_1.eq(10)
     * The expression is the field name (field_1), the fact that we're in an "and" context and the fact that
     * we are looking for an exact match (eq). Where as the value is simply the value 10.
     *
     * @return Expression
     * @throws ParseException
     * @throws ValidationException
     */
    public function parse()
    {
        // first let's make a flag to show if we are in the value scope or an operator has just occurred
        $current_indentation = 0;
        $previous_context = null;

        $base_expression = new Expression();
        $current_expression = $base_expression;

        while ($token = $this->tokens->peek()) {
            // check the context
            try {
                $context = $this->getContext($token['type'], $previous_context);
            } catch (ValidationException $e) {
                throw new ValidationException("QURI string could not be parsed. The unexpected input of '{$token['value']}' was found in your query at character {$token['position']}.");
            }

            $current_expression = $this->applyContextToExp($current_expression, $context, $token['value']);
            $current_indentation = $this->getIndentation($current_indentation, $context);
            $previous_context = $context;
        }

        if (isset($context) && $context != self::CLOSE_BRACKET) {
            throw new ParseException("Malformed input, error near '" . $token['value'] . "'");
        }
        if ($current_indentation != 0) {
            throw new ParseException("Incorrect indentation");
        }

        return $base_expression;
    }

    /**
     * Returns the context (a const in this class) from a token type (a Lexer const)
     *
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

        if (!count($intersected)) {
            throw new ValidationException("");
        }

        return $intersected[0];
    }

    /**
     * Gets an array of possible contexts for a given token type
     *
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
            Lexer::OPEN_BRACKETS => [self::OPEN_BRACKET, self::NEW_EXPRESSION],
            Lexer::COMMA => [self::AND_OR, self::VALUE_SEPARATOR],
            Lexer::PIPE => [self::AND_OR],
            Lexer::DOT => [self::DOT],
            Lexer::EQ => [self::CONDITIONAL],
            Lexer::NEQ => [self::CONDITIONAL],
            Lexer::IN => [self::CONDITIONAL],
            Lexer::NIN => [self::CONDITIONAL],
            Lexer::OUT => [self::CONDITIONAL],
            Lexer::LIKE => [self::CONDITIONAL],
            Lexer::BETWEEN => [self::CONDITIONAL],
            Lexer::GT => [self::CONDITIONAL],
            Lexer::GTE => [self::CONDITIONAL],
            Lexer::LT => [self::CONDITIONAL],
            Lexer::LTE => [self::CONDITIONAL],
            Lexer::ISNULL => [self::CONDITIONAL],
            Lexer::ISNOTNULL => [self::CONDITIONAL],
        ];
        if (array_key_exists($token_type, $available_contexts)) {
            return $available_contexts[$token_type];
        }
        //todo: make better exception
        throw new \Exception("Token type is invalid");
    }

    /**
     * Returns an array of allowed contexts
     *
     * @param int|null $previous_context The last context type which is a const in this class. If null it's assumed to be the first token
     * @return array
     * @throws \Exception
     */
    public function getAllowedContexts($previous_context)
    {
        $allowed_context_map = [
            null => [self::NEW_EXPRESSION, self::FIELD_NAME],
            self::OPEN_BRACKET => [self::VALUE],
            self::NEW_EXPRESSION => [self::FIELD_NAME],
            self::CLOSE_BRACKET => [self::AND_OR, self::CLOSE_BRACKET],
            self::FIELD_NAME => [self::DOT],
            self::CONDITIONAL => [self::OPEN_BRACKET],
            self::AND_OR => [self::FIELD_NAME, self::NEW_EXPRESSION],
            self::VALUE => [self::VALUE_SEPARATOR, self::CLOSE_BRACKET],
            self::VALUE_SEPARATOR => [self::VALUE],
            self::DOT => [self::CONDITIONAL],
        ];

        if (array_key_exists($previous_context, $allowed_context_map)) {
            return $allowed_context_map[$previous_context];
        }

        throw new ValidationException("");
    }

    /**
     * Gets the indentation from the context.
     *
     * @param int $current_indentation
     * @param int $context A const on this classs
     * @return int
     */
    public function getIndentation($current_indentation, $context)
    {
        if (in_array($context, [self::OPEN_BRACKET, self::NEW_EXPRESSION])) {
            $current_indentation++;
        } else if ($context == self::CLOSE_BRACKET) {
            $current_indentation--;
        }

        return $current_indentation;
    }

    /**
     * Applies the context to the expression
     *
     * @param Operation|Expression $current_expression
     * @param int $context A context from the constants on this class
     * @param mixed $value A value to store for the context
     * @return mixed
     */
    public function applyContextToExp($current_expression, $context, $value)
    {
        switch ($context) {
            case self::FIELD_NAME:
                $current_expression = $current_expression->createOperation();
                $current_expression->setFieldName($value);
                break;
            case self::CONDITIONAL:
                $current_expression->setOperator($value);
                break;
            case self::AND_OR:
                if ($value == ',')
                    $current_expression->setType("and");
                else
                    $current_expression->setType("or");
                break;
            case self::VALUE:
                $current_expression->addValue($value);
                break;
            case self::OPEN_BRACKET:
                // open bracket to set values
                break;
            case self::NEW_EXPRESSION:
                $current_expression = $current_expression->createNestedExpression();
                break;
            case self::CLOSE_BRACKET:
                $current_expression = $current_expression->getParent();
                break;
            case self::VALUE_SEPARATOR:
                // do nothing
                break;
            case self::DOT:
                // do nothing
                break;
        }

        return $current_expression;
    }
}