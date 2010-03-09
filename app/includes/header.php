<!DOCTYPE html>
<html>
  <head>
    <title>My Store</title>
    <link rel="stylesheet" href="/css/master.css" type="text/css">
  </head>
  <body>
    <h1>Best T-Shirt Store Ever</h1>
    <ul id="nav">
      <li><a href="/">Home</a></li>
      <li><a href="/cart/">Cart</a></li>
    </ul>
    <div id="minicart">
      <h2>Cart Summary</h2>
      <p><?= $cart->quantity() ?> items in your cart.</p>
    </div>