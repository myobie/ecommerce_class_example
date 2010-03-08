<?

$dir = dirname(__FILE__);
require_once "$dir/../generic/model.php";

class Page extends GenericModel
{
  
  public static $table_name = "pages";
  public static $foreign_key = "page_id";
  public static $fields = array(
    "slug",
    "title",
    "content"
  );
  
  function __construct($hash = array())
  {
    parent::__construct($hash);
  }
  
}

?>