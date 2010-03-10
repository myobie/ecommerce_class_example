<?

$dir = dirname(__FILE__);
require_once "$dir/../app/requires.php";
$db = new DB();

$cart = find_or_create_cart();

include "../app/includes/header.php";

?>

<h2>Thank you for your order.</h2>
<p>You will be notified by email when your item is shipped.</p>
    
<? include "../app/includes/footer.php" ?>