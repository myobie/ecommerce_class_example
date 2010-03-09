<?

$dir = dirname(__FILE__);
require_once "$dir/../../app/requires.php";
$db = new DB();

$products = Product::all(array("order" => "name ASC"));
$colors = Color::all(array("order" => "name ASC"));

?><!DOCTYPE html>
<html>
  <head>
    <title>Admin - Photos</title>
  </head>
  <body>
    
    <h1>Upload a photo</h1>
    
    <form action="/admin/photos/create.php" method="post" enctype="multipart/form-data">
      
      <p>
        <label for="product_id">Product:</label><br>
        <select name="photo[product_id]" id="product_id">
          <? foreach ($products as $product) { ?>
            <option value="<?= $product->id() ?>">
              <?= $product->g("name") ?> - 
              <?= implode(" ,", $product->category_names()) ?>
            </option>
          <? } ?>
        </select>
      </p>
      
      <p>
        <label for="color_id">Color:</label><br>
        <select name="photo[color_id]" id="color_id">
          <? foreach ($colors as $color) { ?>
            <option value="<?= $color->id() ?>">
              <?= $color->g("name") ?>
            </option>
          <? } ?>
        </select>
      </p>
      
      <p>
        <label for="file">File:</label><br>
        <input type="file" name="file" id="file">
      </p>
      
      <p><button type="submit">Upload photo</button></p>
      
    </form>
    
  </body>
</html>