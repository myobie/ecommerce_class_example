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

$variants = $product->variants(array(
  "order" => "color_id ASC, size_id ASC, sku ASC"
));

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
    
    <table cellpadding="0" cellspacing="0" border="0" id="variants">
      <thead>
        <tr>
          <th>Color</th>
          <th>Size</th>
          <th>Price</th>
          <th>SKU</th>
          <th>Quantity</th>
        </tr>
      </thead>
      <tbody>
        <tr class="add">
          <td>
            <select name="variants[new][color_id]" id="new_color_chooser">
              <option value="">Available Colors</option>
              <? foreach ($colors as $color) { ?>
                <option value="<?= $color->id() ?>"><?= $color->g("name") ?></option>
              <? } ?>
            </select>
          </td>
          <td>
            <? include "../sizes/available_select.snippet.php" ?>
          </td>
          <td>
            <input type="text" 
                   id = "new_price"
                   name="variants[new][price]"
                   value=""
                   placeholder="<?= $product->g("default_price") ?>">
          </td>
          <td>
            <input type="text" 
                   name="variants[new][sku]"
                   id="new_sku"
                   value="<?= $product->g("base_sku") ?>">
          </td>
          <td>
            <input type="text" 
                   name="variants[new][quantity]"
                   id="new_quantity"
                   value="1">
          </td>
        </tr>
        <? foreach ($variants as $variant) { ?>
          <? include "../variants/table_row.snippet.php" ?>
        <? } ?>
      </tbody>
    </table>
  </fieldset>
  
  <p>
    <button type="submit">Update product</button>
  </p>
  
</form>
    
<? include "../../app/includes/admin/footer.php" ?>