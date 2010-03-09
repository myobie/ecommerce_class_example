<?

$dir = dirname(__FILE__);
require_once "$dir/../app/requires.php";
$db = new DB();

$photo = Photo::first(array(
  "where" => array(
    "product_id = ? AND color_id = ?", 
    $_REQUEST["product_id"], 
    $_REQUEST["color_id"]
  )
));

?>
<img src="<?= $photo->url("medium") ?>" width="300" height="300" class="product_photo">