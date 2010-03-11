<?

$dir = dirname(__FILE__);
require_once "$dir/../app/requires.php";
$db = new DB();

$cart = find_or_create_cart();

include "../app/includes/header.php";
  
?>

<h2>Checkout</h2>

<p>Let's assume there is a form here.</p>

<form action="/checkout/process.php" method="post">
  <button type="submit">Purchase all the items in my cart</button>
</form>

<? include "../app/includes/footer.php" ?>