$(function() {
  
  // --- product image switcher ---
  
  if ($("ul.products li").length > 0) {
    $("ul.products li select[name=color_id]").change(function() {
      var product_id = $(this).parent().find("input[name=product_id]").val();
      var image_tag = $(this).parents("li").find("img.product_photo");
      
      $.get(
        "/photos/image_tag.snippet.php", 
        { product_id:product_id, color_id:this.value },
        function(data) { $(image_tag).replaceWith(data); }
      );
    });
  }
  
  // --- checkout billing info is same show/hide ---
  
  if ($("#billing_is_same").length > 0) {
    var toggle_billing_info = function() {
      if (this.checked) {
        $("#billing-info-fields").hide();
      } else {
        $("#billing-info-fields").show();
      }
    };
    
    $("#billing_is_same").change(toggle_billing_info);
    
    // check now since it could have been checked from page load
    toggle_billing_info.call($("#billing_is_same")[0]);
  }
  
});