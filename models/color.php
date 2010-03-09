<?

$dir = dirname(__FILE__);
require_once "$dir/../generic/model.php";

class Color extends GenericModel
{
  
  public static $table_name = "colors";
  public static $fields = array(
    "name" => "string",
    "r" => "float",
    "g" => "float",
    "b" => "float",
    "c" => "float",
    "m" => "float",
    "y" => "float",
    "k" => "float"
  );
  
  function __construct($hash = array())
  {
    parent::__construct($hash);
  }
  
  function photos($hash = array())
  {
    return $this->has_many("Photo", $hash);
  }
  
  function variants($hash = array())
  {
    return $this->has_many("Variant", $hash);
  }
  
}

?>