<?

$dir = dirname(__FILE__);
require_once "$dir/../generic/model.php";

class Collection extends GenericModel
{
  
  public static $table_name = "collections";
  public static $foreign_key = "collection_id";
  public static $fields = array(
    "name"
  );
  
  function __construct($hash = array())
  {
    parent::__construct($hash);
  }
  
}

?>