<?

$dir = dirname(__FILE__);
require_once "$dir/app/requires.php";
$db = new DB();

$cart = find_or_create_cart();

$products = Product::all(array("order" => "name ASC"));
$sizes = Size::all(array("order" => "id ASC"));
$colors = Color::all(array("order" => "name ASC"));
$first_color_id = $colors[0]->id(); // make sure the images line up with the select box

include "app/includes/header.php";

?>

<h2>Products</h2>
<ul class="products">
  <? foreach ($products as $product) { ?>
    <li class="<?= cycle(array("odd", "even")) ?>">
      <span class="title">
        <?= $product->g("name") ?> - <?= implode(", ", $product->category_names()) ?>
      </span>
      
      <img src="<?= $product->photo($first_color_id)->url("medium") ?>" width="300" height="300" class="product_photo">
    
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