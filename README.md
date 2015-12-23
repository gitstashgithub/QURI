# QURI Spec and Lexer

[![Build Status](https://travis-ci.org/theHarvester/QURI.svg?branch=master)](https://travis-ci.org/theHarvester/QURI)

WORK IN PROGRESS


RESTful api's often propose submitting queries as a single string "q" or as a single
parameter per field you want to query. Unfortunately, this doesn't deal with a lot
of common cases that require more exact query execution.

`QURI` is a query string syntax for constructing complex query expressions. This 
package provides a lexer for parsing that query string format out into 
processable entities.

`QURI` allows you to construct complex expressions in a single string e.g. 
find me all the   boys that like apples or oranges or that have a dog:
```
?q=gender.eq("boy"),(fruits.in("apples","oranges")|pets.in("dog"))
```

## Structure

The generic structure of a request is built up with a field name, an operator and a value.

`field_name.operator(value)`

For example - find where name starts with "John":

`name.like("John%")`

These expressions can be strung together with an 'and' `,` or an 'or' `|`. 

For example - find where date of birth is greater than Jan 1st 2000 and less than Dec 31st 2000:

`dob.gt("2000-01-01"),dob.lt("2000-12-31")`

The last example can be rewritten with a between for clarity:

`dob.between("2000-01-01", "2000-12-31")`

Closures can be used to group operations.

For example - find where Id equals 1 or 2 and the name equals "John":

`(id.eq(1)|id.eq(2)),name.eq("John")`

This can be rewritten with an `in` for clarity:

`id.in(1,2),name.eq("John")`

### Operations:
* `eq(mixed $value)` - Test that the field is equal to a value
* `neq(mixed $value)` - Test that the field is not equal to a value
* `gt(mixed $value)` - Test that the field's value is greater than the value provided
* `gte(mixed $value)` - Test that the field's value is greater than or equal to the value provided
* `lt(mixed $value)` - Test that the field's value is less than the value provided
* `lte(mixed $value)` - Test that the field's value is less than or equal to the value provided
* `in(mixed $value1, ..., mixed $valueN)` - Test that the field's value is in the list of values provided (comma delimited)
* `nin(mixed $value1, ..., mixed $valueN)` - Test that the field's value is not any of the values provided
* `like(string $string)` - Test that the field is "like" a value. Use of "%" represents wildcards.
* `between(mixed $min, mixed $max)` - Test that the field's value is between the first and second paramters.

### And / Or:
If multiple fields are being tested, its necessary to sepcify whether they should
be evaluated as "and" or "or" tests.
* The comma represents "and" e.g. `field1.eq(3),field2.eq(4)`
* The pipe represents "or" e.g. `field1.eq(3)|field1.eq(4)`

### Closures:
Wrapping `()` brackets can be used to affect order of operations for queries.

### Escaping values:
* Quotes (single or double) `" or '` can be used to escape strings e.g. `field.like("apple=orang%")`
* Escaping qoutes: If you need to use quotes, you can use the `\` character to escape the quote character

### Complex field names:
Quoting strings also works for field names which allows for more flexibility. For example `'field_1.field_2'` will result with `field_1.field_2` in the field name, this can be useful for related table searches.

## Using the parser
To initialize the Parser, we first need to create a Lexer and pass it in.

```
$queryStr = $_GET['q'];
$lexer = new Lexer($queryStr);
$parser = new Parser($lexer);
$expression = $parser->parse();
```

Altertively this can be done through a helper method on the Parser class.

```
$expression = Parser::initAndParse($queryStr);
```

### Using the expression object

After a string has been parsed and the expression object has been returned. An expression object can retrieve the operations and further nested expressions.

```
public function applyExpression($builder, Expression $expression)
{
    // Determine if the context is an 'and' or an 'or'
    $andOr = $expression->getType(); 
    //
    // If there are nested expressions, create a where closure and recurse
    //
    // This ensures, (id.eq(1)|id.eq(2)),name.eq("John")
    // comes out as, where ( id = 1 or id = 2 ) and name = "John"
    // in the SQL.
    if ($nestedExpressions = $expression->nestedExpressions()) {
        // Start a closure so nested expressions are applied to the sql in a nested fashion
        $builder->where(function ($builder) use ($nestedExpressions) {
            foreach ($nestedExpressions as $nestedExpression) {
                // Recurse deeper and apply child expressions to the builder
                $this->applyExpression($builder, $nestedExpression);
            }
        });
    }
    // If there are any operations at this level, apply them to the current builder context
    if ($operations = $expression->operations()) {
        foreach ($operations as $operation) {
            // Mutate the builder internernally with the operation object and the andOr
            $this->applyOperation($builder, $operation, $andOr);
        }
    }
}
```