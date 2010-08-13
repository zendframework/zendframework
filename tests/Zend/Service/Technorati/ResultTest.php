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
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Test helper
 */

/**
 * @see Zend_Service_Technorati_Result
 */

/**
 * @see Zend_Service_Technorati_SearchResult
 */


/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Technorati
 */
class Zend_Service_Technorati_ResultTest extends Zend_Service_Technorati_TestCase
{
    /**
     * Any *Result class should be a child of Result
     * thus it's safe to test basic methods on such child class.
     */
    public function setUp()
    {
        $this->ref = new ReflectionClass('Zend_Service_Technorati_Result');
        $this->domElements = self::getTestFileElementsAsDom('TestSearchResultSet.xml');
        $this->object = new Zend_Service_Technorati_SearchResult($this->domElements->item(0));
        $this->objectRef = new ReflectionObject($this->object);
    }

    public function testResultIsAbstract()
    {
        $this->assertTrue($this->ref->isAbstract());
    }

    /**
     * Security check
     */
    public function testResultIsParentOfThisObjectClass()
    {
        $this->assertTrue($this->objectRef->isSubclassOf($this->ref));
    }

    public function testResultSerialization()
    {
        $this->_testResultSerialization($this->object);
    }
}
