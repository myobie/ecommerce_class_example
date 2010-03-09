<?

$dir = dirname(__FILE__);
require_once "$dir/../../app/requires.php";
$db = new DB();

$product = Product::get($_POST["id"]);
$product->update($_POST["product"]);
$product->save();

$categories = Category::all();
$existing_category_ids = $product->category_ids();

$categories_to_add = array();
$categories_to_remove = array();

if (empty($_POST["product_categories"]))
{
  echo "Empty, removing all.";
  $categories_to_remove = $existing_category_ids;
} else {
  
  foreach ($categories as $category) {
    if (array_search($category->id(), $_POST["product_categories"]) === false)
    {
      array_push($categories_to_remove, $category->id());
    } else if (array_search($category->id(), $existing_category_ids) === false) {
      array_push($categories_to_add, $category->id());
    }
  }
  
  // only remove ones that are already there
  $categories_to_remove = array_intersect($categories_to_remove, $existing_category_ids);
  
  if (! empty($categories_to_remove))
  {
    Categorization::delete(array(
      "where" => array(
        "product_id = ? AND category_id IN (?)", 
        $product->id(),
        implode(",", $categories_to_remove)
      )
    ));
  }
  
  if (! empty($categories_to_add))
  {
    foreach ($categories_to_add as $id) {
      Categorization::create(array("product_id" => $product->id(), "category_id" => $id));
    }
  }
  
}

header('Location: /admin/products/');

?>