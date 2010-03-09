<?

$dir = dirname(__FILE__);
require_once "$dir/../generic/model.php";

class Photo extends GenericModel
{
  
  public static $table_name = "photos";
  public static $fields = array(
    "product_id" => "int",
    "color_id" => "int",
    "file_name" => "string"
  );
  public static $virtual_fields = array(
    "tmpfile" => array()
  );
  public static $sizes = array(
    "small" => "30x30>",
    "medium" => "300x300>"
  );
  
  function __construct($hash = array())
  {
    parent::__construct($hash);
  }
  
  function product()
  {
    return $this->belongs_to("Product");
  }
  
  function color()
  {
    return $this->belongs_to("Color");
  }
  
  function url($type = "default")
  {
    if ($this->persisted())
    {
      return "/images" . "/uploads" . "/" . 
             $this->id() . "/$type" . "/" . $this->g("file_name");
    } else {
      return "/images" . "/blanks" . "/$type" . "/blank.png";
    }
  }
  
  function path($type = "default")
  {
    $dir = dirname(__FILE__);
    $base_path = "$dir/../../images/uploads/";
    return $base_path . $this->id() . "/$type" . "/" . $this->g("file_name");
  }
  
  function before_save()
  {
    // move tmpfile into place
    
    $attributes = $this->attributes();
    
    if (! empty($attributes["tmpfile"]))
    {
      if (empty($attributes["file_name"]))
        $this->update(array("file_name" => basename($attributes["tmpfile"]["name"])));
        
      move_uploaded_file($attributes["tmpfile"]["tmp_name"], $this->path());
    }
  }
  
  function after_save()
  {
    // generate thumbnails
    
    $attributes = $this->attributes();
    
    if (! empty($attributes["tmpfile"]))
    {
      
    }
  }
  
}

?>