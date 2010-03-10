<?

$dir = dirname(__FILE__);
require_once "$dir/../generic/model.php";

class User extends GenericModel
{
  
  public static $table_name = "users";
  public static $fields = array(
    "first_name" => "string",
    "last_name" => "string",
    "email" => "string",
    "address" => "string",
    "city" => "string",
    "state" => "string",
    "postal_code" => "string",
    "country" => "string",
    "phone" => "string"
  );
  
  function __construct($hash = array())
  {
    parent::__construct($hash);
  }
  
  function orders($hash = array())
  {
    return $this->has_many("Order");
  }
  
  function cart()
  {
    // TODO: find or create a cart for this user
  }
  
}

?>