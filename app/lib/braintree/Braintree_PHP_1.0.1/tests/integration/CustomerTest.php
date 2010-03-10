<?php
require_once realpath(dirname(__FILE__)) . '/../TestHelper.php';

class Braintree_CustomerTest extends PHPUnit_Framework_TestCase
{
    function testAll_smokeTest()
    {
        $all = Braintree_Customer::all();
        $this->assertTrue($all->totalItems() > 0);
    }

    function testCreate()
    {
        $result = Braintree_Customer::create(array(
            'firstName' => 'Mike',
            'lastName' => 'Jones',
            'company' => 'Jones Co.',
            'email' => 'mike.jones@example.com',
            'phone' => '419.555.1234',
            'fax' => '419.555.1235',
            'website' => 'http://example.com'
        ));
        $this->assertEquals(true, $result->success);
        $customer = $result->customer;
        $this->assertEquals('Mike', $customer->firstName);
        $this->assertEquals('Jones', $customer->lastName);
        $this->assertEquals('Jones Co.', $customer->company);
        $this->assertEquals('mike.jones@example.com', $customer->email);
        $this->assertEquals('419.555.1234', $customer->phone);
        $this->assertEquals('419.555.1235', $customer->fax);
        $this->assertEquals('http://example.com', $customer->website);
    }

    function testCreate_blankCustomer()
    {
        $result = Braintree_Customer::create();
        $this->assertEquals(true, $result->success);
        $this->assertNotNull($result->customer->id);

        $result = Braintree_Customer::create(array());
        $this->assertEquals(true, $result->success);
        $this->assertNotNull($result->customer->id);
    }

    function testCreate_withCreditCard()
    {
        $result = Braintree_Customer::create(array(
            'firstName' => 'Mike',
            'lastName' => 'Jones',
            'company' => 'Jones Co.',
            'email' => 'mike.jones@example.com',
            'phone' => '419.555.1234',
            'fax' => '419.555.1235',
            'website' => 'http://example.com',
            'creditCard' => array(
                'number' => '5105105105105100',
                'expirationDate' => '05/12',
                'cvv' => '123',
                'cardholderName' => 'Mike Jones'
            )
        ));
        $this->assertEquals(true, $result->success);
        $customer = $result->customer;
        $this->assertEquals('Mike', $customer->firstName);
        $this->assertEquals('Jones', $customer->lastName);
        $this->assertEquals('Jones Co.', $customer->company);
        $this->assertEquals('mike.jones@example.com', $customer->email);
        $this->assertEquals('419.555.1234', $customer->phone);
        $this->assertEquals('419.555.1235', $customer->fax);
        $this->assertEquals('http://example.com', $customer->website);
        $creditCard = $customer->creditCards[0];
        $this->assertEquals('510510', $creditCard->bin);
        $this->assertEquals('5100', $creditCard->last4);
        $this->assertEquals('Mike Jones', $creditCard->cardholderName);
        $this->assertEquals('05/2012', $creditCard->expirationDate);
        $this->assertEquals('05', $creditCard->expirationMonth);
        $this->assertEquals('2012', $creditCard->expirationYear);
    }

