<?

require_once "db.php";
require_once "utils.php";

abstract class GenericModel
{
  
  public static $table_name = "";
  public static $foreign_key = "";
  public static $fields = array();
  public static $virtual_fields = array();
  public static $mass_assign = array();
  private $instance_cache = array();
  private $original_values = array();
  private $values = array();
  private $changed = array();
  private $saved = false;
  private $persisted = false;
  private $belongs_tos = array();
  
  function __construct($hash = array())
  {
    $this->setup($hash);
  }
  
  public static function clear_cache()
  {
    $klass = get_called_class();
    Cache::clear($klass);
  }
  
  public static function all($hash = array())
  {
    global $db;
    $klass = get_called_class();
    
    if (empty($hash["fields"]))
    {
      $fields = array_keys($klass::$fields);
      array_unshift($fields, "id");
    } else {
      $fields = $hash["fields"];
    }
    
    if (empty($hash["table"]))
    {
      $table = $klass::$table_name;
    } else {
      $table = $hash["table"];
    }
    
    array_unique($fields);
    
    $result = $db->select(array(
      "table" => $table,
      "fields" => $fields,
      "where" => $hash["where"],
      "order" => $hash["order"],
      "limit" => $hash["limit"]
    ));
    
    // convert to model instances
    
    $models = array();
    
    foreach ($result as $record) {
      $model = Cache::get($klass, $record["id"]);
      
      if (!$model) {
        $model = new $klass;
        $model->setup($record);
        $model->saved = true;
        $model->persisted = true;
        $model->original_values = $model->values;
        $model->changed = array();
        
        Cache::set($klass, $record["id"], $model);
      }
      
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
    
    $result = Cache::get($klass, $id);
    
    if (!$result)
      $result = $klass::first(array("where" => array("id = ?", $id)));
    
    return $result;
  }
  
  public static function random()
  {
    $klass = get_called_class();
    
    $result = $klass::first(array("order" => "RANDOM()"));
    
    return $result;
  }
  
  public static function create($hash = array())
  {
    $klass = get_called_class();
    $me = new $klass;
    // print_r($hash);
    $me->setup($hash);
    // print_r($me->changed_attributes());
    $success = $me->save();
    // var_dump($success);
    
    if ($success)
    {
      return $me;
    } else {
      return $success;
    }
  }
  
  public static function delete($hash = array())
  {
    global $db;
    $klass = get_called_class();
    
    $hash = array_merge($hash, array("table" => $klass::$table_name));
    
    $db->delete($hash);
    
    return $success;
  }
  
  public static function delete_ids($ids = array())
  {
    $klass = get_called_class();
    
    $klass::delete(array(
      "where" => array("id IN (?)", implode(",", $ids))
    ));
  }
  
  public function id()
  {
    return $this->original_values["id"];
  }
  
  public function reload()
  {
    global $db;
    $klass = get_called_class();
    
    Cache::clear($klass, $this->id());
    $this->instance_cache = array();
    
    $from_db = $klass::get($this->id());
    
    $this->setup($from_db->attributes());
    $this->saved = true;
    $this->persisted = true;
    $this->original_values = $this->values;
    $this->changed = array();
  }
  
  public function save()
  {
    global $db;
    $klass = get_called_class();
    
    $this->before_save();
    
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
      $this->original_values = $this->values;
    }
    
    $this->after_save($success);
    
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
    
    $this->before_destroy();
    
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
    
    $this->after_destroy($success);
    
    return !!$success;
  }
  
  public function all_fields()
  {
    $klass = get_called_class();
    return array_merge($klass::$fields, $klass::$virtual_fields, array("id" => "serial"));
  }
  
  public function attributes()
  {
    return $this->values;
  }
  
  public function get_attribute($key)
  {
    return $this->values[$key];
  }
  
  public function g($key)
  {
    return $this->get_attribute($key);
  }
  
  public function changed_attributes()
  {
    return $this->changed;
  }
  
  public function saved()
  {
    return $this->saved;
  }
  
  public function persisted()
  {
    return $this->persisted;
  }
  
  private function setup($hash = array())
  {
    $klass = get_called_class();
    $all_fields = $this->all_fields();
    
    $hash = array_intersect_key($hash, $all_fields);
    
    foreach ($hash as $key => $value) {
      if ($klass::$fields[$key] != "string" && $value === "")
      {
        unset($hash[$key]);
      } else {
        $this->values[$key] = $value;
      }
    }
    
    $this->changed = array_merge($this->changed, 
                                 array_intersect_key($hash, $klass::$fields));
    
    $this->saved = false;
  }
  
  // --- Relationships ---
  
  public function has_many_where($hash = array())
  {
    $klass = get_called_class();
    $key = to_underscore($klass) . "_id";
    
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
  
  public function has_many($model, $hash = array(), $reload = false)
  {
    $result = $this->instance_cache[$model];
    
    if (gettype($result) == "NULL" || $reload)
    {
      $hash = $this->has_many_where($hash);
      $result = $model::all($hash);
      $this->instance_cache[$model] = $result;
    }
    
    return $result;
  }
  
  public function belongs_to($model)
  {
    array_push($this->belongs_tos, $model);
    
    $key = to_underscore($model);
    return $model::get($this->get_attribute($key."_id"));
  }
  
  // --- Callbacks ---
  
  function before_save() {}
  function after_save($success) {}
  
  function before_destroy() {}
  function after_destroy($success) {}
  
}

?>