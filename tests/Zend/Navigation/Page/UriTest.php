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
 * @package    Zend_Navigation
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Zend/Navigation/Page/Uri.php';

/**
 * Tests the class Zend_Navigation_Page_Uri
 *
 * @category   Zend
 * @package    Zend_Navigation
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Navigation
 */
class Zend_Navigation_Page_UriTest extends PHPUnit_Framework_TestCase
{
    public function testUriOptionAsString()
    {
        $page = new Zend_Navigation_Page_Uri(array(
            'label' => 'foo',
            'uri' => '#'
        ));

        $this->assertEquals('#', $page->getUri());
    }

    public function testUriOptionAsNull()
    {
        $page = new Zend_Navigation_Page_Uri(array(
            'label' => 'foo',
            'uri' => null
        ));

        $this->assertNull($page->getUri(), 'getUri() should return null');
    }

    public function testUriOptionAsInteger()
    {
        try {
            $page = new Zend_Navigation_Page_Uri(array('uri' => 1337));
            $this->fail('An invalid \'uri\' was given, but ' .
                        'a Zend_Navigation_Exception was not thrown');
        } catch (Zend_Navigation_Exception $e) {

        }
    }

    public function testUriOptionAsObject()
    {
        try {
            $uri = new stdClass();
            $uri->foo = 'bar';

            $page = new Zend_Navigation_Page_Uri(array('uri' => $uri));
            $this->fail('An invalid \'uri\' was given, but ' .
                        'a Zend_Navigation_Exception was not thrown');
        } catch (Zend_Navigation_Exception $e) {

        }
    }

    public function testSetAndGetUri()
    {
        $page = new Zend_Navigation_Page_Uri(array(
            'label' => 'foo',
            'uri' => '#'
        ));

        $page->setUri('http://www.example.com/')->setUri('about:blank');

        $this->assertEquals('about:blank', $page->getUri());
    }

    public function testGetHref()
    {
        $uri = 'spotify:album:4YzcWwBUSzibRsqD9Sgu4A';

        $page = new Zend_Navigation_Page_Uri();
        $page->setUri($uri);

        $this->assertEquals($uri, $page->getHref());
    }
}
