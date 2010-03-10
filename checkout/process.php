<?

$dir = dirname(__FILE__);
require_once "$dir/../app/requires.php";
$db = new DB();

$cart = find_or_create_cart();

$order = new Order($_POST["order"]);
$order->update(array(
  "total" => $cart->final_total() * 100,
  "shipping_total" => $cart->shipping() * 100,
  "tax_total" => $cart->tax() * 100
));
$success = $order->process_checkout();

if ($success)
{
  $order->copy($cart);
  $cart->destroy();
  header('Location: /checkout/thank_you.php');
} else {
  
  // TODO: show the form and an error, so they can make adjustments
  // echo "There was an error: " . $order->g("error_message");
  
  include "../app/includes/header.php";
  
?>

<h2 class="error">Checkout was not successful</h2>

<div id="errors">
  <ul>
    <? foreach ($order->errors as $error) { ?>
      <li><?= $error ?></li>
    <? } ?>
  </ul>
</div>

<? include "form.snippet.php" ?>

<?

  include "../app/includes/footer.php";
  
} // end of else

?>