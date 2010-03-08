<?

require_once "db.php";

abstract class GenericModel
{
  
  public static $table_name = "";
  public static $foreign_key = "";
  public static $fields = array();
  private static $mass_assign = array();
  private $original_values = array();
  private $values = array();
  private $changed = array();
  private $saved = false;
  private $persisted = false;
  
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
      $model->saved = true;
      $model->persisted = true;
      $model->original_values = $model->values;
      $model->changed = array();
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
  
  public static function get($id)
  {
    $klass = get_called_class();
    
    $hash = array("where" => array("id = ?", $id));
    
    $result = $klass::first($hash);
    
    return $result;
  }
  
  public function id()
  {
    return $this->original_values["id"];
  }
  
  public function save()
  {
    global $db;
    $klass = get_called_class();
    
    if ($this->persisted)
    {
      // update
      $success = $db->update_row($klass::$table_name, $this->id(), $this->changed);
    } else {
      // insert
      $success = $db->insert_row($klass::$table_name, $this->changed);
      $this->values["id"] = $success;
    }
    
    if ($success)
    {
      $this->saved = true;
      $this->persisted = true;
      $this->changed = array();
      $model->original_values = $model->values;
    }
    
    return !!$success;
  }
  
  public function update($hash = array())
  {
    $this->setup($hash);
    return $this;
  }
  
  public function destroy()
  {
    global $db;
    $klass = get_called_class();
    
    if ($this->persisted)
    {
      $success = $db->delete_row($klass::$table_name, $this->id());
    } else {
      return false;
    }
    
    if ($success)
    {
      $this->saved = true;
      $this->persisted = true;
      $this->changed = array();
      $model->original_values = $model->values;
    }
    
    return !!$success;
  }
  
  public function attributes()
  {
    return $this->values;
  }
  
  public function get_attribute($key)
  {
    return $this->values[$key];
  }
  
  public function changed_attributes()
  {
    return $this->changed;
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
    
    $this->changed = array_unique(array_merge($this->changed, $hash));
    
    $this->saved = false;
  }
  
  public function has_many_where($hash = array())
  {
    $klass = get_called_class();
    $key = $klass::$foreign_key;
    
    if (empty($hash["where"]))
    {
      
      $hash["where"] = array("$key = ?", $this->id());
      
    } else {
      
      if (gettype($hash["where"]) == "array")
      {
        $hash["where"][0] = "(" . $hash["where"][0] . ") AND $key = ?";
        array_push($hash["where"], $this->id());
      } else {
        $hash["where"] = array("(" . $hash["where"] . ") AND $key = ?", $this->id());
      }
      
    }
    
    return $hash;
  }
  
  public function has_many($model, $hash = array())
  {
    $hash = $this->has_many_where($hash);
    return $model::all($hash);
  }
  
  public function belongs_to($model)
  {
    $model_klass = ucfirst($model);
    return $model::get($this->get_attribute($model."_id"));
  }
  
  
}

?>