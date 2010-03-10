<!DOCTYPE html>
<html>
  <head>
    <title>My Store</title>
    <link rel="stylesheet" href="/css/master.css" type="text/css">
  </head>
  <body>
    <div id="header">
      <h1>Best T-Shirt Store Ever</h1>
      <ul id="nav">
        <li><a href="/">Home</a></li>
        <li class="account"><a href="/cart/">Cart</a></li>
      </ul>
      <div id="minicart">
        <h2>Cart Summary</h2>
        <p><?= $cart_quantity ?> items in your cart. <strong>Total: $<?= number_format($cart_total / 100, 2, ".", ",") ?></strong></p>
        <div class="summary">
          <table cellpadding="0" cellspacing="0" border="0">
            <? foreach ($cart_items as $cart_item) { ?>
              <tr>
                <td class="quantity"><?= $cart_item["quantity"] ?></td> 
                <td class="description"><? // $variant->description() ?></td> 
                <td>$<?= number_format($cart_item["quantity"] * $cart_item["price"] / 100, 2, ".", ",") ?></td>
              </tr>
            <? } ?>
          </table>
          
          <p><a href="/cart/">Edit cart</a></p>
        </div>
      </div>
    </div>
    <div id="content">