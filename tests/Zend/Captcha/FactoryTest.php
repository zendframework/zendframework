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
 * @subpackage UnitTest
 */

namespace ZendTest\Captcha;

use DirectoryIterator;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Captcha;

/**
 * @category   Zend
 * @package    Zend_Captcha
 * @subpackage UnitTest
 */
class FactoryTest extends TestCase
{
    protected $testDir;
    protected $tmpDir;

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        // remove captcha images
        if (null !== $this->testDir) {
            foreach (new DirectoryIterator($this->testDir) as $file) {
                if (!$file->isDot() && !$file->isDir()) {
                    unlink($file->getPathname());
                }
            }
        }
    }

    /**
     * Determine system TMP directory
     *
     * @return string
     * @throws Zend_File_Transfer_Exception if unable to determine directory
     */
    protected function getTmpDir()
    {
        if (null === $this->tmpDir) {
            $this->tmpDir = sys_get_temp_dir();
        }
        return $this->tmpDir;
    }

    public function setUpImageTest()
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

        $this->testDir = $this->getTmpDir() . '/ZF_test_images';
        if (!is_dir($this->testDir)) {
            @mkdir($this->testDir);
        }
    }

    public function testCanCreateDumbCaptcha()
    {
        $captcha = Captcha\Factory::factory(array(
            'class' => 'Zend\Captcha\Dumb',
            'options' => array(
                'sessionClass' => 'ZendTest\Captcha\TestAsset\SessionContainer',
            ),
        ));
        $this->assertInstanceOf('Zend\Captcha\Dumb', $captcha);
    }

    public function testCanCreateDumbCaptchaUsingShortName()
    {
        $captcha = Captcha\Factory::factory(array(
            'class' => 'dumb',
            'options' => array(
                'sessionClass' => 'ZendTest\Captcha\TestAsset\SessionContainer',
            ),
        ));
        $this->assertInstanceOf('Zend\Captcha\Dumb', $captcha);
    }

    public function testCanCreateFigletCaptcha()
    {
        $captcha = Captcha\Factory::factory(array(
            'class' => 'Zend\Captcha\Figlet',
            'options' => array(
                'sessionClass' => 'ZendTest\Captcha\TestAsset\SessionContainer',
            ),
        ));
        $this->assertInstanceOf('Zend\Captcha\Figlet', $captcha);
    }

    public function testCanCreateFigletCaptchaUsingShortName()
    {
        $captcha = Captcha\Factory::factory(array(
            'class' => 'figlet',
            'options' => array(
                'sessionClass' => 'ZendTest\Captcha\TestAsset\SessionContainer',
            ),
        ));
        $this->assertInstanceOf('Zend\Captcha\Figlet', $captcha);
    }

    public function testCanCreateImageCaptcha()
    {
        $this->setUpImageTest();
        $captcha = Captcha\Factory::factory(array(
            'class' => 'Zend\Captcha\Image',
            'options' => array(
                'sessionClass' => 'ZendTest\Captcha\TestAsset\SessionContainer',
                'imgDir'       => $this->testDir,
                'font'         => __DIR__. '/../Pdf/_fonts/Vera.ttf',
            ),
        ));
        $this->assertInstanceOf('Zend\Captcha\Image', $captcha);
    }

    public function testCanCreateImageCaptchaUsingShortName()
    {
        $this->setUpImageTest();
        $captcha = Captcha\Factory::factory(array(
            'class' => 'image',
            'options' => array(
                'sessionClass' => 'ZendTest\Captcha\TestAsset\SessionContainer',
                'imgDir'       => $this->testDir,
                'font'         => __DIR__. '/../Pdf/_fonts/Vera.ttf',
            ),
        ));
        $this->assertInstanceOf('Zend\Captcha\Image', $captcha);
    }

    public function testCanCreateReCaptcha()
    {
        $captcha = Captcha\Factory::factory(array(
            'class' => 'Zend\Captcha\ReCaptcha',
            'options' => array(
                'sessionClass' => 'ZendTest\Captcha\TestAsset\SessionContainer',
            ),
        ));
        $this->assertInstanceOf('Zend\Captcha\ReCaptcha', $captcha);
    }

    public function testCanCreateReCaptchaUsingShortName()
    {
        $captcha = Captcha\Factory::factory(array(
            'class' => 'recaptcha',
            'options' => array(
                'sessionClass' => 'ZendTest\Captcha\TestAsset\SessionContainer',
            ),
        ));
        $this->assertInstanceOf('Zend\Captcha\ReCaptcha', $captcha);
    }

    public function testOptionsArePassedToCaptchaAdapter()
    {
        $captcha = Captcha\Factory::factory(array(
            'class'   => 'ZendTest\Captcha\TestAsset\MockCaptcha',
            'options' => array(
                'foo' => 'bar',
            ),
        ));
        $this->assertEquals(array('foo' => 'bar'), $captcha->options);
    }
}
