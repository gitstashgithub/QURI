# QURI Spec and Lexer

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

### Operations:
* `eq(mixed $value)` - Test that the field is equal to a value
* `neq(mixed $value)` - Test that the field is not equal to a value
* `gt(mixed $value)` - Test that the field's value is greater than the value provided
* `gte(mixed $value)` - Test that the field's value is greater than or equal to the value provided
* `lt(mixed $value)` - Test that the field's value is less than the value provided
* `lte(mixed $value)` - Test that the field's value is less than or equal to the value provided
* `in(mixed $value1, ..., mixed $valueN)` - Test that the field's value is in the list of values provided (comma delimited)
* `notin(mixed $value1, ..., mixed $valueN)` - Test that the field's value is not any of the values provided
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
* Escaping qoutes: If you need to use quotes, you can use the `\\` character to escape the quote character