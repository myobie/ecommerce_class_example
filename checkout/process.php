<?

function mysqli_result_to_array($result)
{
  $arr = array();
  
  while($row = $result->fetch_assoc())
  {
    array_push($arr, $row);
  }
  
  return $arr;
}

$m = new mysqli('localhost', 'root', '', 'ecommerce_class');

session_start();

$cart_id = $m->real_escape_string($_SESSION["cart_id"]);
$cart_result = $m->query("SELECT * FROM carts WHERE id = '$cart_id'");

if ($cart_result->num_rows < 1)
{
  $m->query("INSERT INTO carts VALUES()");
  $cart_id = $m->insert_id;
  $_SESSION["cart_id"] = $cart_id;
}

$cart_items = mysqli_result_to_array($result);

array_walk($cart_items, function (&$cart_item, $key) {
  global $m;
  if (gettype($cart_item["price"]) == "NULL")
  {
    $product_id = $cart_item["product_id"];
    $result = $m->query("SELECT id, default_price FROM products WHERE id = '$product_id'");
    $temp_products = mysqli_result_to_array($result);
    $temp_product = $temp_products[0];
    $cart_item["price"] = $temp_product["default_price"];
  }
});

$total_func = create_function('$sum, $row', 'return $sum += ($row["quantity"] * $row["price"]);');
$cart_total = array_reduce($cart_items, $total_func, 0);

// --- Braintree stuff ---

set_include_path(
  get_include_path() . PATH_SEPARATOR .
  realpath(dirname(__FILE__)) . '/ZendFramework-1.10.2-minimal/library'
);

require_once "../braintree/Braintree.php";

Braintree_Configuration::environment('sandbox');
Braintree_Configuration::merchantId('your_merchant_id');
Braintree_Configuration::publicKey('your_public_key');
Braintree_Configuration::privateKey('your_private_key');

$result = Braintree_Transaction::sale(array(
    'amount' => number_format($cart_total / 100, 2, ".", ""),
    'creditCard' => array(
        'number' => '5105105105105100',
        'expirationDate' => '05/12',
        'cvv' => '123'
    )
));

echo 'Transaction ID: ' . $result->transaction->id;
echo 'Status: ' . $result->transaction->status;



if ($result->success)
{
  
  /*
    - create an order record
    - create an order item for each cart item
    - delete all the cart items
    - delete the cart
  */ 
  
  // header('Location: /checkout/thank_you.php');
} else {
  
  // --- There was an error! ---
  
  include "../app/includes/header.php";
  
?>

<h2 class="error">Checkout was not successful</h2>

<pre>
<? print_r($result); ?>
</pre>

<?

  include "../app/includes/footer.php";
  
} // end of else

?>