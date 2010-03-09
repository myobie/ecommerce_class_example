<?

$dir = dirname(__FILE__);
require_once "$dir/../generic/requires.php";
$db = new DB();

$cart = find_or_create_cart();

$variant = Variant::first(array(
  "where" => array(
    "product_id = ? AND color_id = ? AND size_id = ?",
    $_POST["product_id"],
    $_POST["color_id"],
    $_POST["size_id"]
  )
));

$cart->add($variant);

header('Location: /cart/');

?>