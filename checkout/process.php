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

$result = $m->query("SELECT ci.id, ci.quantity, ci.variant_id, 
                            v.price, p.default_price, v.color_id, v.size_id
                     FROM   cart_items ci, variants v, products p
                     WHERE  ci.variant_id = v.id
                     AND    v.product_id = p.id
                     ");

$cart_items = mysqli_result_to_array($result);

foreach ($cart_items as $cart_item)
{
  if (gettype($cart_item["price"]) == "NULL")
  {
    $cart_item["price"] = $cart_item["default_price"]
  }
}

print_r($cart_items);

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

if ($result->success)
{
  
  $status = $result->transaction->status;
  $transaction_key = $result->transaction->id;
  
  $m->query("INSERT INTO orders SET total = '$cart_total', 
                                    status = '$status',
                                    transaction_key = '$transaction_key'");
  
  $order_id = $m->insert_id;
  
  foreach ($cart_items as $cart_item)
  {
    $quantity = $cart_item["quantity"];
    $price = $cart_item["price"];
    $name = $cart_item["name"];
    $color_id = $cart_item["color_id"];
    $size_id = $cart_item["size_id"];
    $subtotal = $cart_item["price"] * $cart_item["quantity"];
    $variant_id = $cart_item["variant_id"];
    
    $m->query("INSERT INTO order_items
               SET order_id = '$order_id'
                   quantity = '$quantity'
                   price_per_variant = '$price'
                   name = '$name'
                   color_id = '$color_id'
                   size_id = '$size_id'
                   subtotal = '$subtotal'
                   variant_id = '$variant_id'
               ");
  }
  
  $m->query("DELETE FROM cart_items WHERE cart_id = '$cart_id'");
  $m->query("DELETE FROM carts WHERE id = '$cart_id'");
  
  // header('Location: /checkout/thank_you.php');
} else {
  
  // --- There was an error! ---
  
  $status = $result->transaction->status;
  $transaction_key = $result->transaction->id;
  
  $m->query("INSERT INTO orders SET total = '$cart_total', 
                                    status = '$status',
                                    error_message = 'error'");
  
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