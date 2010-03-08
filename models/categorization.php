<?

$dir = dirname(__FILE__);
require_once "$dir/../generic/model.php";

class Categorization extends GenericModel
{
  
  public static $table_name = "categorizations";
  public static $fields = array(
    "slug",
    "title",
    "content"
  );
  
  function __construct($hash = array())
  {
    parent::__construct($hash);
  }
  
  function product()
  {
    return $this->belongs_to("Product");
  }
  
  function category()
  {
    return $this->belongs_to("Category");
  }
  
}

?>