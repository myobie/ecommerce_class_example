<?

$dir = dirname(__FILE__);
require_once "$dir/../generic/model.php";
require_once "$dir/transaction.php";

class Order extends GenericModel
{
  
  public static $table_name = "orders";
  public static $fields = array(
    "user_id" => "int",
    "total" => "int",
    "shipping_total" => "int",
    "tax_total" => "int",
    "email" => "string",
    "phone" => "string",
    "name" => "string",
    "address" => "string",
    "city" => "string",
    "state" => "string",
    "postal_code" => "string",
    "country" => "string",
    "billing_name" => "string",
    "billing_address" => "string",
    "billing_city" => "string",
    "billing_state" => "string",
    "billing_postal_code" => "string",
    "billing_country" => "string",
    "shipped_at" => "datetime",
    "shipping_tracking_number" => "string",
    "transaction_key" => "string",
    "status" => "string",
    "error_message" => "text"
  );
  public static $virtual_fields = array(
    "first_name" => null,
    "last_name" => null,
    "billing_first_name" => null,
    "billing_last_name" => null,
    "billing_is_same" => false,
    "card" => array()
  );
  
  public $errors = array();
  
  function __construct($hash = array())
  {
    parent::__construct($hash);
  }
  
  function user()
  {
    return $this->belongs_to("User");
  }
  
  function order_items($hash = array())
  {
    return $this->has_many("OrderItem", $hash);
  }
  
  function total()
  {
    return $this->g("total");
  }
  
  function total_formatted()
  {
    return "$" . number_format($this->final_total(), 2, ".", ",");
  }
  
  function before_save()
  {
    $first_name = $this->g("first_name");
    $last_name = $this->g("last_name");
    if (!empty($first_name) && !empty($last_name))
      $this->update(array("name" => $first_name . " " . $last_name));

    $billing_first_name = $this->g("billing_first_name");
    $billing_last_name = $this->g("billing_last_name");
    if (!empty($billing_first_name) && !empty($billing_last_name))
      $this->update(array("billing_name" => $billing_first_name . " " . $billing_last_name));
    
    if ($this->g("billing_is_same"))
    {
      $this->update(array(
        "billing_name" => $this->g("name"), 
        "billing_address" => $this->g("address"), 
        "billing_city" => $this->g("city"), 
        "billing_state" => $this->g("state"), 
        "billing_postal_code" => $this->g("postal_code"), 
        "billing_country" => $this->g("country")
      ));
    }
  }
  
  function validate()
  {
    $this->before_save();
    
    $required_fields = array("email", "phone", "name", "address", "city", "state", "postal_code", "country", "billing_name", "billing_address", "billing_city", "billing_state", "billing_postal_code", "billing_country");
    
    $valid = true;
    
    foreach ($required_fields as $field) {
      $value = $this->g($field);
      if (empty($value))
      {
        $valid = false;
        array_push($this->errors, ucfirst($field) . " is blank");
      }
    }
    
    return $valid;
  }
  
  function process_checkout()
  {
    if (! $this->validate())
      return false;
    
    $transaction = new Transaction($this->g("card"));
    
    $transaction_success = $transaction->purchase($this->total());
    
    if ($transaction_success)
    {
      $this->update(array(
        "transaction_key" => $transaction->key,
        "status" => "authorized"
      ));
    } else {
      $this->update(array(
        "status" => "failed",
        "error_message" => $transaction->error_message
      ));
      array_push($this->errors, $transaction->error_message);
    }
    
    $save_success = $this->save();
    
    return $transaction_success && $save_success;
  }
  
  function copy($cart)
  {
    $cart_items = $cart->cart_items();
    
    foreach ($cart_items as $cart_item) {
      
      $variant = $cart_item->variant();
      $product = $variant->product();
      
      $order_item = new OrderItem($cart_item->attributes());
      $order_item->update(array(
        "price_per_variant" => $variant->price() * 100,
        "name" => $product->g("name"),
        "color_id" => $variant->g("color_id"),
        "size_id" => $variant->g("size_id"),
        "photo_id" => $product->photo($variant->g("color_id"))->id(),
        "subtotal" => $cart_item->subtotal() * 100
      ));
      $order_item->save();
      
    }
  }
  
}

?>