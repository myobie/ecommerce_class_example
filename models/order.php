<?

$dir = dirname(__FILE__);
require_once "$dir/../generic/model.php";

class Order extends GenericModel
{
  
  public static $table_name = "orders";
  public static $foreign_key = "order_id";
  public static $fields = array(
    "user_id",
    "total",
    "shipping_total",
    "tax_total",
    "name",
    "address",
    "city",
    "state",
    "postal_code",
    "country",
    "phone",
    "shipped_at",
    "shipping_tracking_number"
  );
  
  function __construct($hash = array())
  {
    parent::__construct($hash);
  }
  
}

?>