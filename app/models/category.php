<?

$dir = dirname(__FILE__);
require_once "$dir/../generic/model.php";

class Category extends GenericModel
{
  
  public static $table_name = "categories";
  public static $fields = array(
    "name" => "string"
  );
  
  function __construct($hash = array())
  {
    parent::__construct($hash);
  }
  
  function products()
  {
    return Product::all(array(
      "where" => array(
        "id IN (SELECT product_id FROM categorizations WHERE category_id = ?)", 
        $this->id()
      )
    ));
  }
  
}

?>