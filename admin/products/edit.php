<?

$dir = dirname(__FILE__);
require_once "$dir/../../generic/requires.php";

$db = new DB();

$product = Product::get($_GET["id"]);

?><!DOCTYPE html>
<html>
  <head>
    <title>Admin - Products</title>
  </head>
  <body>
    
    <h1>Editing Product <?= $product->id() ?>: <?= $product->g("name") ?></h1>
    
    
    
  </body>
</html>