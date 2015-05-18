<?php

use Wholemeal\QueryFilter\ParserFactory;

ini_set('display_errors', 1);

include_once "../vendor/autoload.php";

?>
<form action="index.php">
    Test input:
    <input type="text" name="test_str" style="width: 400px" value="<?php echo isset($_GET['test_str'])?$_GET['test_str']:'' ?>" />
    <br />
    <br />
    <input type="submit" value="Submit">
</form>
<pre>
<?php

if(isset($_GET['test_str']) && $_GET['test_str']){
    $strings_to_test = [
        $_GET['test_str']
    ];
} else {
    $strings_to_test = [
        'my_field_1.in(1111,2222)',
        'field.eq(a),field.neq(b),field.in(a,b,c)', // field=a AND field!=b AND field IN(a,b,c)
        'field.eq(a)|field.neq(b)', // field=a OR field=b
        '(field.eq(a)|field.neq(b)),field.eq(c)', // (field=a OR field!=b) AND field=c
        '(field.eq(a))',
    ];
}

foreach($strings_to_test as $test_str) {
    print '-- String to test -- <br />' . $test_str . '<br /><br />-- Result -- <br />';
    print json_encode(ParserFactory::make($test_str)->getResults()->toArray(), JSON_PRETTY_PRINT);
    print '<br /><br /><br />';
}

?></pre>