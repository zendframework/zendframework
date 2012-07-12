<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\Technorati;

/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_Technorati
 */
class ResultTest extends TestCase
{
    /**
     * Any *Result class should be a child of Result
     * thus it's safe to test basic methods on such child class.
     */
    public function setUp()
    {
        $this->ref = new \ReflectionClass('Zend\Service\Technorati\AbstractResult');
        $this->domElements = self::getTestFileElementsAsDom('TestSearchResultSet.xml');
        $this->object = new Technorati\SearchResult($this->domElements->item(0));
        $this->objectRef = new \ReflectionObject($this->object);
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
