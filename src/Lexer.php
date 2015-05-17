<?php namespace Wholemeal\QueryFilter;

use Doctrine\Common\Lexer\AbstractLexer;
use Wholemeal\QueryFilter\Exceptions\InvalidCharacterException;

class Lexer extends AbstractLexer
{
    //TODO: Slim down the ones we don't need
    // All tokens that are not valid identifiers must be < 100
    const NONE                = 1;
    const INTEGER             = 2;
    const STRING              = 3;
    const INPUT_PARAMETER     = 4;
    const FLOAT               = 5;
    const CLOSE_BRACKETS      = 6;
    const OPEN_BRACKETS       = 7;
    const COMMA               = 8;
    const PIPE                = 9;
//    const DIVIDE              = 9;
    const DOT                 = 10;
//    const EQUALS              = 11;
//    const GREATER_THAN        = 12;
//    const LOWER_THAN          = 13;
//    const MINUS               = 14;
//    const MULTIPLY            = 15;
//    const NEGATE              = 16;
//    const PLUS                = 17;
//    const OPEN_CURLY_BRACE    = 18;
//    const CLOSE_CURLY_BRACE   = 19;

    // All tokens that are also identifiers should be >= 100
    const IDENTIFIER          = 100;
    const EQ                  = 101;
    const NEQ                 = 102;
    const IN                  = 103;
    const OUT                 = 104;
    const LIKE                = 105;
    const BETWEEN             = 106;
    const GT                  = 107;
    const GTE                 = 108;
    const LT                  = 109;
    const LTE                 = 110;


    /**
     * Creates a new query scanner object.
     *
     * @param string $input a query string
     */
    public function __construct($input)
    {
        $this->setInput($input);
    }

    /**
     * @inheritdoc
     */
    protected function getCatchablePatterns()
    {
        return array(
            '[a-z_\\\][a-z0-9_\:\\\]*[a-z0-9_]{1}',
            '(?:[0-9]+(?:[\.][0-9]+)*)(?:e[+-]?[0-9]+)?',
            "'(?:[^']|'')*'",
            '\?[0-9]*|:[a-z]{1}[a-z0-9_]{0,}'
        );
    }

    /**
     * @inheritdoc
     */
    protected function getNonCatchablePatterns()
    {
        return array('\s+', '(.)');
    }

    /**
     * @inheritdoc
     */
    protected function getType(&$value)
    {
        $type = self::NONE;

        // Recognizing numeric values
        if (is_numeric($value)) {
            return (strpos($value, '.') !== false || stripos($value, 'e') !== false)
                ? self::FLOAT : self::INTEGER;
        }

        if (ctype_alpha($value[0]) || $value[0] === '_') {
            $name = self::class .'::' . strtoupper($value);

            if (defined($name)) {
                $type = constant($name);

                if ($type > 100) {
                    return $type;
                }
            }

            return self::STRING;
        } else if ($value[0] === '?' || $value[0] === ':') {
            return self::INPUT_PARAMETER;
        } else {
            switch ($value) {
                case '.': return self::DOT;
                case ',': return self::COMMA;
                case '(': return self::OPEN_BRACKETS;
                case ')': return self::CLOSE_BRACKETS;
                case '|': return self::PIPE;
                case '=': // return self::EQUALS;
                case '>': // return self::GREATER_THAN;
                case '<': // return self::LOWER_THAN;
                case '+': // return self::PLUS;
                case '-': // return self::MINUS;
                case '*': // return self::MULTIPLY;
                case '/': // return self::DIVIDE;
                case '!': // return self::NEGATE;
                case '{': // return self::OPEN_CURLY_BRACE;
                case '}': // return self::CLOSE_CURLY_BRACE;
                default:
                            throw new InvalidCharacterException("Invalid character. The character '{$value}' is not supported");
                    break;
            }
        }

        return $type;
    }
}