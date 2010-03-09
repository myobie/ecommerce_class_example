<?

$dir = dirname(__FILE__);
require_once "$dir/../generic/model.php";

class Cart extends GenericModel
{
  
  public static $table_name = "carts";
  public static $fields = array(
    "user_id" => "int"
  );
  
  function __construct($hash = array())
  {
    parent::__construct($hash);
  }
  
  function cart_items($hash = array())
  {
    return $this->has_many("CartItem", $hash);
  }
  
  function user()
  {
    return $this->belongs_to("User");
  }
  
}

?>