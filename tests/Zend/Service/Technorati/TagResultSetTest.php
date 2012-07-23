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

use Zend\Service\Technorati;

/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_Technorati
 */
class TagResultSetTest extends TestCase
{
    public function setUp()
    {
        $this->dom = self::getTestFileContentAsDom('TestTagResultSet.xml');
    }

    public function testConstruct()
    {
        $this->_testConstruct('Zend\Service\Technorati\TagResultSet', array($this->dom));
    }

    public function testTagResultSet()
    {
        $object = new Technorati\TagResultSet($this->dom);

        // check counts
        $this->assertInternalType('integer', $object->totalResults());
        $this->assertEquals(3, $object->totalResults());
        $this->assertInternalType('integer', $object->totalResultsAvailable());
        $this->assertEquals(268877, $object->totalResultsAvailable());

        // check properties
        $this->assertInternalType('integer', $object->getPostsMatched());
        $this->assertEquals(268877, $object->getPostsMatched());
        $this->assertInternalType('integer', $object->getBlogsMatched());
        $this->assertEquals(1812, $object->getBlogsMatched());
    }

    public function testTagResultSetItemsInstanceOfResult()
    {
        $this->_testResultSetItemsInstanceOfResult(
                    'Zend\Service\Technorati\TagResultSet',
                    array($this->dom),
                    'Zend\Service\Technorati\TagResult');
    }

    public function testTagResultSetSerialization()
    {
        $this->_testResultSetSerialization(new Technorati\TagResultSet($this->dom));
    }
}
