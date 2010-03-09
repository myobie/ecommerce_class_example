<?

$dir = dirname(__FILE__);
require_once "$dir/../app/requires.php";
$db = new DB();

$cart = find_or_create_cart();
$cart_items = $cart->cart_items();

include "../app/includes/header.php";

?>

<h2>Cart</h2>

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
        <? $variant = $cart_item->variant() ?>
        <? $product = $variant ->product() ?>
        <tr id="cart_item_<?= $cart_item->id() ?>">
          
          <td class="photo">
            <img src="#" width="30" height="30">
          </td>
          <td>
            <?= $product->g("name") ?> 
            (<?= $variant->color()->g("name") ?> - <?= $variant->size()->g("name") ?>)
          </td>
          <td class="quantity">
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

<? include "../app/includes/footer.php"; ?>