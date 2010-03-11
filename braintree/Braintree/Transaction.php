<?php
/**
 * Braintree Transaction processor
 *
 * @package    Braintree
 * @category   Resources
 * @copyright  2010 Braintree Payment Solutions
 */

/**
 * Creates and manages transactions
 *
 * At minimum, an amount, credit card number, and 
 * credit card expiration date are required. 
 * 
 * <b>Minimalistic example:</b>
 * <code>
 * Braintree_Transaction::saleNoValidate(array(
 *   'amount' => '100.00',
 *   'creditCard' => array(
 *       'number' => '5105105105105100',
 *       'expirationDate' => '05/12',
 *       ),
 *   ));
 * </code>
 *
 * <b>Full example:</b>
 * <code>
 * Braintree_Transaction::saleNoValidate(array(
 *    'amount'      => '100.00',
 *    'orderId'    => '123',
 *    'creditCard' => array(
 *         // if token is omitted, the gateway will generate a token
 *         'token' => 'credit_card_123',
 *         'number' => '5105105105105100',
 *         'expirationDate' => '05/2011',
 *         'cvv' => '123',
 *    ),
 *    'customer' => array(
 *     // if id is omitted, the gateway will generate an id
 *     'id'    => 'customer_123',
 *     'firstName' => 'Dan',
 *     'lastName' => 'Smith',
 *     'company' => 'Braintree Payment Solutions',
 *     'email' => 'dan@example.com',
 *     'phone' => '419-555-1234',
 *     'fax' => '419-555-1235',
 *     'website' => 'http://braintreepaymentsolutions.com'
 *    ),
 *    'billing'    => array(
 *      'firstName' => 'Carl',
 *      'lastName'  => 'Jones',
 *      'company'    => 'Braintree',
 *      'streetAddress' => '123 E Main St',
 *      'extendedAddress' => 'Suite 403',
 *      'locality' => 'Chicago',
 *      'region' => 'IL',
 *      'postalCode' => '60622',
 *      'countryName' => 'United States of America'
 *    ),
 *    'shipping' => array(
 *      'firstName'    => 'Andrew',
 *      'lastName'    => 'Mason',
 *      'company'    => 'Braintree',
 *      'streetAddress'    => '456 W Main St',
 *      'extendedAddress'    => 'Apt 2F',
 *      'locality'    => 'Bartlett',
 *      'region'    => 'IL',
 *      'postalCode'    => '60103',
 *      'countryName'    => 'United States of America'
 *    ),
 *    'customFields'    => array(
 *      'birthdate'    => '11/13/1954'
 *    )
 *  )
 * </code>
 *
 * <b>== Storing in the Vault ==</b>
 *
 * The customer and credit card information used for
 * a transaction can be stored in the vault by setting
 * <i>transaction[options][storeInVault]</i> to true.
 * <code>
 *   $transaction = Braintree_Transaction::saleNoValidate(array(
 *     'customer' => array(
 *       'firstName'    => 'Adam',
 *       'lastName'    => 'Williams'
 *     ),
 *     'creditCard'    => array(
 *       'number'    => '5105105105105100',
 *       'expirationDate'    => '05/2012'
 *     ),
 *     'options'    => array(
 *       'storeInVault'    => true
 *     )
 *   ));
 *
 *  echo $transaction->customerDetails->id
 *  // '865534'
 *  echo $transaction->creditCardDetails->token
 *  // '6b6m'
 * </code>
 *
 * To also store the billing address in the vault, pass the
 * <b>addBillingAddressToPaymentMethod</b> option.
 * <code>
 *   Braintree_Transaction.saleNoValidate(array(
 *    ...
 *     'options' => array(
 *       'storeInVault' => true
 *       'addBillingAddressToPaymentMethod' => true
 *     )
 *   ));
 * </code>
 *
 * <b>== Submitting for Settlement==</b>
 *
 * This can only be done when the transction's
 * status is <b>authorized</b>. If <b>amount</b> is not specified,
 * the full authorized amount will be settled. If you would like to settle
 * less than the full authorized amount, pass the desired amount.
 * You cannot settle more than the authorized amount.
 *
 * A transaction can be submitted for settlement when created by setting
 * $transaction[options][submitForSettlement] to true.
 *
 * <code>
 *   $transaction = Braintree_Transaction::saleNoValidate(array(
 *     'amount'    => '100.00',
 *     'creditCard'    => array(
 *       'number'    => '5105105105105100',
 *       'expirationDate'    => '05/2012'
 *     ),
 *     'options'    => array(
 *       'submitForSettlement'    => true
 *     )
 *   ));
 * </code>
 *
 *
 * @package    Braintree
 * @category   Resources
 * @copyright  2010 Braintree Payment Solutions
 *
 * 
 * @property-read string $avsErrorResponseCode
 * @property-read string $avsPostalCodeResponseCode
 * @property-read string $avsStreetAddressResponseCode
 * @property-read string $cvvResponseCode
 * @property-read string $id transaction id
 * @property-read string $amount transaction amount
 * @property-read object $billingDetails transaction billing address
 * @property-read string $createdAt transaction created timestamp
 * @property-read object $creditCardDetails transaction credit card info
 * @property-read object $customerDetails transaction customer info
 * @property-read array  $customFields custom fields passed with the request
 * @property-read string $processorResponseCode gateway response code
 * @property-read object $shippingDetails transaction shipping address
 * @property-read string $status transaction status
 * @property-read array  $statusHistory array of StatusDetails objects
 * @property-read string $type transaction type
 * @property-read string $updatedAt transaction updated timestamp
 * 
 */