    function testCreate_withCreditCardAndBillingAddress()
    {
        $result = Braintree_Customer::create(array(
            'firstName' => 'Mike',
            'lastName' => 'Jones',
            'company' => 'Jones Co.',
            'email' => 'mike.jones@example.com',
            'phone' => '419.555.1234',
            'fax' => '419.555.1235',
            'website' => 'http://example.com',
            'creditCard' => array(
                'number' => '5105105105105100',
                'expirationDate' => '05/12',
                'cvv' => '123',
                'cardholderName' => 'Mike Jones',
                'billingAddress' => array(
                    'firstName' => 'Drew',
                    'lastName' => 'Smith',
                    'company' => 'Smith Co.',
                    'streetAddress' => '1 E Main St',
                    'extendedAddress' => 'Suite 101',
                    'locality' => 'Chicago',
                    'region' => 'IL',
                    'postalCode' => '60622',
                    'countryName' => 'United States of America'
                )
            )
        ));
        $this->assertEquals(true, $result->success);
        $customer = $result->customer;
        $this->assertEquals('Mike', $customer->firstName);
        $this->assertEquals('Jones', $customer->lastName);
        $this->assertEquals('Jones Co.', $customer->company);
        $this->assertEquals('mike.jones@example.com', $customer->email);
        $this->assertEquals('419.555.1234', $customer->phone);
        $this->assertEquals('419.555.1235', $customer->fax);
        $this->assertEquals('http://example.com', $customer->website);
        $creditCard = $customer->creditCards[0];
        $this->assertEquals('510510', $creditCard->bin);
        $this->assertEquals('5100', $creditCard->last4);
        $this->assertEquals('Mike Jones', $creditCard->cardholderName);
        $this->assertEquals('05/2012', $creditCard->expirationDate);
        $this->assertEquals('05', $creditCard->expirationMonth);
        $this->assertEquals('2012', $creditCard->expirationYear);
        $address = $customer->addresses[0];
        $this->assertEquals($address, $creditCard->billingAddress);
        $this->assertEquals('Drew', $address->firstName);
        $this->assertEquals('Smith', $address->lastName);
        $this->assertEquals('Smith Co.', $address->company);
        $this->assertEquals('1 E Main St', $address->streetAddress);
        $this->assertEquals('Suite 101', $address->extendedAddress);
        $this->assertEquals('Chicago', $address->locality);
        $this->assertEquals('IL', $address->region);
        $this->assertEquals('60622', $address->postalCode);
        $this->assertEquals('United States of America', $address->countryName);
    }

    function testCreate_withValidationErrors()
    {
        $result = Braintree_Customer::create(array(
            'email' => 'invalid',
            'creditCard' => array(
                'number' => 'invalid',
                'billingAddress' => array(
                    'streetAddress' => str_repeat('x', 256)
                )
            )
        ));
        $this->assertEquals(false, $result->success);
        $errors = $result->errors->forKey('customer')->onAttribute('email');
        $this->assertEquals('81604', $errors[0]->code);
        $errors = $result->errors->forKey('customer')->forKey('creditCard')->onAttribute('number');
        $this->assertEquals('81716', $errors[0]->code);
        $errors = $result->errors->forKey('customer')->forKey('creditCard')->forKey('billingAddress')->onAttribute('streetAddress');
        $this->assertEquals('81812', $errors[0]->code);
    }

    function testCreateNoValidate_returnsCustomer()
    {
        $customer = Braintree_Customer::createNoValidate(array(
            'firstName' => 'Paul',
            'lastName' => 'Martin'
        ));
        $this->assertEquals('Paul', $customer->firstName);
        $this->assertEquals('Martin', $customer->lastName);
    }

    function testCreateNoValidate_throwsIfInvalid()
    {
        $this->setExpectedException('Braintree_Exception_ValidationsFailed');
        $customer = Braintree_Customer::createNoValidate(array('email' => 'invalid'));
    }

    function testDelete_deletesTheCustomer()
    {
        $result = Braintree_Customer::create(array());
        $this->assertEquals(true, $result->success);
        Braintree_Customer::find($result->customer->id);
        Braintree_Customer::delete($result->customer->id);
        $this->setExpectedException('Braintree_Exception_NotFound');
        Braintree_Customer::find($result->customer->id);
    }

    function testFind()
    {
        $result = Braintree_Customer::create(array(
            'firstName' => 'Mike',
            'lastName' => 'Jones',
            'company' => 'Jones Co.',
            'email' => 'mike.jones@example.com',
            'phone' => '419.555.1234',
            'fax' => '419.555.1235',
            'website' => 'http://example.com'
        ));
        $this->assertEquals(true, $result->success);
        $customer = Braintree_Customer::find($result->customer->id);
        $this->assertEquals('Mike', $customer->firstName);
        $this->assertEquals('Jones', $customer->lastName);
        $this->assertEquals('Jones Co.', $customer->company);
        $this->assertEquals('mike.jones@example.com', $customer->email);
        $this->assertEquals('419.555.1234', $customer->phone);
        $this->assertEquals('419.555.1235', $customer->fax);
        $this->assertEquals('http://example.com', $customer->website);
    }

