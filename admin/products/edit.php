<?

$dir = dirname(__FILE__);
require_once "$dir/../../app/requires.php";

$db = new DB();

$product = Product::get($_GET["id"]);

$product_types = ProductType::all(array("order" => "name ASC"));
$collections = Collection::all(array("order" => "name ASC"));
$categories = Category::all(array("order" => "name ASC"));
$colors = Color::all(array("order" => "name ASC"));
$sizes = Size::all(array("order" => "id ASC"));

$variants = $product->variants();

$product_category_names = $product->category_names();

include "../../app/includes/admin/header.php";

?>
    
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
  
  <fieldset class="categories checkboxes">
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
  
  <fieldset class="inventory">
    <legend>Inventory</legend>
    
    <? foreach ($variants as $variant) { ?>
      <div class="variant">
        
        <p class="color">
          <label>Color:</label>
          <select name="variants[<?= $variant->id() ?>][color_id]">
            <? foreach ($colors as $color) { ?>
              <option value="<?= $color->id() ?>" <?= $color->id() == $variant->g("color_id") ? "selected" : "" ?>>
                <?= $color->g("name") ?>
              </option>
            <? } ?>
          </select>
        </p>
        
        <p class="size">
          <label>Size:</label>
          <select name="variants[<?= $variant->id() ?>][size_id]">
            <? foreach ($sizes as $size) { ?>
              <option value="<?= $size->id() ?>" <?= $size->id() == $variant->g("size_id") ? "selected" : "" ?>>
                <?= $size->g("name") ?>
              </option>
            <? } ?>
          </select>
        </p>
        
        <p class="price">
          <label>Price:</label>
          <input type="text" 
                 name="variants[<?= $variant->id() ?>][price]"
                 value="<?= $variant->g("price") ?>"
                 placeholder="<?= $product->g("default_price") ?>">
          <em>Leave blank to use default price</em>
        </p>
        
        <p class="sku">
          <label>SKU:</label>
          <input type="text" 
                 name="variants[<?= $variant->id() ?>][sku]"
                 value="<?= $variant->g("sku") ?>">
          <em>Must be unique per item</em>
        </p>
        
        <p class="quantity">
          <label>Quantity:</label>
          <input type="text" 
                 name="variants[<?= $variant->id() ?>][quantity]"
                 value="<?= $variant->g("quantity") ?>">
        </p>
        
      </div>
    <? } ?>
  </fieldset>
  
  <p>
    <button type="submit">Update product</button>
  </p>
  
</form>
    
<? include "../../app/includes/admin/footer.php" ?>