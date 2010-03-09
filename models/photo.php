<?

$dir = dirname(__FILE__);
require_once "$dir/../generic/model.php";

class Photo extends GenericModel
{
  
  public static $table_name = "photos";
  public static $fields = array(
    "product_id" => "int",
    "color_id" => "int",
    "file_name" => "string"
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
  
}

?>