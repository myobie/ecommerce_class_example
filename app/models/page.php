<?

$dir = dirname(__FILE__);
require_once "$dir/../generic/model.php";

class Page extends GenericModel
{
  
  public static $table_name = "pages";
  public static $fields = array(
    "slug" => "string",
    "title" => "string",
    "content" => "text"
  );
  
  function __construct($hash = array())
  {
    parent::__construct($hash);
  }
  
}

?>