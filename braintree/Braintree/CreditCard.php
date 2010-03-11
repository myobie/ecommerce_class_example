<?php
/**
 * Braintree CreditCard module
 *
 * @package    Braintree
 * @category   Resources
 * @copyright  2010 Braintree Payment Solutions
 */

/**
 * Creates and manages Braintree CreditCards
 *
 *
 * @package    Braintree
 * @category   Resources
 * @copyright  2010 Braintree Payment Solutions
 *
 * @property-read string $billingAddress
 * @property-read string $bin
 * @property-read string $cardType
 * @property-read string $cardholderName
 * @property-read string $createdAt
 * @property-read string $customerId
 * @property-read string $expirationDate
 * @property-read string $expirationMonth
 * @property-read string $expirationYear
 * @property-read string $last4
 * @property-read string $maskedNumber
 * @property-read string $token
 * @property-read string $updatedAt
 */
class Braintree_CreditCard extends Braintree
{
    public static function create($attribs)
    {
        Braintree_Util::verifyKeys(self::createSignature(), $attribs);
        return self::_doCreate('/payment_methods', array('credit_card' => $attribs));
    }

    /**
     * attempts the create operation assuming all data will validate
     * returns a Braintree_CreditCard object instead of a Result
     *
     * @access public
     * @param array $attribs
     * @return object
     * @throws Braintree_Exception_ValidationError
     */
    public static function createNoValidate($attribs)
    {
        $result = self::create($attribs);
        return self::returnObjectOrThrowException(__CLASS__, $result);
    }
    /**
     * create a customer from a TransparentRedirect operation
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
            '/payment_methods/all/confirm_transparent_redirect_request',
            array('id' => $params['id'])
        );
    }

    /**
     * 
     * @access public
     * @param none
     * @return string
     */
    public static function createCreditCardUrl()
    {
        return Braintree_Configuration::merchantUrl() .
                '/payment_methods/all/create_via_transparent_redirect_request';
    }

    /**
     * returns a PagedCollection of expired credit cards
     * @return object PagedCollection
     */
    public static function expired($options = null)
    {
        $page = isset($options['page']) ? $options['page'] : 1;
        $queryPath = '/payment_methods/all/expired?page=' . $page;
        $response = Braintree_Http::get($queryPath);
        $attributes = $response['paymentMethods'];
        $attributes['items'] = Braintree_Util::extractAttributeAsArray(
            $attributes,
            'creditCard'
        );
        $pager = array(
            'className' => __CLASS__,
            'classMethod' => __METHOD__,
            'methodArgs' => array()
        );

        return new Braintree_PagedCollection($attributes, $pager);
    }
    /**
     * returns a PagedCollection of credit cards expiring between start/end
     *
     * @return object PagedCollection
     */
    public static function expiringBetween($startDate, $endDate, $options = null)
    {
        $page = isset($options['page']) ? $options['page'] : 1;
        $queryPath = '/payment_methods/all/expiring?page=' . $page .
        '&start=' . date('mY', $startDate) . '&end=' . date('mY', $endDate);
        $response = Braintree_Http::get($queryPath);
        $attributes = $response['paymentMethods'];
        $attributes['items'] = Braintree_Util::extractAttributeAsArray(
            $attributes,
            'creditCard'
        );
        $pager = array(
            'className' => __CLASS__,
            'classMethod' => __METHOD__,
            'methodArgs' => array($startDate, $endDate)
        );

        return new Braintree_PagedCollection($attributes, $pager);

    }
    /**
     * find a creditcard by token
     * 
     * @access public
     * @param string $token credit card unique id
     * @return object Braintree_CreditCard
     * @throws Braintree_Exception_NotFound
     */
    public static function find($token)
    {
        self::_validateId($token);
        try {
            $response = Braintree_Http::get('/payment_methods/'.$token);
            return self::factory($response['creditCard']);
        } catch (Braintree_Exception_NotFound $e) {
            throw new Braintree_Exception_NotFound(
                'credit card with token ' . $token . ' not found'
            );
        }

    }

   /**
     * create a credit on the card for the passed transaction
     *
     * @access public
     * @param array $attribs
     * @return object Braintree_Result_Successful or Braintree_Result_Error
     */
    public static function credit($token, $transactionAttribs)
    {
        self::_validateId($token);
        return Braintree_Transaction::credit(
            array_merge(
                $transactionAttribs,
                array('paymentMethodToken' => $token)
            )
        );
    }

