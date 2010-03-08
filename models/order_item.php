<?

$dir = dirname(__FILE__);
require_once "$dir/../generic/model.php";

class OrderItem extends GenericModel
{
  
  public static $table_name = "order_items";
  public static $foreign_key = "order_item_id";
  public static $fields = array(
    "order_id",
    "quantity",
    "price_per_variant",
    "name",
    "color_id",
    "size_id",
    "photo_id",
    "subtotal",
    "variant_id"
  );
  
  function __construct($hash = array())
  {
    parent::__construct($hash);
  }
  
  function order()
  {
    return $this->belongs_to("Order");
  }
  
  function color()
  {
    return $this->belongs_to("Color");
  }
  
  function size()
  {
    return $this->belongs_to("Size");
  }
  
  function photo()
  {
    return $this->belongs_to("Photo");
  }
  
  function variant()
  {
    return $this->belongs_to("Variant");
  }
  
}

?>