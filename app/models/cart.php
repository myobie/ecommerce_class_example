<?

$dir = dirname(__FILE__);
require_once "$dir/../generic/model.php";

class Cart extends GenericModel
{
  
  public static $table_name = "carts";
  public static $fields = array(
    "user_id" => "int"
  );
  
  function __construct($hash = array())
  {
    parent::__construct($hash);
  }
  
  function cart_items($hash = array())
  {
    return $this->has_many("CartItem", $hash);
  }
  
  function user()
  {
    return $this->belongs_to("User");
  }
  
  function quantity()
  {
    $func = create_function('$sum, $i', 'return $sum += $i->g("quantity");');
    return array_reduce($this->cart_items(), $func, 0);
  }
  
  function total()
  {
    $func = create_function('$sum, $i', 'return $sum += $i->subtotal();');
    return array_reduce($this->cart_items(), $func, 0);
  }
  
  function total_formatted()
  {
    return "$" . number_format($this->total(), 2, ".", ",");
  }
  
  function tax()
  {
    return $this->total() * 0.05;
  }
  
  function tax_formatted()
  {
    return "$" . number_format($this->tax(), 2, ".", ",");
  }
  
  function shipping()
  {
    return 5.00; // flat rate of 5 dollars
  }
  
  function shipping_formatted()
  {
    return "$" . number_format($this->shipping(), 2, ".", ",");
  }
  
  function final_total()
  {
    return $this->shipping() + $this->tax() + $this->total(); // hard coded tax number
  }
  
  function final_total_formatted()
  {
    return "$" . number_format($this->final_total(), 2, ".", ",");
  }
  
  function add($variant)
  {
    $cart_item = $this->cart_items(array("where" => array("variant_id = ?", $variant->id())));
    
    if (!$cart_item)
    {
      $cart_item = CartItem::create(array(
        "cart_id" => $this->id(),
        "quantity" => 1,
        "variant_id" => $variant->id()
      ));
    } else {
      $cart_item->update(array("quantity" => $cart_item->g("quantity") + 1));
      $cart_item->save();
    }
    
    return $cart_item;
  }
  
}

?>