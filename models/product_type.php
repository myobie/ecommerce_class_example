<?

$dir = dirname(__FILE__);
require_once "$dir/../generic/model.php";

class ProductType extends GenericModel
{
  
  public static $table_name = "product_type";
  public static $foreign_key = "product_type_id";
  public static $fields = array(
    "name"
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