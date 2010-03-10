<?php
// add ZendFramework to load path
set_include_path(
  get_include_path() . PATH_SEPARATOR .
  realpath(dirname(__FILE__)) . '/ZendFramework-1.10.2-minimal/library'
);

require_once('Braintree_PHP_1.0.1/lib/Braintree.php');

Braintree_Configuration::environment('sandbox');
Braintree_Configuration::merchantId('integration_merchant_id');
Braintree_Configuration::publicKey('integration_public_key');
Braintree_Configuration::privateKey('integration_private_key');
?>
