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
 * The interface required by all Zend_InfoCard Adapter classes to implement. It represents
 * a series of callback methods used by the component during processing of an information card
 *
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Adapter
 */
interface AdapterInterface
{
    /**
     * Store the assertion's claims in persistent storage
     *
     * @param string $assertionURI The assertion type URI
     * @param string $assertionID The specific assertion ID
     * @param array $conditions An array of claims to store associated with the assertion
     * @return bool True on success, false on failure
     */
    public function storeAssertion($assertionURI, $assertionID, $conditions);

    /**
     * Retrieve the claims of a given assertion from persistent storage
     *
     * @param string $assertionURI The assertion type URI
     * @param string $assertionID The assertion ID to retrieve
     * @return mixed False if the assertion ID was not found for that URI, or an array of
     *               conditions associated with that assertion if found in the same format
     *                  provided
     */
    public function retrieveAssertion($assertionURI, $assertionID);

    /**
     * Remove the claims of a given assertion from persistent storage
     *
     * @param string $asserionURI The assertion type URI
     * @param string $assertionID The assertion ID to remove
     * @return bool True on success, false on failure
     */
    public function removeAssertion($asserionURI, $assertionID);
}
