<?

$dir = dirname(__FILE__);
require_once "$dir/../generic/model.php";

class CartItem extends GenericModel
{
  
  public static $table_name = "cart_items";
  public static $fields = array(
    "cart_id" => "int",
    "quantity" => "int",
    "variant_id" => "int"
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
  
  function subtotal()
  {
    return $this->variant()->price() * $this->g("quantity");
  }
  
  function subtotal_formatted()
  {
    return "$" . number_format($this->subtotal(), 2, ".", ",");
  }
}

?>