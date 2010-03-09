<?

$dir = dirname(__FILE__);
require_once "$dir/../app/requires.php";
$db = new DB();

$cart = find_or_create_cart();
$cart_item = CartItem::first(array(
  "where" => array("cart_id = ? AND id = ?", $cart->id(), $_GET["id"])
));

$cart_item->destroy();

header('Location: /cart/');

?>