    /**
     * create a credit on this card, assuming validations will pass
     *
     * returns a Braintree_Transaction object on success
     *
     * @access public
     * @param array $attribs
     * @return object Braintree_Transaction
     * @throws Braintree_Exception_ValidationError
     */
    public static function creditNoValidate($token, $transactionAttribs)
    {
        $result = self::credit($token, $transactionAttribs);
        return self::returnObjectOrThrowException('Transaction', $result);
    }

    /**
     * create a new sale for the current card
     *
     * @param string $token
     * @param array $transactionAttribs
     * @return object Braintree_Result_Successful or Braintree_Result_Error
     * @see Braintree_Transaction::sale()
     */
    public static function sale($token, $transactionAttribs)
    {
        self::_validateId($token);
        return Braintree_Transaction::sale(
            array_merge(
                $transactionAttribs,
                array('paymentMethodToken' => $token)
            )
        );
    }

    /**
     * create a new sale using this card, assuming validations will pass
     *
     * returns a Braintree_Transaction object on success
     * 
     * @access public
     * @param array $transactionAttribs
     * @param string $token
     * @return object Braintree_Transaction
     * @throws Braintree_Exception_ValidationsFailed
     * @see Braintree_Transaction::sale()
     */
    public static function saleNoValidate($token, $transactionAttribs)
    {
        $result = self::sale($token, $transactionAttribs);
        return self::returnObjectOrThrowException('Transaction', $result);
    }

    /**
     * updates the creditcard record
     *
     * if calling this method in static context, $token
     * is the 2nd attribute. $token is not sent in object context.
     *
     * @access public
     * @param array $attributes
     * @param string $token (optional)
     * @return object Braintree_Result_Successful or Braintree_Result_Error
     */
    public static function update($token, $attributes)
    {
        Braintree_Util::verifyKeys(self::updateSignature(), $attributes);
        self::_validateId($token);
        return self::_doUpdate('put', '/payment_methods/' . $token, array('creditCard' => $attributes));
    }

    /**
     * update a creditcard record, assuming validations will pass
     *
     * if calling this method in static context, $token
     * is the 2nd attribute. $token is not sent in object context.
     * returns a Braintree_CreditCard object on success
     *
     * @access public
     * @param array $attributes
     * @param string $token
     * @return object Braintree_CreditCard
     * @throws Braintree_Exception_ValidationsFailed
     */
    public static function updateNoValidate($token, $attributes)
    {
        $result = self::update($token, $attributes);
        return self::returnObjectOrThrowException(__CLASS__, $result);
    }
    /**
     *
     * @access public
     * @param none
     * @return string
     */
    public static function updateCreditCardUrl()
    {
        return Braintree_Configuration::merchantUrl() .
                '/payment_methods/all/update_via_transparent_redirect_request';
    }

    /**
     * update a customer from a TransparentRedirect operation
     *
     * @access public
     * @param array $attribs
     * @return object
     */
    public static function updateFromTransparentRedirect($queryString)
    {
        $params = Braintree_TransparentRedirect::parseAndValidateQueryString(
            $queryString
        );
        return self::_doUpdate(
            'post',
            '/payment_methods/all/confirm_transparent_redirect_request',
            array('id' => $params['id'])
        );
    }

    /* instance methods */
    /**
     * returns false if default is null or false
     *
     * @return boolean
     */
    public function isDefault()
    {
        if ($this->default) {
            return true;
        }
        return false;
    }

    /**
     * checks whether the card is expired based on the current date
     *
     * @return boolean
     */
    public function isExpired()
    {
        if ($this->expirationYear == date('Y')) {
           return ($this->expirationMonth < date('m'));
        }
        return ($this->expirationYear < date('Y'));
    }

    public static function delete($token)
    {
        self::_validateId($token);
        Braintree_Http::delete('/payment_methods/' . $token);
        return new Braintree_Result_Successful();
    }

    /**
     * sets instance properties from an array of values
     *
     * @access protected
     * @param array $creditCardAttribs array of creditcard data
     * @return none
     */
    protected function _initialize($creditCardAttribs)
    {
        // set the attributes
        $this->_attributes = array_merge($this->_attributes, $creditCardAttribs);

        // map each address into its own object
        $billingAddress = isset($creditCardAttribs['billingAddress']) ?
            Braintree_Address::factory($creditCardAttribs['billingAddress']) :
            null;

        $this->_set('billingAddress', $billingAddress);
        $this->_set('expirationDate', $this->expirationMonth . '/' . $this->expirationYear);
        $this->_set('maskedNumber', $this->bin . '******' . $this->last4);
    }

