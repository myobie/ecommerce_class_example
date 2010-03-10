<?
/*
$dir = dirname(__FILE__);
require_once "$dir/../app/requires.php";
$db = new DB();

$cart = find_or_create_cart();

$variant = Variant::first(array(
  "where" => array(
    "product_id = ? AND color_id = ? AND size_id = ?",
    $_POST["product_id"],
    $_POST["color_id"],
    $_POST["size_id"]
  )
));

$cart->add($variant);
*/


function mysqli_result_to_array($result)
{
  $arr = array();
  
  while($row = $result->fetch_assoc())
  {
    array_push($arr, $row);
  }
  
  return $arr;
}

$m = new mysqli('localhost', 'root', '', 'ecommerce_class');

session_start();

$cart_id = $m->real_escape_string($_SESSION["cart_id"]);
$cart_result = $m->query("SELECT * FROM carts WHERE id = '$cart_id'");

if ($cart_result->num_rows < 1)
{
  $m->query("INSERT INTO carts VALUES()");
  $cart_id = $m->insert_id;
  $_SESSION["cart_id"] = $cart_id;
}

$product_id = $m->real_escape_string($_POST["product_id"]);
$color_id = $m->real_escape_string($_POST["color_id"]);
$size_id = $m->real_escape_string($_POST["size_id"]);

$result = $m->query("SELECT * FROM variants 
                     WHERE product_id = '$product_id'
                     AND color_id = '$color_id'
                     AND size_id = '$size_id'");
$variants = mysqli_result_to_array($result);
$variant = $variants[0];
$variant_id = $variant["id"];

$cart_item_result = $m->query("SELECT * FROM cart_items WHERE variant_id = '$variant_id' AND cart_id = '$cart_id'");
$cart_items = mysqli_result_to_array($cart_item_result);
$cart_item = $cart_items[0];

if (!$cart_item)
{
  $m->query("INSERT INTO cart_items SET cart_id = '$cart_id', quantity = 1, variant_id = '$variant_id'");
} else {
  $current_quantity = $cart_item["quantity"];
  $current_quantity++;
  $id = $cart_item["id"];
  
  $m->query("UPDATE cart_items SET quantity = '$current_quantity' WHERE id = '$id'");
}

header('Location: /cart/');

?>