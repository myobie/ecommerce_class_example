<?

$dir = dirname(__FILE__);
require_once "$dir/../generic/model.php";

class Variant extends GenericModel
{
  
  public static $table_name = "variants";
  public static $fields = array(
    "product_id" => "int",
    "color_id" => "int",
    "size_id" => "int",
    "price" => "int",
    "sku" => "string",
    "quantity" => "int"
  );
  
  function __construct($hash = array())
  {
    parent::__construct($hash);
  }
  
  function product()
  {
    return $this->belongs_to("Product");
  }
  
  function color()
  {
    return $this->belongs_to("Color");
  }
  
  function size()
  {
    return $this->belongs_to("Size");
  }
  
}

?>