    /**
     * returns false if comparing object is not a Braintree_CreditCard,
     * or is a Braintree_CreditCard with a different id
     *
     * @param object $otherCreditCard customer to compare against
     * @return boolean
     */
    public function isEqual($otherCreditCard)
    {
        return !is_a('Braintree_CreditCard', $otherCreditCard) ?
                false :
                $this->token === $otherCreditCard->token;
    }

   public static function createSignature()
   {
        return array(
            'customerId', 'cardholderName', 'cvv', 'number',
            'expirationDate', 'expirationMonth', 'expirationYear', 'token',
            array('options' => array('verifyCard')),
            array(
                'billingAddress' => array(
                    'firstName',
                    'lastName',
                    'company',
                    'countryName',
                    'extendedAddress',
                    'locality',
                    'region',
                    'postalCode',
                    'streetAddress',
                    ),
                ),
            );
   }
   public static function updateSignature()
   {
        // return all but the first element of create signature
        $signature = self::createSignature();
        return array_slice($signature, 1);
   }

   /**
     * returns private/nonexistent instance properties
     * @ignore
     * @access public
     * @param string $name property name
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
     * create a printable representation of the object as:
     * ClassName[property=value, property=value]
     * @return string
     */
    public function  __toString()
    {
        $objOutput = Braintree_Util::implodeAssociativeArray($this->_attributes);
        return __CLASS__ . '[' . $objOutput . ']';
    }
    /* private class properties  */

    /**
     * @access protected
     * @var array registry of customer data
     */
    private $_attributes = array(
        'billingAddress'     => '',
        'bin' => '',
        'cardType'  => '',
        'cardholderName' => '',
        'createdAt'   => '',
        'customerId'          => '',
        'expirationMonth'    => '',
        'expirationYear'    => '',
        'last4'  => '',
        'token'      => '',
        'updatedAt'   => '',
        ); 

    /**
     * verifies that a valid credit card token is being used
     * @param string $token
     * @throws InvalidArgumentException
     */
    private static function _validateId($token = null)
    {
        if (empty($token)) {
           throw new InvalidArgumentException(
                   'expected address id to be set'
                   );
        }
        if (!preg_match('/^[0-9A-Za-z_-]+$/', $token)) {
            throw new InvalidArgumentException(
                    $token . ' is an invalid address id.'
                    );
        }
    }
    /**
     * sets private properties
     * this function is private so values are read only
     * @access protected
     * @param string $key
     * @param mixed $value
     */
    private function _set($key, $value)
    {
        $this->_attributes[$key] = $value;
    }

     /* private class methods */

    /**
     * sends the create request to the gateway
     *
     * @param string $url
     * @param array $params
     * @return mixed
     */
    private static function _doCreate($url, $params)
    {
        $response = Braintree_Http::post($url, $params);

        return self::_verifyGatewayResponse($response);
    }

    /**
     * sends the update request to the gateway
     *
     * @param string $url
     * @param array $params
     * @return mixed
     */
    private static function _doUpdate($httpVerb, $url, $params)
    {
        $response = Braintree_Http::$httpVerb($url, $params);
        return self::_verifyGatewayResponse($response);
    }

    /**
     * generic method for validating incoming gateway responses
     *
     * creates a new Braintree_CreditCard object and encapsulates
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
        if (isset($response['creditCard'])) {
            // return a populated instance of Braintree_Address
            return new Braintree_Result_Successful(
                    self::factory($response['creditCard'])
            );
        } else if (isset($response['apiErrorResponse'])) {
            return new Braintree_Result_Error($response['apiErrorResponse']);
        } else {
            throw new Braintree_Exception_Unexpected(
            "Expected address or apiErrorResponse"
            );
        }
    }

    /**
     *  factory method: returns an instance of Braintree_CreditCard
     *  to the requesting method, with populated properties
     *
     * @return object instance of Braintree_CreditCard
     */
    public static function factory($attributes)
    {
        $instance = new self();
        $instance->_initialize($attributes);
        return $instance;
    }
}