    function testFind_throwsExceptionIfNotFound()
    {
        $this->setExpectedException('Braintree_Exception_NotFound');
        Braintree_Customer::find("does-not-exist");
    }

    function testUpdate()
    {
        $result = Braintree_Customer::create(array(
            'firstName' => 'Old First',
            'lastName' => 'Old Last',
            'company' => 'Old Company',
            'email' => 'old.email@example.com',
            'phone' => 'old phone',
            'fax' => 'old fax',
            'website' => 'http://old.example.com'
        ));
        $this->assertEquals(true, $result->success);
        $customer = $result->customer;
        $updateResult = Braintree_Customer::update($customer->id, array(
            'firstName' => 'New First',
            'lastName' => 'New Last',
            'company' => 'New Company',
            'email' => 'new.email@example.com',
            'phone' => 'new phone',
            'fax' => 'new fax',
            'website' => 'http://new.example.com'
        ));
        $this->assertEquals(true, $updateResult->success);
        $this->assertEquals('New First', $updateResult->customer->firstName);
        $this->assertEquals('New Last', $updateResult->customer->lastName);
        $this->assertEquals('New Company', $updateResult->customer->company);
        $this->assertEquals('new.email@example.com', $updateResult->customer->email);
        $this->assertEquals('new phone', $updateResult->customer->phone);
        $this->assertEquals('new fax', $updateResult->customer->fax);
        $this->assertEquals('http://new.example.com', $updateResult->customer->website);
    }

    function testUpdateNoValidate()
    {
        $result = Braintree_Customer::create(array(
            'firstName' => 'Old First',
            'lastName' => 'Old Last',
            'company' => 'Old Company',
            'email' => 'old.email@example.com',
            'phone' => 'old phone',
            'fax' => 'old fax',
            'website' => 'http://old.example.com'
        ));
        $this->assertEquals(true, $result->success);
        $customer = $result->customer;
        $updated = Braintree_Customer::updateNoValidate($customer->id, array(
            'firstName' => 'New First',
            'lastName' => 'New Last',
            'company' => 'New Company',
            'email' => 'new.email@example.com',
            'phone' => 'new phone',
            'fax' => 'new fax',
            'website' => 'http://new.example.com'
        ));
        $this->assertEquals('New First', $updated->firstName);
        $this->assertEquals('New Last', $updated->lastName);
        $this->assertEquals('New Company', $updated->company);
        $this->assertEquals('new.email@example.com', $updated->email);
        $this->assertEquals('new phone', $updated->phone);
        $this->assertEquals('new fax', $updated->fax);
        $this->assertEquals('http://new.example.com', $updated->website);
    }

    function testCreateFromTransparentRedirect()
    {
        $queryString = $this->createCustomerViaTr(
            array(
                'customer' => array(
                    'first_name' => 'Joe',
                    'last_name' => 'Martin',
                    'credit_card' => array(
                        'number' => '5105105105105100',
                        'expiration_date' => '05/12'
                    )
                )
            ),
            array(
            )
        );
        $result = Braintree_Customer::createFromTransparentRedirect($queryString);
        $this->assertTrue($result->success);
        $this->assertEquals('Joe', $result->customer->firstName);
        $this->assertEquals('Martin', $result->customer->lastName);
        $creditCard = $result->customer->creditCards[0];
        $this->assertEquals('510510', $creditCard->bin);
        $this->assertEquals('5100', $creditCard->last4);
        $this->assertEquals('05/2012', $creditCard->expirationDate);
    }

