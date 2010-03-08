<?

$dir = dirname(__FILE__);
require_once "$dir/../generic/model.php";

class User extends GenericModel
{
  
  public static $table_name = "users";
  public static $foreign_key = "user_id";
  public static $fields = array(
    "first_name",
    "last_name",
    "email",
    "address",
    "city",
    "state",
    "postal_code",
    "country",
    "phone"
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