final class Braintree_Transaction extends Braintree
{   
    /* public class methods */

    /**
     *
     * @access public
     * @param array $attribs
     * @return object
     */
    private static function create($attribs)
    {
        Braintree_Util::verifyKeys(self::createSignature(), $attribs);
        return self::_doCreate('/transactions', array('transaction' => $attribs));
    }

    /**
     *
     * @access public
     * @param array $attribs
     * @return object
     * @throws Braintree_Exception_ValidationError
     */
    private static function createNoValidate($attribs)
    {
        $result = self::create($attribs);
        return self::returnObjectOrThrowException(__CLASS__, $result);
    }
    /**
     *
     * @access public
     * @param array $attribs
     * @return object
     */
    public static function createFromTransparentRedirect($queryString)
    {
        $params = Braintree_TransparentRedirect::parseAndValidateQueryString(
                $queryString
        );
        return self::_doCreate(
                '/transactions/all/confirm_transparent_redirect_request',
                array('id' => $params['id'])
        );
    }
    /**
     *
     * @access public
     * @param none
     * @return string
     */
    public static function createTransactionUrl()
    {
        return Braintree_Configuration::merchantUrl() .
                '/transactions/all/create_via_transparent_redirect_request';
    }

    /**
     * creates a full array signature of a valid gateway request
     * @return array gateway request signature format
     */
    public static function createSignature()
    {
        return array(
                'amount', 'customerId', 'orderId', 'paymentMethodToken', 'type',
                array('creditCard'   =>
                    array('token', 'cardholderName', 'cvv', 'expirationDate', 'number'),
                ),
                array('customer'      =>
                    array(
                        'id', 'company', 'email', 'fax', 'firstName',
                        'lastName', 'phone', 'website'),
                ),
                array('billing'       =>
                    array(
                        'firstName', 'lastName', 'company', 'countryName',
                        'extendedAddress', 'locality', 'postalCode', 'region',
                        'streetAddress'),
                ),
                array('shipping'      =>
                    array(
                        'firstName', 'lastName', 'company', 'countryName',
                        'extendedAddress', 'locality', 'postalCode', 'region',
                        'streetAddress'),
                ),
                array('options'       =>
                    array(
                        'storeInVault', 'submitForSettlement',
                        'addBillingAddressToPaymentMethod'),
                ),
                array('customFields' => array('_anyKey_')
                ),
        );
    }

    /**
     *
     * @access public
     * @param array $attribs
     * @return object
     */
    public static function credit($attribs)
    {
        return self::create(array_merge($attribs, array('type' => 'credit')));
    }

    /**
     *
     * @access public
     * @param array $attribs
     * @return object
     * @throws Braintree_Exception_ValidationError
     */
    public static function creditNoValidate($attribs)
    {
        $result = self::credit($attribs);
        return self::returnObjectOrThrowException(__CLASS__, $result);
    }


    /**
     * @access public
     *
     */
    public static function find($id)
    {
        self::_validateId($id);
        try {
            $response = Braintree_Http::get('/transactions/'.$id);
            return self::factory($response['transaction']);
        } catch (Braintree_Exception_NotFound $e) {
            throw new Braintree_Exception_NotFound(
            'transaction with id ' . $id . ' not found'
            );
        }

    }
    /**
     * new sale
     * @param array $attribs
     * @return array
     */
    public static function sale($attribs)
    {
        return self::create(array_merge(array('type' => 'sale'), $attribs));
    }

    /**
     * roughly equivalent to the ruby bang method
     * @access public
     * @param array $attribs
     * @return array
     * @throws Braintree_Exception_ValidationsFailed
     */
    public static function saleNoValidate($attribs)
    {
        $result = self::sale($attribs);
        return self::returnObjectOrThrowException(__CLASS__, $result);
    }

