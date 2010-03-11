<?

$dir = dirname(__FILE__);
require_once "$dir/../../app/requires.php";

$db = new DB();

$sizes = Size::all(array(
  "where" => array(
    "id NOT IN (SELECT size_id FROM variants WHERE product_id = ? AND color_id = ?)",
    $_REQUEST["product_id"], 
    $_REQUEST["color_id"]
  )
));

?>

<? if (count($sizes) > 0) { ?>
  
<select name="variants[new][size_id]" id="new_size_chooser">
  <option value="">Available Sizes</option>
  <? foreach ($sizes as $size) { ?>
    <option value="<?= $size->id() ?>"><?= $size->g("name") ?></option>
  <? } ?>
</select>

<? } else { ?>

<em id="new_size_chooser">No available sizes.</em>

<? } ?>