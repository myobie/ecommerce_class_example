<?

$dir = dirname(__FILE__);
require_once "$dir/../../app/requires.php";

$db = new DB();

$variant = Variant::create($_POST["variant"]);
$product = $variant->product();

?>
<? include "table_row.snippet.php" ?>