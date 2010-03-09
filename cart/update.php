<?

$dir = dirname(__FILE__);
require_once "$dir/../app/requires.php";
$db = new DB();

$cart = find_or_create_cart();
$cart_items = $cart->cart_items();

foreach ($cart_items as $cart_item) {
  $cart_item->update(array(
    "quantity" => $_POST["cart_items"][$cart_item->id()]["quantity"]
  ));
  $cart_item->save();
}

header('Location: /cart/');

?>