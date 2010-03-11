<?

$dir = dirname(__FILE__);
require_once "$dir/../../app/requires.php";

$db = new DB();

$variant = Variant::get($_POST["id"]);
$variant->destroy();

?>