<form id="checkout" action="/checkout/process.php" method="post">
  
  <fieldset>
    <legend>Contact Information</legend>
    
    <p>
      <label for="email">Email:</label>
      <input type="text" name="order[email]" id="email" value="<?= $order->g("email") ?>">
    </p>
    
    <p>
      <label for="phone">Phone number:</label>
      <input type="text" name="order[phone]" id="phone" value="<?= $order->g("phone") ?>">
    </p>
  </fieldset>
  
  <fieldset>
    <legend>Shipping Information</legend>
    
    <p>
      <label for="first_name">First name:</label>
      <input type="text" name="order[first_name]" id="first_name" value="<?= $order->g("first_name") ?>">
    </p>
    
    <p>
      <label for="last_name">Last name:</label>
      <input type="text" name="order[last_name]" id="last_name" value="<?= $order->g("last_name") ?>">
    </p>
    
    <p>
      <label for="address">Address:</label>
      <input type="text" name="order[address]" id="address" value="<?= $order->g("address") ?>">
    </p>
    
    <p>
      <label for="city">City:</label>
      <input type="text" name="order[city]" id="city" value="<?= $order->g("city") ?>">
    </p>
    
    <p>
      <label for="state">State:</label>
      <input type="text" name="order[state]" id="state" value="<?= $order->g("state") ?>">
    </p>
    
    <p>
      <label for="zip">Zip:</label>
      <input type="text" name="order[postal_code]" id="zip" value="<?= $order->g("postal_code") ?>">
    </p>
    
    <input type="hidden" name="order[country]" value="USA">
  </fieldset>
  
  <fieldset>
    <legend>Billing Information</legend>
    
    <p class="checkbox">
      <input type="checkbox" name="order[billing_is_same]" value="1" id="billing_is_same" value="<?= $order->g("billing_is_same") ?>">
      <label for="billing_is_same">
        My billing information is the same as my shipping information
      </label>
    </p>
    
    <div id="billing-info-fields">
      <p>
        <label for="billing_first_name">First name:</label>
        <input type="text" name="order[billing_first_name]" id="billing_first_name" value="<?= $order->g("billing_first_name") ?>">
      </p>

      <p>
        <label for="billing_last_name">Last name:</label>
        <input type="text" name="order[billing_last_name]" id="billing_last_name" value="<?= $order->g("billing_last_name") ?>">
      </p>

      <p>
        <label for="billing_address">Address:</label>
        <input type="text" name="order[billing_address]" id="billing_address" value="<?= $order->g("billing_address") ?>">
      </p>

      <p>
        <label for="billing_city">City:</label>
        <input type="text" name="order[billing_city]" id="billing_city" value="<?= $order->g("billing_city") ?>">
      </p>

      <p>
        <label for="billing_state">State:</label>
        <input type="text" name="order[billing_state]" id="billing_state" value="<?= $order->g("billing_state") ?>">
      </p>

      <p>
        <label for="billing_zip">Zip:</label>
        <input type="text" name="order[billing_postal_code]" id="billing_zip" value="<?= $order->g("billing_postal_code") ?>">
      </p>

      <input type="hidden" name="order[billing_country]" value="USA">
    </div>
  </fieldset>
  
  <fieldset>
    <legend>Payment Information</legend>
    <? $card = $order->g("card") ?>
    
    <p>
      <label for="card_number">Card number:</label>
      <input type="text" name="order[card][number]" id="card_number" value="<?= $card["number"] ?>">
    </p>
    
    <p>
      <label for="card_security_code">CVV:</label>
      <input type="text" name="order[card][security_code]" id="card_security_code" value="<?= $card["security_code"] ?>">
    </p>
    
    <p>
      <label for="expiration_month">Expiration month:</label>
      <select name="order[card][month]" id="expiration_month">
        <? $months = range(1, 12) ?>
        <? foreach ($months as $month) { ?>
          <option value="<?= $month ?>" <?= $card["month"] == $month ? "selected" : "" ?>><?= $month ?></option>
        <? } ?>
      </select>
    </p>
    
    <p>
      <label for="expiration_year">Expiration year:</label>
      <select name="order[card][year]" id="expiration_year">
        <? 
          $this_year = date("Y");
          $years = range($this_year, $this_year + 10);
        ?>
        <? foreach ($years as $year) { ?>
          <option value="<?= $year ?>" <?= $card["year"] == $year ? "selected" : "" ?>><?= $year ?></option>
        <? } ?>
      </select>
    </p>
  </fieldset>
  
  <div id="checkout-total">
    <h3>Cart recap</h3>
    
    <table cellpadding="0" cellspacing="0" border="0">
      <thead>
        <tr>
          <th>Photo</th>
          <th>Quantity</th>
          <th>Details</th>
          <th>Price per unit</th>
          <th>Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <? foreach ($cart_items as $cart_item) { ?>
          <? $variant = $cart_item->variant() ?>
          <? $color = $variant->color() ?>
          <? $product = $variant->product() ?>
          <tr id="cart_item_<?= $cart_item->id() ?>">
            <td class="photo">
              <img src="<?= $product->photo($color->id())->url("small") ?>" width="30" height="30">
            </td>
            <td class="quantity"><?= $cart_item->g("quantity") ?></td>
            <td class="description"><?= $variant->description() ?></td>
            <td><?= $variant->price_formatted() ?></td>
            <td><?= $cart_item->subtotal_formatted() ?></td>
          </tr>
        <? } ?>
      </tbody>
    </table>
    
    <h3>Order total</h3>
    
    <table cellpadding="0" cellspacing="0" border="0" id="totals">
      <tbody>
        <tr>
          <td>Subtotal:</td>
          <td><?= $cart->total_formatted() ?></td>
        </tr>
        <tr>
          <td>Tax (5%):</td>
          <td><?= $cart->tax_formatted() ?></td>
        </tr>
        <tr>
          <td>Shipping (flat rate):</td>
          <td><?= $cart->shipping_formatted() ?></td>
        </tr>
        <tr class="final">
          <td>Final total:</td>
          <td><?= $cart->final_total_formatted() ?></td>
        </tr>
      </tbody>
    </table>
  </div>
  
  <p class="purchase">
    <button type="submit">Purchase</button>
  </p>
  
</form>