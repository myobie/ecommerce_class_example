<?

$dir = dirname(__FILE__);
require_once "$dir/../../app/requires.php";

$db = new DB();

$product = Product::get($_GET["id"]);
$product_types = ProductType::all(array("order" => "name ASC"));
$collections = Collection::all(array("order" => "name ASC"));
$categories = Category::all(array("order" => "name ASC"));
$product_category_names = $product->category_names();

?><!DOCTYPE html>
<html>
  <head>
    <title>Admin - Products</title>
  </head>
  <body>
    
    <h1>Editing Product <?= $product->id() ?>: <?= $product->g("name") ?></h1>
    
    <form action="/admin/products/update.php" method="post">
      <input type="hidden" name="id" value="<?= $product->id() ?>">
      
      <p>
        <label for="product_name">Name:</label>
        <input type="text" name="product[name]" id="product_name" value="<?= $product->g("name") ?>">
      </p>
      
      <p>
        <label for="product_type">Type:</label>
        <select name="product[product_type_id]" id="product_type">
          <? foreach ($product_types as $product_type) { ?>
            <option value="<?= $product_type->id() ?>" <?= $product_type->id() == $product->g("product_type_id") ? "selected" : "" ?>>
              <?= $product_type->g("name") ?>
            </option>
          <? } ?>
        </select>
      </p>
      
      <p>
        <label for="product_collection">Collection:</label>
        <select name="product[collection_id]">
          <? foreach ($collections as $collection) { ?>
            <option value="<?= $collection->id() ?>" <?= $collection->id() == $product->g("collection_id") ? "selected" : "" ?>>
              <?= $collection->g("name") ?>
            </option>
          <? } ?>
        </select>
      </p>
      
      <p>
        <label for="product_default_price">Default price:</label>
        <input type="text" name="product[default_price]" id="product_default_price" value="<?= $product->g("default_price") ?>">
      </p>
      
      <p>
        <label for="product_base_sku">Base SKU:</label>
        <input type="text" name="product[base_sku]" id="product_base_sku" value="<?= $product->g("base_sku") ?>">
      </p>
      
      <fieldset>
        <legend>Categories</legend>
        
        <? foreach ($categories as $category) { ?>
          <p>
            <input type="checkbox" 
                   name="product_categories[]" 
                   value="<?= $category->id() ?>" 
                   id="product_category_<?= $category->id() ?>"
                   <?= array_search($category->g("name"), $product_category_names) !== false ? "checked" : "" ?>
                   >
            <label for="product_category_<?= $category->id() ?>">
              <?= $category->g("name") ?>
            </label>
          </p>
        <? } ?>
      </fieldset>
      
      <p>
        <button type="submit">Update product</button>
      </p>
      
    </form>
    
  </body>
</html>