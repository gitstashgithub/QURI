<?php
use Wholemeal\QueryFilter\Parser;

ini_set('display_errors', 1);

include_once "../vendor/autoload.php";

$test_str = 'my_field_1.in(1111,2222)';

?>
<form action="index.php">
    Test input:
    <input type="text" name="test_str" style="width: 400px" value="<?= isset($_GET['test_str'])?$_GET['test_str']:'' ?>" />
    <br />
    <br />
    <input type="submit" value="Submit">
</form>
<br />
<br />
<pre>
<?


if(isset($_GET['test_str'])){
    $test_str = $_GET['test_str'];
}

$lexer = new \Wholemeal\QueryFilter\Lexer($test_str);

$results = (new Parser($lexer))->getResults();