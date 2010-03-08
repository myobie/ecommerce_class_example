<?

$dir = dirname(__FILE__);
require_once "$dir/../generic/model.php";

class Cart extends GenericModel
{
  
  public static $table_name = "carts";
  public static $fields = array(
    "user_id"
  );
  
  function __construct($hash = array())
  {
    super;
  }
}

?>