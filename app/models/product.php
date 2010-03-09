<?

$dir = dirname(__FILE__);
require_once "$dir/../generic/model.php";

class Product extends GenericModel
{
  
  public static $table_name = "products";
  public static $fields = array(
    "product_type_id" => "int",
    "collection_id" => "int",
    "default_price" => "int",
    "base_sku" => "string",
    "name" => "string"
  );
  
  function __construct($hash = array())
  {
    parent::__construct($hash);
  }
  
  function product_type()
  {
    return $this->belongs_to("ProductType");
  }
  
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
  
  function photo()
  {
    $photos = $this->photos(array("limit" => 1));
    if (! empty($photos))
    {
      return $photos[0];
    } else {
      return new Photo;
    }
  }
  
  function categories()
  {
    return Category::all(array(
      "where" => array(
        "id IN (SELECT category_id FROM categorizations WHERE product_id = ?)", 
        $this->id()
      ),
      "order" => "name ASC"
    ));
  }
  
  function category_names()
  {
    $cats = $this->categories();
    $result = array();
    
    foreach ($cats as $cat) {
      array_push($result, $cat->get_attribute("name"));
    }
    
    return $result;
  }
  
}

?>