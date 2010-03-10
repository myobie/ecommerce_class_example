<?

$dir = dirname(__FILE__);
require_once "$dir/../app/requires.php";

$db = new DB();

$products = Product::all(array("order" => "name ASC"));

include "../app/includes/admin/header.php";

?>
    
<h1>Dashboard</h1>
    
<? include "../app/includes/admin/footer.php" ?>