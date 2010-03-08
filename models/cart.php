<?

$dir = dirname(__FILE__);
require_once "$dir/../generic/model.php";
require_once "cart_item.php";

class Cart extends GenericModel
{
  
  public static $table_name = "carts";
  public static $foreign_key = "cart_id";
  public static $fields = array(
    "user_id"
  );
  
  function __construct($hash = array())
  {
    parent::__construct($hash);
  }
  
  function cart_items($hash = array())
  {
    $hash = $this->has_many_where($hash);
    return CartItem::all($hash);
  }
  
}

?>