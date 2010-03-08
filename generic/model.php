<?

require_once "db.php";

class GenericModel
{
  
  public static $table_name = "";
  public static $fields = array();
  private static $mass_assign = array();
  private $values = array();
  private $saved = false;
  
  function __construct($hash = array())
  {
    $this->setup($hash);
  }
  
  public static function all($hash = array())
  {
    global $db;
    $klass = get_called_class();
    
    if (empty($hash["fields"]))
    {
      $fields = $klass::$fields;
    } else {
      $fields = $hash["fields"];
    }
    
    array_unshift($fields, "id");
    array_unique($fields);
    
    $result = $db->select(array(
      "table" => $klass::$table_name,
      "fields" => $fields,
      "where" => $hash["where"],
      "order" => $hash["order"],
      "limit" => $hash["limit"]
    ));
    
    // convert to model instances
    
    $models = array();
    
    foreach ($result as $record) {
      $model = new $klass;
      $model->setup($record);
      $model->mark_as_saved();
      array_push($models, $model);
    }
    
    return $models;
  }
  
  public static function first($hash = array())
  {
    $klass = get_called_class();
    
    $hash["limit"] = 1;
    
    $result = $klass::all($hash);
    
    if (gettype($result) == "array")
    {
      return $result[0];
    } else {
      return null;
    }
  }
  
  private static function query($string)
  {
    
  }
  
  public function save()
  {
    
  }
  
  public function update($hash = array())
  {
    
  }
  
  public function attributes()
  {
    return $this->values;
  }
  
  public function saved()
  {
    return $this->saved;
  }
  
  private function setup($hash = array())
  {
    foreach ($hash as $key => $value) {
      $this->values[$key] = $value;
    }
    
    $this->saved = false;
  }
  
  private function mark_as_saved()
  {
    $this->saved = true;
  }
  
  
}

?>