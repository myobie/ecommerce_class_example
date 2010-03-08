<?

$dir = dirname(__FILE__);
require_once "$dir/../generic/model.php";

class CartItem extends GenericModel
{
  
  public static $table_name = "cart_items";
  public static $fields = array(
    "cart_id",
    "quantity",
    "variant_id"
  );
  
  function __construct($hash = array())
  {
    parent::__construct($hash);
  }
  
  function cart()
  {
    return $this->belongs_to("Cart");
  }
  
  function variant()
  {
    return $this->belongs_to("Variant");
  }
}

?>