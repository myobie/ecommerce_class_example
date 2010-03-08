<?

$dir = dirname(__FILE__);
require_once "$dir/../generic/model.php";

class Color extends GenericModel
{
  
  public static $table_name = "colors";
  public static $foreign_key = "color_id";
  public static $fields = array(
    "name",
    "r",
    "g",
    "b",
    "c",
    "m",
    "y",
    "k"
  );
  
  function __construct($hash = array())
  {
    parent::__construct($hash);
  }
  
}

?>