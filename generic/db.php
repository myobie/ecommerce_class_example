<?

class DB
{
  
  public static $host = "localhost";
  public static $name = "ecommerce_class";
  public static $username = "root";
  public static $password = "";
  private $conn;
  
  
  function __construct()
  {
    $this->conn = mysql_connect(self::$host, self::$username, self::$password);
    mysql_select_db(self::$name);
  }
  
  public function select($hash = array())
  {
    $query = "SELECT ";
    
    if (empty($hash["fields"]))
    {
      $query .= "* ";
    } else {
      foreach ($hash["fields"] as $field) {
        $field = mysql_real_escape_string($field);
        $query .= "$field, ";
      }
      $query = rtrim($query, " ,") . " ";
    }
    
    $from = mysql_real_escape_string($hash["table"]);
    $query .= "FROM $from ";
    
    $query .= $this->options_to_query($hash);
    
    return $this->query_rows($query);
  }
  
  public function update($hash = array())
  {
    $table = mysql_real_escape_string($hash["table"]);
    $query = "UPDATE $table SET ";
    
    foreach ($hash["values"] as $column => $value) {
      $column = mysql_real_escape_string($column);
      $value = mysql_real_escape_string($value);
      $query .= "$column = '$value', ";
    }
    $query = rtrim($query, " ,") . " ";
    
    $query .= $this->options_to_query($hash);
    
    $result = $this->query($query);
    
    return $result;
  }
  
  public function update_row($table, $id, $values)
  {
    return $this->update(array(
      "table" => $table,
      "values" => $values,
      "where" => array("id = ?", $id),
      "limit" => 1
    ));
  }
  
  public function insert($hash = array())
  {
    $table = mysql_real_escape_string($hash["table"]);
    $query = "INSERT INTO $table SET ";
    
    foreach ($hash["values"] as $column => $value) {
      $column = mysql_real_escape_string($column);
      $value = mysql_real_escape_string($value);
      $query .= "$column = '$value', ";
    }
    $query = rtrim($query, " ,") . " ";
    
    $query .= $this->options_to_query($hash);
    
    $result = $this->query($query);
    
    if ($result)
      $result = mysql_insert_id();
    
    return $result;
  }
  
  public function insert_row($table, $values)
  {
    return $this->insert(array(
      "table" => $table,
      "values" => $values
    ));
  }
  
  public function delete($hash = array())
  {
    $table = mysql_real_escape_string($hash["table"]);
    $query = "DELETE FROM $table ";
    
    $query .= $this->options_to_query($hash);
    
    $result = $this->query($query);
    
    return $result;
  }
  
  public function delete_row($table, $id)
  {
    return $this->delete(array(
      "table" => $table,
      "where" => array("id = ?", $id)
    ));
  }
  
  public function query($string)
  {
    // echo $string;
    // echo "\n\n";
    
    return mysql_query($string);
  }
  
  public function query_rows($string)
  {
    $result = $this->query($string);
    
    if (!$result || mysql_num_rows($result) == 0)
    {
      return array();
    }
    
    $result_array = array();
    
    while ($row = mysql_fetch_assoc($result)) 
    {
      array_push($result_array, $row);
    }
    
    return $result_array;
  }
  
  public function close()
  {
    mysql_close($this->conn);
  }
  
  private function where_substitutions($where)
  {
    if (gettype($where) == "array")
    {
      $where_string = array_shift($where);
      $substitutions = $where;
      
      foreach ($substitutions as $sub) {
        $pos = strpos($where_string, "?");
        
        if ($pos !== false)
        {
          $left = substr($where_string, 0, $pos);
          $right = substr($where_string, $pos + 1);
          
          $where_string = $left . "'" . mysql_real_escape_string($sub) . "'" . $right;
        }
      }
      
      $where = $where_string . " ";
    }
    
    return $where;
  }
  
  private function options_to_query($hash)
  {
    $query = "";
    
    if (! empty($hash["where"]))
    {
      $where = $this->where_substitutions($hash["where"]);
      $query .= "WHERE $where ";
    }
    
    if (! empty($hash["order"]))
    {
      $order = mysql_real_escape_string($hash["order"]);
      $query .= "ORDER BY $order ";
    }
    
    if (! empty($hash["limit"]))
    {
      $limit = mysql_real_escape_string($hash["limit"]);
      $query .= "LIMIT $limit ";
    }
    
    return $query;
  }
  
}


?>