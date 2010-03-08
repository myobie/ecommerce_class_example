<?

$dir = dirname(__FILE__);
require_once "$dir/../generic/model.php";

class Size extends GenericModel
{
  
  public static $table_name = "sizes";
  public static $foreign_key = "size_id";
  public static $fields = array(
    "name",
    "short_name"
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