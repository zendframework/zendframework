<?php

namespace ZendTest\Infocard\TestAsset;
use Zend\InfoCard\Adapter;

class MockAdapter implements Adapter\AdapterInterface
{
    public function storeAssertion($assertionURI, $assertionID, $conditions)
    {
        if (empty($assertionURI) || empty($assertionID) || empty($conditions)) {
            throw new \PHPUnit_Framework_AssertionFailedError('Bad parameters to mock object');
        }
        return true;
    }

    public function retrieveAssertion($assertionURI, $assertionID)
    {
        if (empty($assertionURI) || empty($assertionID)) {
            throw new \PHPUnit_Framework_AssertionFailedError('Bad parameters to mock object');
        }
        return false;
    }

    public function removeAssertion($asserionURI, $assertionID)
    {
        if (empty($assertionURI) || empty($assertionID)) {
            throw new \PHPUnit_Framework_AssertionFailedError('Bad parameters to mock object');
        }
    }
}
