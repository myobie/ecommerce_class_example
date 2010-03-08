<?

$dir = dirname(__FILE__);
require_once "$dir/generic/requires.php";

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

$first_cart->update(array("user_id" => 4));
print_r($first_cart->attributes());
echo $first_cart->saved() ? "Saved" : "Unsaved";
print_r($first_cart->changed_attributes());

var_dump($first_cart->save());

print_r($first_cart->changed_attributes());
print_r($first_cart->attributes());

var_dump($first_cart->destroy());

$new_cart = new Cart(array("user_id" => 8));
print_r($new_cart->changed_attributes());
var_dump($new_cart->save());
print_r($new_cart->attributes());

$new_cart2 = new Cart();
$new_cart2->update(array("user_id" => 8));
var_dump($new_cart2->save());


$cart_8 = Cart::first(array("where" => "id = 8"));
print_r($cart_8->cart_items());

echo "\n\n";

echo count($cart_8->cart_items(array("where" => "quantity > 78")));

print_r(Cart::get(8));

$item = CartItem::first();
print_r($item);
print_r($item->attributes());
print_r($item->cart());



?>