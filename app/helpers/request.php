<?

$dir = dirname(__FILE__);
require_once "$dir/../models/cart.php";

function find_or_create_cart()
{
  session_start();
  
  $cart = Cart::get($_SESSION["cart_id"]);
  
  if (!$cart) {
    $cart = Cart::create();
    $_SESSION["cart_id"] = $cart->id();
  }
  
  return $cart;
}

?>