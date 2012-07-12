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
class TagsResultTest extends TestCase
{
    public function setUp()
    {
        $this->domElements = self::getTestFileElementsAsDom('TestTagsResultSet.xml');
    }

    public function testConstruct()
    {
        $this->_testConstruct('Zend\Service\Technorati\TagsResult', array($this->domElements->item(0)));
    }

    public function testTagsResult()
    {
        $object = new Technorati\TagsResult($this->domElements->item(2));

        // check properties
        $this->assertInternalType('string', $object->getTag());
        $this->assertEquals('Weblog', $object->getTag());
        $this->assertInternalType('integer', $object->getPosts());
        $this->assertEquals(8336350, $object->getPosts());
    }

    public function testTagsResultSerialization()
    {
        $this->_testResultSerialization(new Technorati\TagsResult($this->domElements->item(0)));
    }

    public function testTagsResultSpecialEncoding()
    {
        $object = new Technorati\TagsResult($this->domElements->item(0));
        $this->assertEquals('練習用', $object->getTag());
        $this->assertEquals(19655999, $object->getPosts());
    }
}
