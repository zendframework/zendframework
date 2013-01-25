<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View\Helper;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\View\Renderer\PhpRenderer as View;
use Zend\View\Helper\Gravatar;

/**
 * @category   Zend
 * @package    Zendview
 * @subpackage UnitTests
 * @group      Zendview
 * @group      Zendview_Helper
 */
class GravatarTest extends TestCase
{
    /**
     * @var Gravatar
     */
    protected $helper;

    /**
     * @var View
     */
    protected $view;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->helper = new Gravatar();
        $this->view   = new View();
        $this->view->doctype()->setDoctype(strtoupper("XHTML1_STRICT"));
        $this->helper->setView($this->view);

        if (isset($_SERVER['HTTPS'])) {
            unset ($_SERVER['HTTPS']);
        }
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        unset($this->helper, $this->view);
    }

    /**
     * Test default options.
     */
    public function testGravatarXhtmlDoctype()
    {
        $this->assertRegExp(
            '/\/>$/',
            $this->helper->__invoke('example@example.com')->__toString()
        );
    }

    /**
     * Test if doctype is HTML
     */
    public function testGravatarHtmlDoctype()
    {
        $object = new Gravatar();
        $view   = new View();
        $view->doctype()->setDoctype(strtoupper("HTML5"));
        $object->setView($view);

        $this->assertRegExp(
            '/[^\/]>$/',
            $this->helper->__invoke('example@example.com')->__toString()
        );
    }

    /**
     * Test get set methods
     */
    public function testGetAndSetMethods()
    {
        $attribs = array('class' => 'gravatar', 'title' => 'avatar', 'id' => 'gravatar-1');
        $this->helper->setDefaultImg('monsterid')
                     ->setImgSize(150)
                     ->setSecure(true)
                     ->setEmail("example@example.com")
                     ->setAttribs($attribs)
                     ->setRating('pg');
        $this->assertEquals("monsterid", $this->helper->getDefaultImg());
        $this->assertEquals("pg", $this->helper->getRating());
        $this->assertEquals("example@example.com", $this->helper->getEmail());
        $this->assertEquals($attribs, $this->helper->getAttribs());
        $this->assertEquals(150, $this->helper->getImgSize());
        $this->assertTrue($this->helper->getSecure());
    }

    public function tesSetDefaultImg()
    {
        $this->helper->gravatar("example@example.com");

        $img = array(
            "wavatar",
            "http://www.example.com/images/avatar/example.png",
            Gravatar::DEFAULT_MONSTERID,
        );

        foreach ($img as $value) {
            $this->helper->setDefaultImg($value);
            $this->assertEquals(urlencode($value), $this->helper->getDefaultImg());
        }
    }

    public function testSetImgSize()
    {
        $imgSizesRight = array(1, 500, "600");
        foreach ($imgSizesRight as $value) {
            $this->helper->setImgSize($value);
            $this->assertInternalType('int', $this->helper->getImgSize());
        }
    }

    public function testInvalidRatingParametr()
    {
        $ratingsWrong = array( 'a', 'cs', 456);
        $this->setExpectedException('Zend\View\Exception\ExceptionInterface');
        foreach ($ratingsWrong as $value) {
            $this->helper->setRating($value);
        }
    }

    public function testSetRating()
    {
        $ratingsRight = array( 'g', 'pg', 'r', 'x', Gravatar::RATING_R);
        foreach ($ratingsRight as $value) {
            $this->helper->setRating($value);
            $this->assertEquals($value, $this->helper->getRating());
        }
    }

    public function testSetSecure()
    {
        $values = array("true", "false", "text", $this->view, 100, true, "", null, 0, false);
        foreach ($values as $value) {
            $this->helper->setSecure($value);
            $this->assertInternalType('bool', $this->helper->getSecure());
        }
    }

    /**
     * Test SSL location
     */
    public function testHttpsSource()
    {
        $this->assertRegExp(
            '#src="https://secure.gravatar.com/avatar/[a-z0-9]{32}.+"#',
            $this->helper->__invoke("example@example.com", array('secure' => true))->__toString()
        );
    }

    /**
     * Test HTML attribs
     */
    public function testImgAttribs()
    {
        $this->assertRegExp(
            '/class="gravatar" title="Gravatar"/',
            $this->helper->__invoke("example@example.com", array(), array('class' => 'gravatar', 'title' => 'Gravatar'))->__toString()
        );
    }

    /**
     * Test gravatar's options (rating, size, default image and secure)
     */
    public function testGravatarOptions()
    {
        $this->assertRegExp(
            '#src="http://www.gravatar.com/avatar/[a-z0-9]{32}\?s=125&amp;d=wavatar&amp;r=pg"#',
            $this->helper->__invoke("example@example.com", array('rating' => 'pg', 'imgSize' => 125, 'defaultImg' => 'wavatar', 'secure' => false))->__toString()
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
            $this->assertRegExp(
                '#src="https://secure.gravatar.com/avatar/[a-z0-9]{32}.+"#',
                $this->helper->__invoke("example@example.com")->__toString()
            );
        }
    }

    /**
     * @link http://php.net/manual/en/reserved.variables.server.php Section "HTTPS"
     */
    public function testAutoDetectLocationOnIis()
    {
        $_SERVER['HTTPS'] = "off";

        $this->assertRegExp(
            '/src="http:\/\/www.gravatar.com\/avatar\/[a-z0-9]{32}.+"/',
            $this->helper->__invoke("example@example.com")->__toString()
        );
    }

    public function testSetAttribsWithSrcKey()
    {
        $email = 'example@example.com';
        $this->helper->setEmail($email);
        $this->helper->setAttribs(array(
            'class' => 'gravatar',
            'src'   => 'http://example.com',
            'id'    => 'gravatarID',
        ));

        $this->assertRegExp(
            '#src="http://www.gravatar.com/avatar/[a-z0-9]{32}.+"#',
            $this->helper->getImgTag()
        );
    }

    public function testForgottenEmailParameter()
    {
        $this->assertRegExp(
            '#(src="http://www.gravatar.com/avatar/[a-z0-9]{32}.+")#',
            $this->helper->getImgTag()
        );
    }

    public function testReturnImgTag()
    {
        $this->assertRegExp(
            "/^<img\s.+/",
            $this->helper->__invoke("example@example.com")->__toString()
        );
    }

    public function testReturnThisObject()
    {
        $this->assertInstanceOf('Zend\View\Helper\Gravatar', $this->helper->__invoke());
    }

    public function testInvalidKeyPassedToSetOptionsMethod()
    {
        $options = array(
            'unknown' => array('val' => 1)
        );
        $this->helper->__invoke()->setOptions($options);
    }
}
