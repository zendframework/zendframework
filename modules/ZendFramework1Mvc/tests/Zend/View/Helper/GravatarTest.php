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
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\View\Helper;
use Zend\Controller,
    Zend\View\PhpRenderer as View,
    Zend\View\Helper\Gravatar;


/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class GravatarTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Zend\View\Helper\Gravatar
     */
    protected $_object;

    /**
     * @var Zend\View
     */
    protected $_view;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->_object = new Gravatar();
        $this->_view = new View();
        $this->_view->doctype()->setDoctype(strtoupper("XHTML1_STRICT"));
        $this->_object->setView($this->_view);

        if( isset($_SERVER['HTTPS'])) {
            unset ($_SERVER['HTTPS']);
        }
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        unset($this->_object, $this->_view);
    }

    /**
     * Test default options.
     */
    public function testGravataXHTMLDoctype()
    {
        $this->assertRegExp('/\/>$/',
            $this->_object->__invoke('example@example.com')->__toString());
    }

    /**
     * Test if doctype is HTML
     */
    public function testGravatarHTMLDoctype()
    {
        $object = new Gravatar();
        $view   = new View();
        $view->doctype()->setDoctype(strtoupper("HTML5"));
        $object->setView($view);

        $this->assertRegExp('/[^\/]>$/',
            $this->_object->__invoke('example@example.com')->__toString());
    }

    /**
     * Test get set methods
     */
    public function testGetAndSetMethods()
    {
        $attribs = array('class' => 'gravatar', 'title' => 'avatar', 'id' => 'gravatar-1');
        $this->_object->setDefaultImg('monsterid')
             ->setImgSize(150)
             ->setSecure(true)
             ->setEmail("example@example.com")
             ->setAttribs($attribs)
             ->setRating('pg');
        $this->assertEquals("monsterid", $this->_object->getDefaultImg());
        $this->assertEquals("pg", $this->_object->getRating());
        $this->assertEquals("example@example.com", $this->_object->getEmail());
        $this->assertEquals($attribs, $this->_object->getAttribs());
        $this->assertEquals(150, $this->_object->getImgSize());
        $this->assertTrue($this->_object->getSecure());
    }

    public function tesSetDefaultImg()
    {
        $this->_object->gravatar("example@example.com");

        $img = array(
            "wavatar",
            "http://www.example.com/images/avatar/example.png",
            Gravatar::DEFAULT_MONSTERID,
        );

        foreach ($img as $value) {
            $this->_object->setDefaultImg($value);
            $this->assertEquals(urlencode($value), $this->_object->getDefaultImg());
        }
    }

    public function testSetImgSize()
    {
        $imgSizesRight = array(1, 500, "600");
        foreach ($imgSizesRight as $value) {
            $this->_object->setImgSize($value);
            $this->assertInternalType('int', $this->_object->getImgSize());
        }
    }

    public function testInvalidRatingParametr()
    {
        $ratingsWrong = array( 'a', 'cs', 456);
        $this->setExpectedException('Zend\View\Exception');
        foreach ($ratingsWrong as $value) {
            $this->_object->setRating($value);
        }
    }

    public function testSetRating()
    {
        $ratingsRight = array( 'g', 'pg', 'r', 'x', Gravatar::RATING_R);
        foreach ($ratingsRight as $value) {
            $this->_object->setRating($value);
            $this->assertEquals($value, $this->_object->getRating());
        }
    }

    public function testSetSecure()
    {
        $values = array("true", "false", "text", $this->_view, 100, true, "", null, 0, false);
        foreach ($values as $value) {
            $this->_object->setSecure($value);
            $this->assertInternalType('bool', $this->_object->getSecure());
        }
    }

    /**
     * Test SSL location
     */
    public function testHttpsSource()
    {
        $this->assertRegExp('/src="https:\/\/secure.gravatar.com\/avatar\/[a-z0-9]{32}.+"/',
                $this->_object->__invoke("example@example.com", array('secure' => true))->__toString());
    }

    /**
     * Test HTML attribs
     */
    public function testImgAttribs()
    {
        $this->assertRegExp('/class="gravatar" title="Gravatar"/',
                $this->_object->__invoke("example@example.com", array(),
                        array('class' => 'gravatar', 'title' => 'Gravatar'))
                     ->__toString()
        );
    }

    /**
     * Test gravatar's options (rating, size, default image and secure)
     */
    public function testGravatarOptions()
    {
        $this->assertRegExp('/src="http:\/\/www.gravatar.com\/avatar\/[a-z0-9]{32}\?s=125&amp;d=wavatar&amp;r=pg"/',
                $this->_object->__invoke("example@example.com",
                        array('rating' => 'pg', 'imgSize' => 125, 'defaultImg' => 'wavatar',
                            'secure' => false))
                     ->__toString()
        );
    }

    /**
     * Test auto detect location.
     * If request was made through the HTTPS protocol use secure location.
     */
    public function testAutoDetectLocation()
    {
        $values = array("on", "", 1, true);

        foreach ($values as $value) {
            $_SERVER['HTTPS'] = $value;
            $this->assertRegExp('/src="https:\/\/secure.gravatar.com\/avatar\/[a-z0-9]{32}.+"/',
                    $this->_object->__invoke("example@example.com")->__toString());
        }
    }

    /**
     * @link http://php.net/manual/en/reserved.variables.server.php Section "HTTPS"
     */
    public function testAutoDetectLocationOnIis()
    {
        $_SERVER['HTTPS'] = "off";

        $this->assertRegExp('/src="http:\/\/www.gravatar.com\/avatar\/[a-z0-9]{32}.+"/',
                $this->_object->__invoke("example@example.com")->__toString());
    }

    public function testSetAttribsWithSrcKey()
    {
        $email = 'example@example.com';
        $this->_object->setEmail($email);
        $this->_object->setAttribs(array(
            'class' => 'gravatar',
            'src'   => 'http://example.com',
            'id'    => 'gravatarID',
        ));

        $this->assertRegExp('/src="http:\/\/www.gravatar.com\/avatar\/[a-z0-9]{32}.+"/',
                            $this->_object->getImgTag());
    }

    public function testForgottenEmailParameter()
    {
        $this->assertRegExp('/(src="http:\/\/www.gravatar.com\/avatar\/[a-z0-9]{32}.+")/',
                            $this->_object->getImgTag());
    }

    public function testReturnImgTag()
    {
        $this->assertRegExp("/^<img\s.+/",
        $this->_object->__invoke("example@example.com")->__toString());
    }

    public function testReturnThisObject()
    {
        $this->assertInstanceOf('Zend\View\Helper\Gravatar', $this->_object->__invoke());
    }

    public function testInvalidKeyPassedToSetOptionsMethod()
    {
        $options = array(
            'unknown' => array('val' => 1)
        );
        $this->_object->__invoke()->setOptions($options);
    }
}
