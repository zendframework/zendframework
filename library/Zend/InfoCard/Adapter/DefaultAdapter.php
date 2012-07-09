<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_InfoCard
 */

namespace Zend\InfoCard\Adapter;

/**
 * The default InfoCard component Adapter which serves as a pass-thru placeholder
 * for developers. Initially developed to provide a callback mechanism to store and retrieve
 * assertions as part of the validation process it can be used anytime callback facilities
 * are necessary
 *
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Adapter
 */
class DefaultAdapter implements AdapterInterface
{
    /**
     * Store the assertion (pass-thru does nothing)
     *
     * @param string $assertionURI The assertion type URI
     * @param string $assertionID The specific assertion ID
     * @param array $conditions An array of claims to store associated with the assertion
     * @return bool Always returns true (would return false on store failure)
     */
    public function storeAssertion($assertionURI, $assertionID, $conditions)
    {
        return true;
    }

    /**
     * Retrieve an assertion (pass-thru does nothing)
     *
     * @param string $assertionURI The assertion type URI
     * @param string $assertionID The assertion ID to retrieve
     * @return mixed False if the assertion ID was not found for that URI, or an array of
     *               conditions associated with that assertion if found (always returns false)
     */
    public function retrieveAssertion($assertionURI, $assertionID)
    {
        return false;
    }

    /**
     * Remove an assertion (pass-thru does nothing)
     *
     * @param string $assertionURI The assertion type URI
     * @param string $assertionID The assertion ID to remove
     * @return bool Always returns true (false on removal failure)
     */
    public function removeAssertion($assertionURI, $assertionID)
    {
        return null;
    }
}
