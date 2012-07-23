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

use DateTime;
use Zend\Service\Technorati;

/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
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
        $this->assertInstanceOf('DateTime', $object->getCreated());
        $this->assertEquals(new DateTime('2007-11-14 21:52:11'), $object->getCreated());
        $this->assertInstanceOf('DateTime', $object->getUpdated());
        $this->assertEquals(new DateTime('2007-11-14 21:57:59'), $object->getUpdated());

        // check weblog
        $this->assertInstanceOf('Zend\Service\Technorati\Weblog', $object->getWeblog());
        $this->assertEquals(' ScienceRoll', $object->getWeblog()->getName());
    }

    public function testTagResultSerialization()
    {
        $this->_testResultSerialization(new Technorati\TagResult($this->domElements->item(0)));
    }
}
