<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_InfoCard
 */

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
