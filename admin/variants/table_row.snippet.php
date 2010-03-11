<tr id="variant_<?= $variant->id() ?>">
  <td><?= $variant->color()->g("name") ?></td>
  <td><?= $variant->size()->g("name") ?></td>
  <td>
    <input type="text" 
           name="variants[<?= $variant->id() ?>][price]"
           value="<?= $variant->g("price") ?>"
           placeholder="<?= $product->g("default_price") ?>">
  </td>
  <td>
    <input type="text" 
           name="variants[<?= $variant->id() ?>][sku]"
           value="<?= $variant->g("sku") ?>">
  </td>
  <td>
    <input type="text" 
           name="variants[<?= $variant->id() ?>][quantity]"
           value="<?= $variant->g("quantity") ?>">
  </td>
</tr>