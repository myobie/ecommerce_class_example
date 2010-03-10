<?

$dir = dirname(__FILE__);
require_once "$dir/../generic/model.php";

class Size extends GenericModel
{
  
  public static $table_name = "sizes";
  public static $fields = array(
    "name" => "string",
    "short_name" => "string"
  );
  
  function __construct($hash = array())
  {
    parent::__construct($hash);
  }
  
  function variants($hash = array())
  {
    return $this->has_many("Variant", $hash);
  }
  
}

?>