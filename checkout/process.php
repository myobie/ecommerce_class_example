<?

$dir = dirname(__FILE__);
require_once "$dir/../app/requires.php";
$db = new DB();

$cart = find_or_create_cart();

$order = new Order($_POST["order"]);
$success = $order->process_checkout();

if ($success)
{
  header('Location: /checkout/thank_you.php');
} else {
  
  // TODO: show the form and an error, so they can make adjustments
  
}

?>