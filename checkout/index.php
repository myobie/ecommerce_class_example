<?

$dir = dirname(__FILE__);
require_once "$dir/../app/requires.php";
$db = new DB();

$cart = find_or_create_cart();
$cart_items = $cart->cart_items();

include "../app/includes/header.php";

?>

<? if (count($cart_items) > 0) { ?>
  
  <h2>Checkout</h2>
  
  <form id="checkout" action="/checkout/process.php" method="post">
    
    <fieldset>
      <legend>Contact Information</legend>
      
      <p>
        <label for="email">Email:</label>
        <input type="text" name="order[email]" id="email">
      </p>
      
      <p>
        <label for="phone">Phone number:</label>
        <input type="text" name="order[phone]" id="phone">
      </p>
    </fieldset>
    
    <fieldset>
      <legend>Shipping Information</legend>
      
      <p>
        <label for="first_name">First name:</label>
        <input type="text" name="order[first_name]" id="first_name">
      </p>
      
      <p>
        <label for="last_name">Last name:</label>
        <input type="text" name="order[last_name]" id="last_name">
      </p>
      
      <p>
        <label for="address">Address:</label>
        <input type="text" name="order[address]" id="address">
      </p>
      
      <p>
        <label for="city">City:</label>
        <input type="text" name="order[city]" id="city">
      </p>
      
      <p>
        <label for="state">State:</label>
        <input type="text" name="order[state]" id="state">
      </p>
      
      <p>
        <label for="zip">Zip:</label>
        <input type="text" name="order[postal_code]" id="zip">
      </p>
      
      <input type="hidden" name="order[country]" value="USA">
    </fieldset>
    
    <fieldset>
      <legend>Billing Information</legend>
      
      <p class="checkbox">
        <input type="checkbox" name="order[billing_is_same]" value="1" id="billing_is_same">
        <label for="billing_is_same">
          My billing information is the same as my shipping information
        </label>
      </p>
      
      <div id="billing-info-fields">
        <p>
          <label for="first_name">First name:</label>
          <input type="text" name="order[billing_info][first_name]" id="first_name">
        </p>

        <p>
          <label for="last_name">Last name:</label>
          <input type="text" name="order[billing_info][last_name]" id="last_name">
        </p>

        <p>
          <label for="address">Address:</label>
          <input type="text" name="order[billing_info][address]" id="address">
        </p>

        <p>
          <label for="city">City:</label>
          <input type="text" name="order[billing_info][city]" id="city">
        </p>

        <p>
          <label for="state">State:</label>
          <input type="text" name="order[billing_info][state]" id="state">
        </p>

        <p>
          <label for="zip">Zip:</label>
          <input type="text" name="order[billing_info][postal_code]" id="zip">
        </p>

        <input type="hidden" name="order[billing_info][country]" value="USA">
      </div>
    </fieldset>
    
    <fieldset>
      <legend>Payment Information</legend>
      
      <p>
        <label for="card_number">Card number:</label>
        <input type="text" name="order[card][number]" id="card_number">
      </p>
      
      <p>
        <label for="card_security_code">CVV:</label>
        <input type="text" name="order[card][security_code]" id="card_security_code">
      </p>
      
      <p>
        <label for="expiration_month">Expiration month:</label>
        <select name="order[card][month]" id="expiration_month">
          <? $months = range(1, 12) ?>
          <? foreach ($months as $month) { ?>
            <option value="<?= $month ?>"><?= $month ?></option>
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
            <option value="<?= $year ?>"><?= $year ?></option>
          <? } ?>
        </select>
      </p>
    </fieldset>
    
    <div id="checkout-total">
      <table cellpadding="0" cellspacing="0" border="0">
        <thead>
          <tr>
            <th>Thing</th>
            <th>Amount</th>
          </tr>
        </thead>
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
  
<? } else { ?>
  
  <h2>Your cart is currently empty.</h2>
  <p>Shop around and add some items when you are ready.</p>
  
<? } ?>

<? include "../app/includes/footer.php"; ?>