<?
/*
$dir = dirname(__FILE__);
require_once "$dir/../app/requires.php";
$db = new DB();

$cart = find_or_create_cart();
$cart_items = $cart->cart_items();
*/

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

$result = $m->query("SELECT cart_items.id, cart_items.quantity, 
                            variants.price, variants.product_id 
                     FROM cart_items, variants
                     WHERE cart_items.cart_id = '$cart_id' 
                     AND variants.id = cart_items.variant_id");
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

$quantity_func = create_function('$sum, $row', 'return $sum += $row["quantity"];');
$cart_quantity = array_reduce($cart_items, $quantity_func, 0);



include "../app/includes/header.php";

?>

<? if (count($cart_items) > 0) { ?>
  
  <h2>Your Cart</h2>
  
  <form action="/cart/update.php" method="post" id="cart">
    <table cellpadding="0" cellspacing="0" border="0">
      <thead>
        <tr>
          <th>Photo</th>
          <th>Details</th>
          <th>Quantity</th>
          <th>Price per unit</th>
          <th>Subtotal</th>
          <th>Remove</th>
        </tr>
      </thead>
      <tbody>
        <? foreach ($cart_items as $cart_item) { ?>
          <tr id="cart_item_<?= $cart_item["id"] ?>">
          
            <td class="photo">
              <img src="<? //$product->photo($color->id())->url("small") ?>" width="30" height="30">
            </td>
            <td class="description"><? //$variant->description() ?></td>
            <td class="quantity">
              <input type="text" 
                     name="cart_items[<?= $cart_item["id"] ?>][quantity]" 
                     value="<?= $cart_item["quantity"] ?>">
            </td>
            <td>$<?= number_format($cart_item["price"] / 100, 2, ".", ",") ?></td>
            <td><?= number_format($cart_item["price"] / 100 * $cart_item["quantity"], 2, ".", ",") ?></td>
            <td><a href="/cart/remove.php?id=<?= $cart_item["id"] ?>">Remove</a></td>
          
          </tr>
        <? } ?>
      </tbody>
    </table>
  
    <p><button type="submit">Update cart</button></p>
    
    <p class="total">Total: $<?= number_format($cart_total / 100, 2, ".", ",") ?></p>
  </form>

<? } else { ?>
  
  <h2>Your cart is currently empty.</h2>
  <p>Shop around and add some items when you are ready.</p>
  
<? } ?>

<? include "../app/includes/footer.php"; ?>