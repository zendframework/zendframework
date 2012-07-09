<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_InfoCard
 */

namespace Zend\InfoCard\XML\Assertion;

/**
 * The Interface required by any InfoCard Assertion Object implemented within the component
 *
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Xml
 */
interface AssertionInterface
{
    /**
     * Get the Assertion ID of the assertion
     *
     * @return string The Assertion ID
     */
    public function getAssertionID();

    /**
     * Return an array of attributes (claims) contained within the assertion
     *
     * @return array An array of attributes / claims within the assertion
     */
    public function getAttributes();

    /**
     * Get the Assertion URI for this type of Assertion
     *
     * @return string the Assertion URI
     */
    public function getAssertionURI();

    /**
     * Return an array of conditions which the assertions are predicated on
     *
     * @return array an array of conditions
     */
    public function getConditions();

    /**
     * Validate the conditions array returned from the getConditions() call
     *
     * @param array $conditions An array of condtions for the assertion taken from getConditions()
     * @return mixed Boolean true on success, an array of condition, error message on failure
     */
    public function validateConditions(Array $conditions);
}
