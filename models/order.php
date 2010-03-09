<?

$dir = dirname(__FILE__);
require_once "$dir/../generic/model.php";

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
    "shipping_tracking_number" => "string"
  );
  
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
  
}

?>