    function testCreateFromTransparentRedirect_withParamsInTrData()
    {
        $queryString = $this->createCustomerViaTr(
            array(
            ),
            array(
                'customer' => array(
                    'firstName' => 'Joe',
                    'lastName' => 'Martin',
                    'creditCard' => array(
                        'number' => '5105105105105100',
                        'expirationDate' => '05/12'
                    )
                )
            )
        );
        $result = Braintree_Customer::createFromTransparentRedirect($queryString);
        $this->assertTrue($result->success);
        $this->assertEquals('Joe', $result->customer->firstName);
        $this->assertEquals('Martin', $result->customer->lastName);
        $creditCard = $result->customer->creditCards[0];
        $this->assertEquals('510510', $creditCard->bin);
        $this->assertEquals('5100', $creditCard->last4);
        $this->assertEquals('05/2012', $creditCard->expirationDate);
    }

    function testCreateFromTransparentRedirect_withValidationErrors()
    {
        $queryString = $this->createCustomerViaTr(
            array(
                'customer' => array(
                    'first_name' => str_repeat('x', 256),
                    'credit_card' => array(
                        'number' => 'invalid',
                        'expiration_date' => ''
                    )
                )
            ),
            array(
            )
        );
        $result = Braintree_Customer::createFromTransparentRedirect($queryString);
        $this->assertFalse($result->success);
        $errors = $result->errors->forKey('customer')->onAttribute('firstName');
        $this->assertEquals('81608', $errors[0]->code);
        $errors = $result->errors->forKey('customer')->forKey('creditCard')->onAttribute('number');
        $this->assertEquals('81716', $errors[0]->code);
        $errors = $result->errors->forKey('customer')->forKey('creditCard')->onAttribute('expirationDate');
        $this->assertEquals('81709', $errors[0]->code);
    }

    function testUpdateFromTransparentRedirect()
    {
        $customer = Braintree_Customer::createNoValidate();
        $queryString = $this->updateCustomerViaTr(
            array(
                'customer' => array(
                    'first_name' => 'Joe',
                    'last_name' => 'Martin',
                    'email' => 'joe.martin@example.com'
                )
            ),
            array(
                'customerId' => $customer->id
            )
        );
        $result = Braintree_Customer::updateFromTransparentRedirect($queryString);
        $this->assertTrue($result->success);
        $this->assertEquals('Joe', $result->customer->firstName);
        $this->assertEquals('Martin', $result->customer->lastName);
        $this->assertEquals('joe.martin@example.com', $result->customer->email);
    }

    function testUpdateFromTransparentRedirect_withParamsInTrData()
    {
        $customer = Braintree_Customer::createNoValidate();
        $queryString = $this->updateCustomerViaTr(
            array(
            ),
            array(
                'customerId' => $customer->id,
                'customer' => array(
                    'firstName' => 'Joe',
                    'lastName' => 'Martin',
                    'email' => 'joe.martin@example.com'
                )
            )
        );
        $result = Braintree_Customer::updateFromTransparentRedirect($queryString);
        $this->assertTrue($result->success);
        $this->assertEquals('Joe', $result->customer->firstName);
        $this->assertEquals('Martin', $result->customer->lastName);
        $this->assertEquals('joe.martin@example.com', $result->customer->email);
    }

    function testUpdateFromTransparentRedirect_withValidationErrors()
    {
        $customer = Braintree_Customer::createNoValidate();
        $queryString = $this->updateCustomerViaTr(
            array(
                'customer' => array(
                    'first_name' => str_repeat('x', 256),
                )
            ),
            array(
                'customerId' => $customer->id
            )
        );
        $result = Braintree_Customer::updateFromTransparentRedirect($queryString);
        $this->assertFalse($result->success);
        $errors = $result->errors->forKey('customer')->onAttribute('firstName');
        $this->assertEquals('81608', $errors[0]->code);
    }

    function testSale_createsASaleUsingGivenToken()
    {
        $customer = Braintree_Customer::createNoValidate(array(
            'creditCard' => array(
                'number' => '5105105105105100',
                'expirationDate' => '05/12'
            )
        ));
        $creditCard = $customer->creditCards[0];
        $result = Braintree_Customer::sale($customer->id, array(
            'amount' => '100.00'
        ));
        $this->assertTrue($result->success);
        $this->assertEquals('100.00', $result->transaction->amount);
        $this->assertEquals($customer->id, $result->transaction->customerDetails->id);
        $this->assertEquals($creditCard->token, $result->transaction->creditCardDetails->token);
    }

