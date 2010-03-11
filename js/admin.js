$.fn.highlight = function() {
  var original_background = $(this).css("background-color");
  $(this).animate({ "background-color":"#FEFDB0" }, 100).animate({ "background-color":original_background }, 1000);
  return $(this);
};

$.fn.append_remove_link = function() {
  $(this).append('<td class="remove"><a href="#">Remove</a></td>');
  
  $(this).find("td.remove").click(function() {
    var id = $(this).parents("tr")[0].id.match(/^variant_([0-9]+)/)[1];
    remove_variant(id);
    return false;
  });
  
  return $(this);
};

var product_id; // will be filled in if necessary

var add_variant = function() {
  if ($("#new_color_chooser").val() && $("#new_size_chooser").val()) {
  
    var params = {
      variant: {
        product_id: product_id,
        color_id: $("#new_color_chooser").val(),
        size_id: $("#new_size_chooser").val(),
        price: $("#new_price").val(),
        sku: $("#new_sku").val(),
        quantity: $("#new_quantity").val()
      }
    };

    $.ajax({
      type: 'POST',
      url: "/admin/variants/add.php",
      data: params,
      success: function(data) { 
        get_available_sizes();
        
        $("#variants tbody tr.blank").after(data);
        $("#variants tbody tr:nth-child(3)").append_remove_link();
        $("#variants tbody tr:nth-child(3) td").highlight();
        $("#new_price").val("");
        $("#new_sku").val($("#product_base_sku").val());
        $("#new_quantity").val(1);
      }
    });
    
  } else {
    
    alert("Fill in all fields before submitting.");
    
  }
};

var remove_variant = function(id) {
  var answer = confirm("Are you sure you want to delete this inventory item? This cannot be undone.");
  
  if (answer) {
    $.ajax({
      type: 'POST',
      url: "/admin/variants/remove.php",
      data: { id: id },
      success: function(data) { get_available_sizes(); $("#variant_" + id).fadeOut(); }
    });
  }
};

var get_available_sizes = function() {
  var params = {
    product_id: product_id,
    color_id: $("#new_color_chooser").val()
  };

  $.ajax({
    type: 'GET',
    url: "/admin/sizes/available_select.snippet.php",
    data: params,
    success: function(data) { $("#new_size_chooser").replaceWith(data); }
  });
};

$(function() {
  
  if ($("#variants").length > 0) {
    
    product_id = window.location.search.match(/id=([0-9]+)/)[1];
    
    // insert a blank th
    $("#variants thead tr").append("<th>&nbsp;</th>");
    
    // insert the add button and insert a blank row for space
    $("#variants tbody tr:first-child").append("<td><button>Add</button></td>").after('<tr class="blank"><td colspan="6">&nbsp;</td></tr>');
    
    // add a remove link for each variant
    $("#variants tbody tr:gt(1)").append_remove_link();
    
    $("#variants tbody tr.add input, #variants tbody tr.add select").keypress(function(event) {
      if ((event.keyCode || event.which) == 13) {
        add_variant();
        return false;
      }
    });
    
    $("#variants tbody tr.add button").click(function(event) {
      add_variant();
      return false;
    });
    
    $("#new_color_chooser").change(get_available_sizes);
    
    get_available_sizes(); // start out with the correct sizes for the chosen color
    // this wouldn't be necessary if firefox didn't leave form data in after a refresh
  }
  
});