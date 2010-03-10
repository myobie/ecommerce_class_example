<?

$dir = dirname(__FILE__);
require_once "$dir/../../app/requires.php";
$db = new DB();

// --- Product ---

$product = Product::get($_POST["id"]);
$product->update($_POST["product"]);
$product->save();

// --- Categories ---

$categories = Category::all();
$existing_category_ids = $product->category_ids();

$categories_to_add = array();
$categories_to_remove = array();

if (empty($_POST["product_categories"]))
{
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
  
}

if (! empty($categories_to_remove))
{
  Categorization::delete(array(
    "where" => array(
      "product_id = ? AND category_id IN (?)", 
      $product->id(),
      $categories_to_remove
    )
  ));
}

if (! empty($categories_to_add))
{
  foreach ($categories_to_add as $id) {
    Categorization::create(array("product_id" => $product->id(), "category_id" => $id));
  }
}

// --- Variants ---

$variant_ids_from_form = array();

foreach ($_POST["variants"] as $id => $data) {
  
  $variant = Variant::get($id);
  
  if ($variant)
  {
    $variant->update($data);
  } else {
    $variant = new Variant($data);
  }
  
  $variant->save();
  
  array_push($variant_ids_from_form, $variant->id());
  
}

// remove any variant's that were not in the form
if (!empty($variant_ids_from_form))
{
  Variant::delete(array(
    "where" => array(
      "product_id = ? AND id NOT IN (?)", 
      $product->id(), 
      $variant_ids_from_form
    )
  ));
}

// --- Redirect ---

header('Location: /admin/products/');

?>