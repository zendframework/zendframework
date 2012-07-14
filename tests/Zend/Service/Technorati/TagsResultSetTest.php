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
class TagsResultSetTest extends TestCase
{
    public function setUp()
    {
        $this->dom = self::getTestFileContentAsDom('TestTagsResultSet.xml');
        $this->object = new Technorati\TagsResultSet($this->dom);
    }

    public function testConstruct()
    {
        $this->_testConstruct('Zend\Service\Technorati\TagsResultSet', array($this->dom));
    }

    public function testTagsResultSet()
    {
        $object = new Technorati\TagsResultSet($this->dom);

        // check counts
        $this->assertInternalType('integer', $object->totalResults());
        $this->assertEquals(3, $object->totalResults());
        $this->assertInternalType('integer', $object->totalResultsAvailable());
        $this->assertEquals(3, $object->totalResultsAvailable());
    }

    public function testTagsResultSetItemsInstanceOfResult()
    {
        $this->_testResultSetItemsInstanceOfResult(
                    'Zend\Service\Technorati\TagsResultSet',
                    array($this->dom),
                    'Zend\Service\Technorati\TagsResult');
    }

    public function testTagsResultSetSerialization()
    {
        $this->_testResultSetSerialization(new Technorati\TagsResultSet($this->dom));
    }
}
