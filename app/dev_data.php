<?

$dir = dirname(__FILE__);
require_once "$dir/requires.php";

$db = new DB();


Page::create(array(
  "slug" => "about",
  "title" => "About Us",
  "content" => "Hello.\n\n"
));


Size::create(array("name" => "Small", "short_name" => "S"));
Size::create(array("name" => "Medium", "short_name" => "M"));
Size::create(array("name" => "Large", "short_name" => "L"));
Size::create(array("name" => "Extra Large", "short_name" => "XL"));


Color::create(array("name" => "Black", "r" => 0, "g" => 0, "b" => 0));
Color::create(array("name" => "White", "r" => 255, "g" => 255, "b" => 255));
Color::create(array("name" => "Grey", "r" => 190, "g" => 190, "b" => 190));
Color::create(array("name" => "Red", "r" => 255, "g" => 0, "b" => 0));
Color::create(array("name" => "Green", "r" => 0, "g" => 255, "b" => 0));
Color::create(array("name" => "Blue", "r" => 0, "g" => 0, "b" => 255));


Category::create(array("name" => "Men"));
Category::create(array("name" => "Women"));
Category::create(array("name" => "Kids"));


Collection::create(array("name" => "Spring 2010"));
Collection::create(array("name" => "Summer 2010"));
Collection::create(array("name" => "Fall 2010"));
Collection::create(array("name" => "Winter 2010"));


ProductType::create(array("name" => "T-Shirt"));
ProductType::create(array("name" => "Hat"));
ProductType::create(array("name" => "Pants"));


User::create(array(
  "first_name" => "Nathan",
  "last_name" => "Herald",
  "email" => "nathan@myobie.com",
  "address" => "123 Some St",
  "city" => "Reston",
  "state" => "VA",
  "postal_code" => "20190",
  "country" => "USA",
  "phone" => "+1 (555) 555-5555"
));


$p_type = ProductType::first(array("where" => "name = 'T-Shirt'"));
$p_coll = Collection::first(array("where" => "name = 'Spring 2010'"));
$p1_cat = Category::first(array("where" => "name = 'Men'"));

$p1 = Product::create(array(
  "product_type_id" => $p_type->id(),
  "collection_id" => $p_coll->id(),
  "default_price" => 2000,
  "base_sku" => "0001-M-",
  "name" => "Plain T"
));

Categorization::create(array(
  "product_id" => $p1->id(), 
  "category_id" => $p1_cat->id()
));

$p2_cat = Category::first(array("where" => "name = 'Women'"));

$p2 = Product::create(array(
  "product_type_id" => $p_type->id(),
  "collection_id" => $p_coll->id(),
  "default_price" => 2000,
  "base_sku" => "0001-W-",
  "name" => "Plain T"
));

Categorization::create(array(
  "product_id" => $p2->id(), 
  "category_id" => $p2_cat->id()
));


$colors = Color::all();
$sizes = Size::all();
$products = array($p1, $p2);

foreach ($products as $product) {
  foreach ($colors as $color) {
    $color_lowercase = strtolower($color->get_attribute("name"));
    
    foreach ($sizes as $size) {
      
      Variant::create(array(
        "product_id" => $product->id(),
        "color_id" => $color->id(),
        "size_id" => $size->id(),
        "sku" => $product->get_attribute("sku") . "$size->short_name-$color_lowercase",
        "quantity" => 100
      ));
      
    }
  }
}




?>