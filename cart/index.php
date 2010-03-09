<?

$dir = dirname(__FILE__);
require_once "$dir/../generic/requires.php";
$db = new DB();

$cart = find_or_create_cart();
$cart_items = $cart->cart_items();

?><!DOCTYPE html>
<html>
  <head>
    <title>Cart</title>
  </head>
  <body>
    
    <h1>Cart</h1>
    
    <form action="/cart/update.php" method="post">
      <table>
        <thead>
          <tr>
            <th>Details</th>
            <th>Quantity</th>
            <th>Price per unit</th>
            <th>Subtotal</th>
            <th>Remove</th>
          </tr>
        </thead>
        <tbody>
          <? foreach ($cart_items as $cart_item) { ?>
            <? $variant = $cart_item->variant() ?>
            <? $product = $variant ->product() ?>
            <tr id="cart_item_<?= $cart_item->id() ?>">
              <td>
                <?= $product->g("name") ?> 
                (<?= $variant->color()->g("name") ?> - <?= $variant->size()->g("name") ?>)
              </td>
              <td>
                <input type="text" 
                       name="cart_items[<?= $cart_item->id() ?>][quantity]" 
                       value="<?= $cart_item->g("quantity") ?>">
              </td>
              <td><?= $variant->price_formatted() ?></td>
              <td><?= $cart_item->subtotal_formatted() ?></td>
              <td><a href="/cart/remove.php?id=<?= $cart_item->id() ?>">Remove</a></td>
            </tr>
          <? } ?>
        </tbody>
      </table>
      
      <p><button type="submit">Update cart</button></p>
    </form>
    
  </body>
</html>