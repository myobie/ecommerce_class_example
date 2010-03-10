<?

date_default_timezone_set('UTC');

class Transaction
{
  
  private $info = array();
  private $card_type_info = array(
    // name => [ lengths , prefixes ]
    "amex" => array(array(15), array("34", "37")),
    "visa" => array(array(13, 16), array("4")),
    "master" => array(array(16), array("51", "52", "53", "54", "55")),
    "discover" => array(array(16), array("6011"))
  );
  private $card_type = null;
  private $is_valid = null;
  
  public $key = null;
  public $error_message = null;
  
  function __construct($info = array())
  {
    $this->info = $info;
    // remove any non-numbers
    $this->info["number"] = preg_replace('/[^0-9]/', '', $this->info["number"]);
    
    $this->info["short_year"] = substr($this->info["year"], -2, 2);
    
    if (strlen($this->info["month"]) == 1)
      $this->info["month"] = "0" . $this->info["month"];
  }
  
  public function purchase($amount)
  {
    if ($this->validate())
    {
      $dir = dirname(__FILE__);
      require_once "$dir/../lib/braintree/_environment.php";

      $result = Braintree_Transaction::sale(array(
          'amount' => number_format($amount / 100, 2, ".", ""),
          'creditCard' => array(
              'number' => $this->info["number"],
              'expirationDate' => $this->info["month"] . "/" . $this->info["short_year"],
              'cvv' => $this->info["security_code"]
          ),
          'options' => array('submitForSettlement' => true)
      ));

      if ($result->success)
      {
        $this->key = $result->transaction->id;
        return true;
      } else {
        $this->error_message = "Credit card transaction failed.";
        return false;
      }
    } else {
      return false;
    }
  }
  
  public function card_type()
  {
    if (!$this->card_type)
    {
      $this->card_type = "other";

      foreach ($this->card_type_info as $name => $attrs) {
        $lengths = $attrs[0];
        $prefixes = $attrs[1];

        $length_matches = array_search(strlen($this->info["number"]), $lengths) !== false;
        $number_prefix = substr($this->info["number"], 0, count($prefixes[0]));
        $prefix_matches = array_search($number_prefix, $prefixes) !== false;

        if ($length_matches && $prefix_matches)
          $this->card_type = $name;
      }
    }
    
    return $this->card_type;
  }
  
  public function validate()
  {
    if (!$this->is_valid)
    {
      $this->is_valid = true;
      
      if (! $this->verify_presence()) {
        $this->is_valid = false;
        $this->error_message = "Not all required credit card fields are filled in.";
        return $this->is_valid;
      }
      
      if (! $this->verify_known_card_type()) {
        $this->is_valid = false;
        $this->error_message = "Credit card doesn't appear to be one of the four types we accept or there is a typo.";
        return $this->is_valid;
      }
      
      if (! $this->verify_date()) {
        $this->is_valid = false;
        $this->error_message = "Credit card is out of date.";
        return $this->is_valid;
      }
      
      if (! $this->verify_luhn()) {
        $this->is_valid = false;
        $this->error_message = "Credit card number appears to have a typo.";
        return $this->is_valid;
      }
    }
    
    return $this->is_valid;
  }
  
  function verify_known_card_type()
  {
    return $this->card_type() != "other";
  }
  
   function verify_luhn()
   {
    // !!! this is the luhn algorithm
    // !!! I copied it from here: 
    //     http://blog.planzero.org/2009/08/luhn-modulus-implementation-php/
    
    
    // Set the string length and parity
    $number_length=strlen($this->info["number"]);
    $parity=$number_length % 2;

    // Loop through each digit and do the maths
    $total=0;
    for ($i=0; $i<$number_length; $i++) {
      $digit=$number[$i];
      // Multiply alternate digits by two
      if ($i % 2 == $parity) {
        $digit*=2;
        // If the sum is two digits, add them together (in effect)
        if ($digit > 9) {
          $digit-=9;
        }
      }
      // Total up the digits
      $total+=$digit;
    }
    
    return $total % 10 == 0;
  }
  
   function verify_date()
  {
    $greater_month = (int)($this->info["month"]) >= (int)date("n");
    $greater_year = (int)($this->info["year"]) > (int)date("Y");
    $same_year = (int)($this->info["year"]) > (int)date("Y");
    
    return $greater_year || ($same_year && $greater_month);
  }
  
   function verify_presence()
  {
    return !empty($this->info["number"]) && !empty($this->info["security_code"]) && !empty($this->info["month"]) && !empty($this->info["year"]);
  }
  
}


?>