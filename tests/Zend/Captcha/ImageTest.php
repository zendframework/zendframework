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
 * @package    Zend_Captcha
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Captcha;

/**
 * @category   Zend
 * @package    Zend_Captcha
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Captcha
 */
class ImageTest extends \PHPUnit_Framework_TestCase
{
    protected $_tmpDir;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        if (!extension_loaded('gd')) {
            $this->markTestSkipped('The GD extension is not available.');
            return;
        }
        if(!function_exists("imagepng")) {
            $this->markTestSkipped("Image CAPTCHA requires PNG support");
        }
        if(!function_exists("imageftbbox")) {
            $this->markTestSkipped("Image CAPTCHA requires FT fonts support");
        }

        if (isset($this->word)) {
            unset($this->word);
        }
        $this->testDir = $this->_getTmpDir() . '/ZF_test_images';
        if(!is_dir($this->testDir)) {
            @mkdir($this->testDir);
        }
        $this->element = new \Zend_Form_Element_Captcha('captchaI',
                    array('captcha' => array('Image',
                                             'sessionClass' => 'ZendTest\\Captcha\\TestAsset\\SessionContainer',
                                             'imgDir'       => $this->testDir,
                                             'font'         => __DIR__. '/../PDF/_fonts/Vera.ttf')
                         ));
        $this->captcha =  $this->element->getCaptcha();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        // remove chaptcha images
        foreach(new \DirectoryIterator($this->testDir) as $file) {
            if(!$file->isDot() && !$file->isDir()) {
                    unlink($file->getPathname());
            }
        }
    }

    /**
     * Determine system TMP directory
     *
     * @return string
     * @throws Zend_File_Transfer_Exception if unable to determine directory
     */
    protected function _getTmpDir()
    {
        if (null === $this->_tmpDir) {
            $this->_tmpDir = sys_get_temp_dir();
        }
        return $this->_tmpDir;
    }

    public function getView()
    {
        $view = new \Zend_View();
        $view->addHelperPath(__DIR__ . '/../../../../library/Zend/View/Helper');
        return $view;
    }

    public function testCaptchaIsRendered()
    {
        $html = $this->element->render($this->getView());
        $this->assertContains($this->element->getName(), $html);
    }

    public function testCaptchaHasIdAndInput()
    {
        $html = $this->element->render($this->getView());
        $expect = sprintf('type="hidden" name="%s\[id\]" value="%s"', $this->element->getName(), $this->captcha->getId());
        $this->assertRegexp("/<input[^>]*?$expect/", $html, $html);
        $expect = sprintf('type="text" name="%s\[input\]"', $this->element->getName());
        $this->assertRegexp("/<input[^>]*?$expect/", $html, $html);
    }

    public function testCaptchaHasImage()
    {
        $html = $this->element->render($this->getView());
        $id = $this->captcha->getId();
        $this->assertRegexp("|<img[^>]*?src=\"/images/captcha/$id.png\"|", $html, "Expected $id in HTML:\n" . $html);
    }

    public function testCaptchaHasAlt()
    {
        $html = $this->element->render($this->getView());
        $this->assertRegexp('|<img[^>]*? alt=""|', $html, "Expected alt= in HTML:\n" . $html);
        $this->captcha->setImgAlt("Test Image");
        $html = $this->element->render($this->getView());
        $this->assertRegexp('|<img[^>]*? alt="Test Image"|', $html, "Wrong alt in HTML:\n" . $html);
    }

    public function testCaptchaSetSuffix()
    {
        $this->captcha->setSuffix(".jpeg");
        $html = $this->element->render($this->getView());
        $this->assertContains(".jpeg", $html, $html);
    }

    public function testCaptchaSetImgURL()
    {
        $this->captcha->setImgURL("/some/other/URL/");
        $html = $this->element->render($this->getView());
        $this->assertContains("/some/other/URL/", $html, $html);
    }

    public function testCaptchaCreatesImage()
    {
        $this->element->render($this->getView());
        $this->assertTrue(file_exists($this->testDir."/".$this->captcha->getId().".png"));
    }

    public function testCaptchaSetExpiration()
    {
        $this->assertEquals($this->captcha->getExpiration(), 600);
        $this->captcha->setExpiration(3600);
        $this->assertEquals($this->captcha->getExpiration(), 3600);
    }

    public function testCaptchaImageCleanup()
    {
        $this->element->render($this->getView());
        $filename = $this->testDir."/".$this->captcha->getId().".png";
        $this->assertTrue(file_exists($filename));
        $this->captcha->setExpiration(1);
        $this->captcha->setGcFreq(1);
        sleep(2);
        $this->captcha->generate();
        clearstatcache();
        $this->assertFalse(file_exists($filename), "File $filename was found even after GC");
    }

    public function testGenerateReturnsId()
    {
        $id = $this->captcha->generate();
        $this->assertFalse(empty($id));
        $this->assertTrue(is_string($id));
        $this->id = $id;
    }

    public function testGetWordReturnsWord()
    {
        $this->captcha->generate();
        $word = $this->captcha->getWord();
        $this->assertFalse(empty($word));
        $this->assertTrue(is_string($word));
        $this->assertTrue(strlen($word) == 8);
        $this->word = $word;
    }

    public function testGetWordLength()
    {
        $this->captcha->setWordLen(4);
        $this->captcha->generate();
        $word = $this->captcha->getWord();
        $this->assertTrue(is_string($word));
        $this->assertTrue(strlen($word) == 4);
        $this->word = $word;
    }

    public function testAdapterElementName()
    {
        $this->assertEquals($this->captcha->getName(),
        $this->element->getName());
    }

    public function testGenerateIsRandomised()
    {
        $id1 = $this->captcha->generate();
        $word1 = $this->captcha->getWord();
        $id2 = $this->captcha->generate();
        $word2 = $this->captcha->getWord();

        $this->assertFalse(empty($id1));
        $this->assertFalse(empty($id2));
        $this->assertFalse($id1 == $id2);
        $this->assertFalse($word1 == $word2);
    }

    public function testRenderSetsValue()
    {
        $this->testCaptchaIsRendered();
        $this->assertEquals($this->captcha->getId(),
        $this->element->getValue());
    }

    public function testLabelIsNull()
    {
        $this->assertNull($this->element->getLabel());
    }

    public function testRenderInitializesSessionData()
    {
        $this->testCaptchaIsRendered();
        $session = $this->captcha->getSession();
        $this->assertEquals($this->captcha->getTimeout(), $session->setExpirationSeconds);
        $this->assertEquals(1, $session->setExpirationHops);
        $this->assertEquals($this->captcha->getWord(), $session->word);
    }

    public function testWordValidates()
    {
        $this->testCaptchaIsRendered();
        $input = array($this->element->getName() => array("id" => $this->captcha->getId(), "input" => $this->captcha->getWord()));
        $this->assertTrue($this->element->isValid("", $input));
    }

    public function testMissingNotValid()
    {
        $this->testCaptchaIsRendered();
        $this->assertFalse($this->element->isValid("", array()));
        $input = array($this->element->getName() => array("input" => "blah"));
        $this->assertFalse($this->element->isValid("", $input));
    }

    public function testWrongWordNotValid()
    {
        $this->testCaptchaIsRendered();
        $input = array($this->element->getName() => array("id" => $this->captcha->getId(), "input" => "blah"));
        $this->assertFalse($this->element->isValid("", $input));
    }

    /**
     * @group ZF-3995
     */
    public function testIsValidShouldAllowPassingArrayValueWithNoContext()
    {
        $this->testCaptchaIsRendered();
        $input = array($this->element->getName() => array("id" => $this->captcha->getId(), "input" => $this->captcha->getWord()));
        $this->assertTrue($this->element->isValid($input));
    }

    /**
     * @group ZF-3995
     */
    public function testIsValidShouldNotRequireValueToBeNestedArray()
    {
        $this->testCaptchaIsRendered();
        $input = array("id" => $this->captcha->getId(), "input" => $this->captcha->getWord());
        $this->assertTrue($this->element->isValid($input));
    }
}

