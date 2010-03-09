$(function() {
  
  if ($("ul.products li").length > 0) {
    $("ul.products li select[name=color_id]").change(function() {
      var product_id = $(this).parent().find("input[name=product_id]").val();
      var image_tag = $(this).parents("li").find("img.product_photo");
      
      $.get(
        "/photos/image_tag.php", 
        { product_id:product_id, color_id:this.value },
        function(data) { $(image_tag).replaceWith(data); }
      );
    });
  }
  
});