    function testSaleNoValidate_createsASaleUsingGivenToken()
    {
        $customer = Braintree_Customer::createNoValidate(array(
            'creditCard' => array(
                'number' => '5105105105105100',
                'expirationDate' => '05/12'
            )
        ));
        $creditCard = $customer->creditCards[0];
        $transaction = Braintree_Customer::saleNoValidate($customer->id, array(
            'amount' => '100.00'
        ));
        $this->assertEquals('100.00', $transaction->amount);
        $this->assertEquals($customer->id, $transaction->customerDetails->id);
        $this->assertEquals($creditCard->token, $transaction->creditCardDetails->token);
    }

    function testSaleNoValidate_throwsIfInvalid()
    {
        $customer = Braintree_Customer::createNoValidate(array(
            'creditCard' => array(
                'number' => '5105105105105100',
                'expirationDate' => '05/12'
            )
        ));
        $creditCard = $customer->creditCards[0];
        $this->setExpectedException('Braintree_Exception_ValidationsFailed');
        Braintree_Customer::saleNoValidate($customer->id, array(
            'amount' => 'invalid'
        ));
    }

    function testCredit_createsACreditUsingGivenCustomerId()
    {
        $customer = Braintree_Customer::createNoValidate(array(
            'creditCard' => array(
                'number' => '5105105105105100',
                'expirationDate' => '05/12'
            )
        ));
        $creditCard = $customer->creditCards[0];
        $result = Braintree_Customer::credit($customer->id, array(
            'amount' => '100.00'
        ));
        $this->assertTrue($result->success);
        $this->assertEquals('100.00', $result->transaction->amount);
        $this->assertEquals('credit', $result->transaction->type);
        $this->assertEquals($customer->id, $result->transaction->customerDetails->id);
        $this->assertEquals($creditCard->token, $result->transaction->creditCardDetails->token);
    }

    function testCreditNoValidate_createsACreditUsingGivenId()
    {
        $customer = Braintree_Customer::createNoValidate(array(
            'creditCard' => array(
                'number' => '5105105105105100',
                'expirationDate' => '05/12'
            )
        ));
        $creditCard = $customer->creditCards[0];
        $transaction = Braintree_Customer::creditNoValidate($customer->id, array(
            'amount' => '100.00'
        ));
        $this->assertEquals('100.00', $transaction->amount);
        $this->assertEquals('credit', $transaction->type);
        $this->assertEquals($customer->id, $transaction->customerDetails->id);
        $this->assertEquals($creditCard->token, $transaction->creditCardDetails->token);
    }

    function testCreditNoValidate_throwsIfInvalid()
    {
        $customer = Braintree_Customer::createNoValidate(array(
            'creditCard' => array(
                'number' => '5105105105105100',
                'expirationDate' => '05/12'
            )
        ));
        $creditCard = $customer->creditCards[0];
        $this->setExpectedException('Braintree_Exception_ValidationsFailed');
        Braintree_Customer::creditNoValidate($customer->id, array(
            'amount' => 'invalid'
        ));
    }

    function createCustomerViaTr($regularParams, $trParams)
    {
        $trData = Braintree_TransparentRedirect::createCustomerData(
            array_merge($trParams, array("redirectUrl" => "http://www.example.com"))
        );
        return Braintree_TestHelper::submitTrRequest(
            Braintree_Customer::createCustomerUrl(),
            $regularParams,
            $trData
        );
    }

    function updateCustomerViaTr($regularParams, $trParams)
    {
        $trData = Braintree_TransparentRedirect::updateCustomerData(
            array_merge($trParams, array("redirectUrl" => "http://www.example.com"))
        );
        return Braintree_TestHelper::submitTrRequest(
            Braintree_Customer::updateCustomerUrl(),
            $regularParams,
            $trData
        );
    }
}
?>

