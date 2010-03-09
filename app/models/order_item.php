<?

$dir = dirname(__FILE__);
require_once "$dir/../generic/model.php";

class OrderItem extends GenericModel
{
  
  public static $table_name = "order_items";
  public static $fields = array(
    "order_id" => "int",
    "quantity" => "int",
    "price_per_variant" => "int",
    "name" => "string",
    "color_id" => "int",
    "size_id" => "int",
    "photo_id" => "int",
    "subtotal" => "int",
    "variant_id" => "int"
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