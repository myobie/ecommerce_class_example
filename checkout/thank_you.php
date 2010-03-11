<?

$dir = dirname(__FILE__);
require_once "$dir/../app/requires.php";
$db = new DB();

$cart = find_or_create_cart();

include "../app/includes/header.php";
  
?>

<h2 class="thank-you">Thank you for your order.</h2>

<? include "../app/includes/footer.php" ?>