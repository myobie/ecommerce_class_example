<?php
/**
 * collection of errors enumerating all validation errors for a given request
 *
 * @package    Braintree
 * @subpackage Error
 * @copyright  2010 Braintree Payment Solutions
 */

/**
 * collection of errors enumerating all validation errors for a given request
 *
 * @package    Braintree
 * @subpackage Error
 * @copyright  2010 Braintree Payment Solutions
 *
 * @property-read array $errors
 * @property-read array $nested
 */
class Braintree_Error_ValidationErrorCollection extends Braintree_Collection
{
    private $_errors = array();
    private $_nested = array();

    public function  __construct($data)
    {
        foreach($data AS $key => $errorData)
            // map errors to new collections recursively
            if ($key == 'errors') {
                // todo: this array_key_exists check shouldn't be necessary
                // bug in xml parsing
                if (!empty($errorData)) {
                    if (!array_key_exists('errors', $errorData)) {
                        foreach ($errorData AS $error) {
                            $this->_errors[] = new Braintree_Error_Validation($error);
                        }
                    }
                }
            } else {

                $this->_nested[$key] =
                        new Braintree_Error_ValidationErrorCollection($errorData);
            }

    }

    public function deepSize()
    {
        $total = sizeof($this->_errors);
        foreach($this->_nested as $_nestedErrors)
        {
            $total = $total + $_nestedErrors->deepSize();
        }
        return $total;
    }

    public function forKey($key)
    {
        return isset($this->_nested[$key]) ? $this->_nested[$key] : null;
    }

    public function onAttribute($attribute)
    {
        $matches = array();
        foreach ($this->_errors AS $key => $error) {
           if($error->attribute == $attribute) {
               $matches[] = $error;
           }
        }
        return $matches;
    }

    /**
     *
     * @ignore
     */
    public function  __get($name)
    {
        $varName = "_$name";
        return isset($this->$varName) ? $this->$varName : null;
    }

    public function __toString()
    {
        // TODO: implement scope
       if (!empty($this->_errors)) {
           $output[] = $this->_inspect($this->_errors);
       }
       if (!empty($this->_nested)) {
           foreach ($this->_nested AS $key => $values) {
               $output[] = $this->_inspect($this->_nested);
           }
       }
       return join(', ', $output);
    }


    private function _inspect($errors, $scope = null)
    {
        $eOutput = '[' . __CLASS__ . '/errors:[';
        foreach($errors AS $error => $errorObj) {
            $outputErrs[] = "({$errorObj->error['code']} {$errorObj->error['message']})";
        }
        $eOutput .= join(', ', $outputErrs) . ']]';

        return $eOutput;
    }
}
