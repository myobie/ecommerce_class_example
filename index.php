<?
/*
$dir = dirname(__FILE__);
require_once "$dir/app/requires.php";
$db = new DB();

$cart = find_or_create_cart();

$products = Product::all(array("order" => "name ASC"));
$sizes = Size::all(array("order" => "id ASC"));
$colors = Color::all(array("order" => "name ASC"));
$first_color_id = $colors[0]->id(); // make sure the images line up with the select box
*/

function mysqli_result_to_array($result)
{
  $arr = array();
  
  while($row = $result->fetch_assoc())
  {
    array_push($arr, $row);
  }
  
  return $arr;
}

$m = new mysqli('localhost', 'root', '', 'ecommerce_class');

session_start();

$cart_id = $m->real_escape_string($_SESSION["cart_id"]);
$cart_result = $m->query("SELECT * FROM carts WHERE id = '$cart_id'");

if ($cart_result->num_rows < 1)
{
  $m->query("INSERT INTO carts VALUES()");
  $cart_id = $m->insert_id;
  $_SESSION["cart_id"] = $cart_id;
}

$result = $m->query("SELECT cart_items.id, cart_items.quantity, 
                            variants.price, variants.product_id 
                     FROM cart_items, variants
                     WHERE cart_items.cart_id = '$cart_id' 
                     AND variants.id = cart_items.variant_id");
$cart_items = mysqli_result_to_array($result);

$total_func = create_function('$sum, $row', 'return $sum += ($row["quantity"] * $row["price"]);');
$cart_total = array_reduce($cart_items, $total_func, 0);

$quantity_func = create_function('$sum, $row', 'return $sum += $row["quantity"];');
$cart_quantity = array_reduce($cart_items, $quantity_func, 0);

$result = $m->query("SELECT * FROM products ORDER BY name ASC");
$products = mysqli_result_to_array($result);

$product_ids = array();

foreach ($products as $product) {
  array_push($product_ids, $product["id"]);
}

$product_ids_string = "'" . implode("','", $product_ids) . "'";

$result = $m->query("SELECT categories.id, categories.name, categorizations.product_id
                     FROM categories, categorizations
                     WHERE categorizations.category_id = categories.id 
                     AND categorizations.product_id IN ($product_ids_string)
                     GROUP BY categorizations.product_id");
$categories = mysqli_result_to_array($result);

function categories_for_product($product)
{
  global $categories;
  $cats = array();
  
  foreach ($categories as $category) {
    if ($category["product_id"] == $product["id"])
      array_push($cats, $category["name"]);
  }
  
  return $cats;
}

$result = $m->query("SELECT * FROM sizes ORDER BY id ASC");
$sizes = mysqli_result_to_array($result);

$result = $m->query("SELECT * FROM colors ORDER BY name ASC");
$colors = mysqli_result_to_array($result);

$first_color_id = $colors[0]["id"];

function photo_url($type, $photo)
{
  return "/images" . "/uploads" . "/" . 
         $photo["id"] . "/$type" . "/" . $photo["file_name"];
}

include "app/includes/header.php";

?>

<h2>Products</h2>
<ul class="products">

  <? foreach ($products as $product) { ?>
    <li>
      <span class="title">
        <?= $product["name"] ?> - <?= implode(", ", categories_for_product($product)) ?>
      </span>
      
      <?
      $id = $product["id"];
      $result = $m->query("SELECT * FROM photos WHERE product_id = '$id' AND color_id = '$first_color_id' LIMIT 1");
      $photos = mysqli_result_to_array($result);
      $photo = $photos[0];
      ?>
      
      <img src="<?= photo_url("medium", $photo) ?>" width="300" height="300" class="product_photo">
    
      <form action="/cart/add.php" method="post">
        <input type="hidden" name="product_id" value="<?= $id ?>">
        <select name="size_id">
          <? foreach ($sizes as $size) { ?>
            <option value="<?= $size["id"] ?>"><?= $size["short_name"] ?></option>
          <? } ?>
        </select>
        <select name="color_id">
          <? foreach ($colors as $color) { ?>
            <option value="<?= $color["id"] ?>"><?= $color["name"] ?></option>
          <? } ?>
        </select>
        <button type="submit">Add to cart</button>
      </form>
    </li>
  <? } ?>
  

</ul>
    
<? include "app/includes/footer.php" ?>