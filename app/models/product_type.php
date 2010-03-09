<?

$dir = dirname(__FILE__);
require_once "$dir/../generic/model.php";

class ProductType extends GenericModel
{
  
  public static $table_name = "product_types";
  public static $fields = array(
    "name" => "string"
  );
  
  function __construct($hash = array())
  {
    parent::__construct($hash);
  }
  
  function product($hash = array())
  {
    return $this->has_many("Product", $hash);
  }
  
}

?>