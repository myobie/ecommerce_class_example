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
    $base_path = realpath("$dir/../../images/uploads");
    $folder = $base_path . "/" . $this->id() . "/$type" . "/";
    return $folder . $this->g("file_name");
  }
  
  function before_save()
  {
    $attributes = $this->attributes();
    
    if (!empty($attributes["tmpfile"]) && empty($attributes["file_name"]))
    {
      $name = basename($attributes["tmpfile"]["name"]);
      $name = preg_replace("/[^a-zA-Z0-9-_.]/", "-", $name);
      $name = preg_replace("/[-]{2,}/", "-", $name);
      $this->update(array("file_name" => $name));
    }
  }
  
  function after_save()
  {
    $attributes = $this->attributes();
    
    if (! empty($attributes["tmpfile"]))
    {
      mkdir(dirname($this->path()), 0755, true);
      move_uploaded_file($attributes["tmpfile"]["tmp_name"], $this->path());
      
      foreach (self::$sizes as $name => $geometry) {
        mkdir(dirname($this->path($name)), 0755, true);
        
        $convert = "/usr/local/bin/convert " . $this->path() . 
                   " -resize \"$geometry\" " . $this->path($name);
        shell_exec($convert);
        
        $chmod = "chmod 755 " . $this->path($name);
        shell_exec($chmod);
      }
    }
  }
  
}

?>