<?

$dir = dirname(__FILE__);
require_once "$dir/../../app/requires.php";

$db = new DB();

$products = Product::all(array("order" => "name ASC"));

include "../../app/includes/admin/header.php";

?>
    
<h1>Products</h1>

<table>
  <thead>
    <tr>
      <th>Name</th>
      <th>Type</th>
      <th>Collection</th>
      <th>Categories</th>
      <th>Edit</th>
    </tr>
  </thead>
  <tbody>
    <? foreach ($products as $product) { ?>
      <tr id="product_<?= $product->id() ?>">
        <td><?= $product->get_attribute("name") ?></td>
        <td><?= $product->product_type()->get_attribute("name") ?></td>
        <td><?= $product->collection()->get_attribute("name") ?></td>
        <td><?= implode(", ", $product->category_names()) ?></td>
        <td><a href="/admin/products/edit.php?id=<?= $product->id() ?>">Edit</a></td>
      </tr>
    <? } ?>
  </tbody>
</table>
    
<? include "../../app/includes/admin/footer.php" ?>