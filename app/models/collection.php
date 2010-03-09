<?

$dir = dirname(__FILE__);
require_once "$dir/../generic/model.php";

class Collection extends GenericModel
{
  
  public static $table_name = "collections";
  public static $fields = array(
    "name" => "string"
  );
  
  function __construct($hash = array())
  {
    parent::__construct($hash);
  }
  
  function products($hash = array())
  {
    return $this->has_many("Product", $hash);
  }
  
}

?>