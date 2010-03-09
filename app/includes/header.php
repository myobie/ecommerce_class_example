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
        <p><?= $cart->quantity() ?> items in your cart. <strong>Total: <?= $cart->total_formatted() ?></strong></p>
        <div class="summary">
          <table cellpadding="0" cellspacing="0" border="0">
            <? $cart_items = $cart->cart_items(); ?>
            <? foreach ($cart_items as $cart_item) { ?>
              <? $variant = $cart_item->variant() ?>
              <tr>
                <td class="quantity"><?= $cart_item->g("quantity") ?></td> 
                <td class="description"><?= $variant->description() ?></td> 
                <td><?= $cart_item->subtotal_formatted(); ?></td>
              </tr>
            <? } ?>
          </table>
          
          <p><a href="/cart/">Edit cart</a></p>
        </div>
      </div>
    </div>
    <div id="content">