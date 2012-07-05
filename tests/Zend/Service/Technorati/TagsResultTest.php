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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Service\Technorati;
use Zend\Service\Technorati;

/**
 * Test helper
 */

/**
 * @see Technorati\TagsResult
 */


/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
