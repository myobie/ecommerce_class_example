<?

$dir = dirname(__FILE__);
require_once "$dir/../app/requires.php";
$db = new DB();

$cart = find_or_create_cart();
$cart_items = $cart->cart_items();

$order = new Order();

include "../app/includes/header.php";

?>

<? if (count($cart_items) > 0) { ?>
  
  <h2>Checkout</h2>
  
  <? include "form.snippet.php" ?>
  
<? } else { ?>
  
  <h2>Your cart is currently empty.</h2>
  <p>Shop around and add some items when you are ready.</p>
  
<? } ?>

<? include "../app/includes/footer.php"; ?>