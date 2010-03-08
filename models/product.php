<?

$dir = dirname(__FILE__);
require_once "$dir/../generic/model.php";

class Product extends GenericModel
{
  
  public static $table_name = "products";
  public static $foreign_key = "product_id";
  public static $fields = array(
    "product_type_id",
    "collection_id",
    "default_price",
    "base_sku",
    "name"
  );
  
  function __construct($hash = array())
  {
    parent::__construct($hash);
  }
  
  // function product_type()
  // {
  //   return $this->belongs_to("ProductType");
  // }
  
  function collection()
  {
    return $this->belongs_to("Collection");
  }
  
  function variants($hash = array())
  {
    return $this->has_many("Variant", $hash);
  }
  
  function photos($hash = array())
  {
    return $this->has_many("Photo", $hash);
  }
  
  function categories()
  {
    return Category::all(array(
      "where" => array(
        "id IN (SELECT category_id FROM categorizations WHERE product_id = ?)", 
        $this->id()
      )
    ));
  }
  
}

?>