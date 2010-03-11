<?php
// add ZendFramework to load path
set_include_path(
  get_include_path() . PATH_SEPARATOR .
  realpath(dirname(__FILE__)) . '/ZendFramework-1.10.2-minimal/library'
);

require_once('Braintree_PHP_1.0.1/lib/Braintree.php');

Braintree_Configuration::environment('sandbox');
Braintree_Configuration::merchantId('xwghnxb7hfv25n84');
Braintree_Configuration::publicKey('gzdgfky7q7vc3ndy');
Braintree_Configuration::privateKey('q7qpjyxr82x7b78d');
?>
