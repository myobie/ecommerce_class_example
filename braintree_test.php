<?

require_once "app/lib/braintree/_environment.php";

$result = Braintree_Transaction::sale(array(
    'amount' => "20.00",
    'creditCard' => array(
        'number' => "4111111111111111",
        'expirationDate' => "01/12",
        'cvv' => "123"
    ),
    'options' => array('submitForSettlement' => true)
));

print_r($result);

/*
  
  Notes:
  
  - amount must be a string
  - expritationDate must look like the above example
  - no spaces allowed in number
  
*/

?>