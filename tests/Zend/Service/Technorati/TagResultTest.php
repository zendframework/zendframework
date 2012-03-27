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

/**
 * @namespace
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
class TagResultTest extends TestCase
{
    public function setUp()
    {
        $this->domElements = self::getTestFileElementsAsDom('TestTagResultSet.xml');
    }

    public function testConstruct()
    {
        $this->_testConstruct('Zend\Service\Technorati\TagResult', array($this->domElements->item(0)));
    }

    public function testTagResult()
    {
        $object = new Technorati\TagResult($this->domElements->item(1));

        // check properties
        $this->assertInternalType('string', $object->getTitle());
        $this->assertContains('Permalink for : VerveEarth', $object->getTitle());
        $this->assertInternalType('string', $object->getExcerpt());
        $this->assertContains('VerveEarth: Locate Your Blog!', $object->getExcerpt());
        $this->assertInstanceOf('Zend\Uri\Http', $object->getPermalink());
        $this->assertEquals(\Zend\Uri\UriFactory::factory('http://scienceroll.com/2007/11/14/verveearth-locate-your-blog/'), $object->getPermalink());
        $this->assertInstanceOf('Zend\Date\Date', $object->getCreated());
        $this->assertEquals(new \Zend\Date\Date('2007-11-14 21:52:11'), $object->getCreated());
        $this->assertInstanceOf('Zend\Date\Date', $object->getUpdated());
        $this->assertEquals(new \Zend\Date\Date('2007-11-14 21:57:59'), $object->getUpdated());

        // check weblog
        $this->assertInstanceOf('Zend\Service\Technorati\Weblog', $object->getWeblog());
        $this->assertEquals(' ScienceRoll', $object->getWeblog()->getName());
    }

    public function testTagResultSerialization()
    {
        $this->_testResultSerialization(new Technorati\TagResult($this->domElements->item(0)));
    }
}