    /**
     * Returns a PagedCollection of transactions matching the search query.
     *
     * If <b>query</b> is a string, the search will be a basic search.
     * If <b>query</b> is a hash, the search will be an advanced search.
     *
     * @param mixed $query search query
     * @param array $options options such as page number
     * @return object Braintree_PagedCollection
     * @throws InvalidArgumentException
     */
    public static function search($query, $options)
    {
        if(is_string($query)) {
            return self::_basicSearch($query, $options);
        } else if(is_array($query)) {
            return self::_advancedSearch($query, $options);
        } else {
            throw new InvalidArgumentException(
                    'expected search query to be a string or array.'
                    );
        }
    }
    /**
     * returns pagedcollection of Braintree_Transaction objects
     *
     * @param string $query
     * @param array $options
     * @return object Braintree_PagedCollection
     */
    private static function _basicSearch($query, $options)
    {
        $page = isset($options['page']) ? $options['page'] : 1;
        $queryPath = '/transactions/all/search?' .
            http_build_query(array('q' => $query, 'page' => $page));
        $response = Braintree_Http::post($queryPath);
        $attributes = $response['creditCardTransactions'];
        $attributes['items'] = Braintree_Util::extractAttributeAsArray(
                $attributes,
                'transaction'
                );
        $pager = array(
            'className' => __CLASS__,
            'classMethod' => __METHOD__,
            'methodArgs' => array($query)
            );

        return new Braintree_PagedCollection($attributes, $pager);
    }

    /**
     * returns a PagedCollection of search results
     * @param array $query
     * @param array $options
     * @return object Braintree_PagedCollection
     */
    private static function _advancedSearch($query, $options)
    {
        $page = isset($options['page']) ? $options['page'] : 1;
        $queryPath = '/transactions/advanced_search?page=?' . $page;
        $response = Braintree_Http::post($queryPath, array('search' => $query));
        $attributes = $response['creditCardTransactions'];
        $attributes['items'] = Braintree_Util::extractAttributeAsArray(
                $attributes,
                'transaction'
                );
        $pager = array(
            'className' => __CLASS__,
            'classMethod' => __METHOD__,
            'methodArgs' => array($query)
            );

        return new Braintree_PagedCollection($attributes, $pager);
    }
    /**
     * void a transaction by id
     * @param string $id transaction id
     * @return object Braintree_Result_Successful|Braintree_Result_Error
     */
    public static function void($transactionId)
    {
        self::_validateId($transactionId);

        $response = Braintree_Http::put('/transactions/'. $transactionId . '/void');
        return self::_verifyGatewayResponse($response);
    }
    /**
     *
     */
    public static function voidNoValidate($transactionId)
    {
        $result = self::void($transactionId);
        return self::returnObjectOrThrowException(__CLASS__, $result);
    }

    public static function submitForSettlement($transactionId, $amount = null)
    {
        self::_validateId($transactionId);

        $response = Braintree_Http::put(
             '/transactions/'. $transactionId . '/submit_for_settlement',
             array( 'transaction' => array( 'amount' => $amount))
        );
        return self::_verifyGatewayResponse($response);
    }
    /**
     * if calling this method in static context, id
     * is the 2nd attribute. id is not sent in object context.
     */
    public function submitForSettlementNoValidate($amount,$id = null)
    {
        $result = self::submitForSettlement($id, $amount);
        return self::returnObjectOrThrowException(__CLASS__, $result);
    }


    /* instance methods */

    /**
     * sets instance properties from an array of values
     *
     * @access protected
     * @param array $transactionAttribs array of transaction data
     * @return none
     */
    protected function _initialize($transactionAttribs)
    {
        // set the attributes
        $this->_attributes = array_merge($this->_attributes, $transactionAttribs);

        // create detail objects from the transaction
        $this->_set('creditCardDetails',
                new Braintree_Transaction_CreditCardDetails(
                $transactionAttribs['creditCard']
                )
            );
        $this->_set('customerDetails',
                new Braintree_Transaction_CustomerDetails(
                $transactionAttribs['customer']
                )
            );
        $this->_set('billingDetails',
                new Braintree_Transaction_AddressDetails(
                $transactionAttribs['billing']
                )
            );
        $this->_set('shippingDetails',
                new Braintree_Transaction_AddressDetails(
                $transactionAttribs['shipping']
                )
            );

        $statusHistory = array();
        foreach ($transactionAttribs['statusHistory'] AS $history) {
            $statusHistory[] = new Braintree_Transaction_StatusDetails($history);
        }
        $this->_set('statusHistory', $statusHistory);

    }

