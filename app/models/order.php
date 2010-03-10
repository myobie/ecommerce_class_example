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
    "name" => "string",
    "address" => "string",
    "city" => "string",
    "state" => "string",
    "postal_code" => "string",
    "country" => "string",
    "phone" => "string",
    "shipped_at" => "datetime",
    "shipping_tracking_number" => "string",
    "transaction_key" => "string"
  );
  public static $virtual_fields = array(
    "first_name" => null,
    "last_name" => null,
    "billing_is_same" => false,
    "billing_info" => array(),
    "card" => array()
  );
  
  public $transaction_error = null;
  
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
  
  function process_checkout()
  {
    // TEMP: just for testing
    $this->update(array(
      "card" => array(
        "number" => "4111 1111 1111 1111",
        "security_code" => "123",
        "month" => "1",
        "year" => "2012"
      )
    ));
    
    $transaction = new Transaction($this->g("card"));
    
    $success = $transaction->authorize();
    
    if ($success)
    {
      $this->update(array("transaction_key" => $transaction->key));
      $this->save();
    } else {
      $this->transaction_error = $transaction->error_message;
    }
  }
  
}

?>