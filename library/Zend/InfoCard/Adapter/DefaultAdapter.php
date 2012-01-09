<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\InfoCard\Adapter;

use Zend\InfoCard\Adapter;

/**
 * The default InfoCard component Adapter which serves as a pass-thru placeholder
 * for developers. Initially developed to provide a callback mechanism to store and retrieve
 * assertions as part of the validation process it can be used anytime callback facilities
 * are necessary
 *
 * @uses       \Zend\InfoCard\Adapter
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DefaultAdapter implements Adapter
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