    /**
     * returns private/nonexistent instance properties
     * @ignore
     * @access public
     * @param var $name property name
     * @return mixed contents of instance properties
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->_attributes)) {
            return $this->_attributes[$name];
        }
        else {
            return parent::__get($name);
        }
    }
    /**
     * returns a string representation of the transaction
     * @return string
     */
    public function  __toString()
    {
        // array of attributes to print
        $display = array(
            'id', 'type', 'amount', 'status',
            'createdAt', 'creditCardDetails', 'customerDetails'
            );

        foreach ($display AS $attrib) {
            if (is_array($this->$attrib)) {
                foreach ($this->$attrib AS $obj) {
                    $pAttrib .= sprintf('%s', $obj);
                }
            } else {
                $pAttrib = $this->$attrib;
            }
            $printableAttribs[$attrib] = sprintf('%s', $pAttrib);
        }
        return __CLASS__ . '[' .
                Braintree_Util::implodeAssociativeArray($printableAttribs) .']';
    }

   
    /**
     * 
     */
    public function refund()
    {
        self::validateId($this->id);
        $response = Braintree_Http::post(
                '/transactions/'.$this->id.'/refund');
        $response = $this->_verifyGatewayResponse($response);
        $this->refundId = $response->refundId;

    }
    
    /**
     * 
     */
    public function isRefunded()
    {
        return isset($this->refundId);
    }
    
    /**
     * 
     */
    public function isEqual($otherTx)
    {
        return $this->id === $otherTx->id;
    }
    
    /**
     * 
     */
    public function vaultCreditCard()
    {
        // print($this->creditCardDetails->token);
        $token = $this->creditCardDetails->token;
        if (empty($token)) {
            return null;
        }
        else {
            return Braintree_CreditCard::find($token);
        }
    }
    /**
     * 
     */
    public function vaultCustomer()
    {
        $customerId = $this->customerDetails->id;
        if (empty($customerId)) {
            return null;
        }
        else {
            return Braintree_Customer::find($customerId);
        }
    }

    /* private class properties  */

    /**
     * @access protected
     * @var array registry of transaction data
     */
    private $_attributes = array(
        'avsErrorResponseCode'         => '',
        'avsPostalCodeResponseCode'    => '',
        'avsStreetAddressResponseCode' => '',
        'cvvResponseCode'              => '',
        'amount'                       => '',
        'billingDetails'               => '',
        'createdAt'                    => '',
        'creditCardDetails'            => '',
        'customerDetails'              => '',
        'customFields'                 => '',
        'id'                           => '',
        'processorResponseCode'        => '',
        'shippingDetails transaction'  => '',
        'status'                       => '',
        'statusHistory'                => '',
        'type'                         => '',
        'updatedAt'                    => '',
        );

    /**
     * sets private properties
     * this function is private so values are read only
     * @access protected
     * @param var $key
     * @param var $value
     */
    private function _set($key, $value)
    {
        $this->_attributes[$key] = $value;
    }

    /**
     * verifies that a valid transaction id is being used
     * @param string transaction id
     * @throws InvalidArgumentException
     */
    private static function _validateId($id = null) {
        if (empty($id)) {
           throw new InvalidArgumentException(
                   'expected transaction id to be set'
                   );
        }
        if (!preg_match('/^[0-9a-z]+$/', $id)) {
            throw new InvalidArgumentException(
                    $id . ' is an invalid transaction id.'
                    );
        }
    }


    /* private class methods */

    
    /**
     * sends the create request to the gateway
     *  
     * @param var $url
     * @param array $params
     * @return mixed
     */
    private static function _doCreate($url, $params)
    {
        $response = Braintree_Http::post($url, $params);

        return self::_verifyGatewayResponse($response);
    }

    /**
     * generic method for validating incoming gateway responses
     * 
     * creates a new Braintree_Transaction object and encapsulates
     * it inside a Braintree_Result_Successful object, or
     * encapsulates a Braintree_Errors object inside a Result_Error
     * alternatively, throws an Unexpected exception if the response is invalid.
     * 
     * @param array $response gateway response values
     * @return object Result_Successful or Result_Error
     * @throws Braintree_Exception_Unexpected
     */
    private static function _verifyGatewayResponse($response)
    {
        if (isset($response['transaction'])) {
            // return a populated instance of Braintree_Transaction
            return new Braintree_Result_Successful(
                    self::factory($response['transaction'])
            );
        } else if (isset($response['apiErrorResponse'])) {
            return new Braintree_Result_Error($response['apiErrorResponse']);
        } else {
            throw new Braintree_Exception_Unexpected(
            "Expected transaction or apiErrorResponse"
            );
        }
    }

    /**
     *  factory method: returns an instance of Braintree_Transaction
     *  to the requesting method, with populated properties
     *
     * @return object instance of Braintree_Transaction
     */
    public static function factory($attributes)
    {
        $instance = new self();
        $instance->_initialize($attributes);
        return $instance;
    }


}
