<?

$dir = dirname(__FILE__);
require_once "$dir/app/requires.php";
$db = new DB();

$cart = find_or_create_cart();

$products = Product::all(array("order" => "name ASC"));
$sizes = Size::all(array("order" => "name ASC"));
$colors = Color::all();

include "app/includes/header.php";

?>

    <h2>Products</h2>
    <ul>
      <? foreach ($products as $product) { ?>
        <li>
          <?= $product->g("name") ?> - <?= implode(", ", $product->category_names()) ?>
          
          <form action="/cart/add.php" method="post">
            <input type="hidden" name="product_id" value="<?= $product->id() ?>">
            <select name="size_id">
              <? foreach ($sizes as $size) { ?>
                <option value="<?= $size->id() ?>"><?= $size->g("short_name") ?></option>
              <? } ?>
            </select>
            <select name="color_id">
              <? foreach ($colors as $color) { ?>
                <option value="<?= $color->id() ?>"><?= $color->g("name") ?></option>
              <? } ?>
            </select>
            <button type="submit">Add to cart</button>
          </form>
        </li>
      <? } ?>
    </ul>
    
<? include "app/includes/footer.php" ?>