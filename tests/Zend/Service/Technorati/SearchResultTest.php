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

use DateTime;
use Zend\Service\Technorati;

/**
 * Test helper
 */

/**
 * @see Technorati\SearchResult
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
class SearchResultTest extends TestCase
{
    public function setUp()
    {
        $this->domElements = self::getTestFileElementsAsDom('TestSearchResultSet.xml');
    }

    public function testConstruct()
    {
        $this->_testConstruct('Zend\Service\Technorati\SearchResult', array($this->domElements->item(0)));
    }

    public function testSearchResult()
    {
        $object = new Technorati\SearchResult($this->domElements->item(0));

        // check properties
        $this->assertInternalType('string', $object->getTitle());
        $this->assertContains('El SDK de Android', $object->getTitle());
        $this->assertInternalType('string', $object->getExcerpt());
        $this->assertContains('[ Android]', $object->getExcerpt());
        $this->assertInstanceOf('Zend\Uri\Http', $object->getPermalink());
        $this->assertEquals(\Zend\Uri\UriFactory::factory('http://blogs.eurielec.etsit.upm.es/miotroblog/?p=271'), $object->getPermalink());
        $this->assertInstanceOf('DateTime', $object->getCreated());
        $this->assertEquals(new DateTime('2007-11-14 22:18:04 GMT'), $object->getCreated());

        // check weblog
        $this->assertInstanceOf('Zend\Service\Technorati\Weblog', $object->getWeblog());
        $this->assertContains('Mi otro blog', $object->getWeblog()->getName());
    }

    public function testSearchResultSerialization()
    {
        $this->_testResultSerialization(new Technorati\SearchResult($this->domElements->item(0)));
    }

    public function testSearchResultSpecialEncoding()
    {
        $object = new Technorati\SearchResult($this->domElements->item(1));

        $this->assertContains('質の超濃い読者をどかんと5000件集めます', $object->getTitle());
    }
}
