<?

$dir = dirname(__FILE__);
require "$dir/models/cart.php";

$db = new DB();

$result = $db->select(array(
  "table" => "carts",
  "where" => array("user_id = ? OR user_id = ?", 1, 3),
  "limit" => 2,
  "order" => "user_id DESC",
  "fields" => array("id", "user_id")
));

print_r($result);

$carts = Cart::all();

print_r($carts);

$first_cart = Cart::first();
$last_cart = Cart::first(array("order" => "user_id DESC"));

print_r($first_cart->attributes());
print_r($last_cart->attributes());

?>