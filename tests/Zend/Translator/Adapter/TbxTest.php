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
 * @package    Zend_Translator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Translator\Adapter;
use Zend\Translator\Adapter;
use Zend\Translator;
use Zend\Locale;
use Zend\Translator\Exception\InvalidFileTypeException;

/**
 * Zend_Translator_Adapter_Tbx
 */

/**
 * PHPUnit test case
 */

/**
 * @category   Zend
 * @package    Zend_Translator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Translator
 */
class TbxTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $adapter = new Adapter\Tbx(__DIR__ . '/_files/translation_en.tbx', 'en');
        $this->assertTrue($adapter instanceof Adapter\Tbx);
    }

    public function testCreate2()
    {
        $this->setExpectedException('Zend\Translator\Exception\InvalidArgumentException');
        $adapter = new Adapter\Tbx(__DIR__ . '/_files/nofile.tbx', 'en');
    }

    public function testCreate3()
    {
        $this->setExpectedException('Zend\Translator\Exception\InvalidFileTypeException');
        $adapter = new Adapter\Tbx(__DIR__ . '/_files/failed.tbx', 'en');
    }

    /**
     * @group ZF-12012
     */
    public function testErrorOnCreateIncludesFilename()
    {
        try {
            $adapter = new Adapter\Tbx(__DIR__ . '/_files/failed.tbx', 'en');
            $this->fail("exception expected");
        } catch (InvalidFileTypeException $e) {
            $this->assertContains('failed.tbx', $e->getMessage());
        }
    }
    
    public function testToString()
    {
        $adapter = new Adapter\Tbx(__DIR__ . '/_files/translation_en.tbx', 'fr');
        $this->assertEquals('Tbx', $adapter->toString());
    }

    public function testTranslate()
    {
        $adapter = new Adapter\Tbx(__DIR__ . '/_files/translation_en.tbx', 'fr');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 1 (en)', $adapter->_('Message 1'));
        $this->assertEquals('Message 6', $adapter->translate('Message 6'));
        $this->assertEquals('Küchen Möbel (en)', $adapter->translate('Cooking furniture'));
        $this->assertEquals('Cooking furniture (en)', $adapter->translate('Küchen Möbel'));
    }

    public function testIsTranslated()
    {
        $adapter = new Adapter\Tbx(__DIR__ . '/_files/translation_en.tbx', 'en');
        $this->assertTrue($adapter->isTranslated('Message 1'));
        $this->assertFalse($adapter->isTranslated('Message 6'));
        $this->assertTrue($adapter->isTranslated('Message 1', true));
        $this->assertTrue($adapter->isTranslated('Message 1', true, 'en'));
        $this->assertFalse($adapter->isTranslated('Message 1', false, 'es'));
    }

    public function testLoadTranslationData()
    {
        $adapter = new Adapter\Tbx(__DIR__ . '/_files/translation_en.tbx', 'fr');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 4 (en)', $adapter->translate('Message 4'));
        $this->assertEquals('Message 2', $adapter->translate('Message 2', 'ru'));
        $this->assertEquals('Message 1', $adapter->translate('Message 1', 'xx'));
    }

    public function testLoadTranslationData2()
    {
        $adapter = new Adapter\Tbx(__DIR__ . '/_files/translation_en.tbx', 'fr');
        $this->setExpectedException('Zend\Translator\Exception\InvalidArgumentException');
        $adapter->addTranslation(__DIR__ . '/_files/translation_en.tbx', 'xx');
    }

    public function testLoadTranslationData3()
    {
        $adapter = new Adapter\Tbx(__DIR__ . '/_files/translation_en.tbx', 'fr');
        $adapter->addTranslation(__DIR__ . '/_files/translation_en2.tbx', 'de', array('clear' => true));
        $this->assertEquals('Nachricht 1', $adapter->translate('Message 1'));
        $this->assertEquals('Nachricht 8', $adapter->translate('Message 8'));
    }

    public function testOptions()
    {
        $adapter = new Adapter\Tbx(__DIR__ . '/_files/translation_en.tbx', 'en');
        $adapter->setOptions(array('testoption' => 'testkey'));
        $expected = array(
            'testoption'      => 'testkey',
            'clear'           => false,
            'content'         => __DIR__ . '/_files/translation_en.tbx',
            'scan'            => null,
            'locale'          => 'en',
            'ignore'          => '.',
            'disableNotices'  => false,
            'log'             => false,
            'logMessage'      => 'Untranslated message within \'%locale%\': %message%',
            'logUntranslated' => false,
            'reload'          => false,
        );
        $options = $adapter->getOptions();

        foreach ($expected as $key => $value) {
            $this->assertArrayHasKey($key, $options);
            $this->assertEquals($value, $options[$key]);
        }

        $this->assertEquals('testkey', $adapter->getOptions('testoption'));
        $this->assertTrue(is_null($adapter->getOptions('nooption')));
    }

    public function testClearing()
    {
        $adapter = new Adapter\Tbx(__DIR__ . '/_files/translation_en.tbx', 'fr');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 4 (en)', $adapter->translate('Message 4'));
        $adapter->addTranslation(__DIR__ . '/_files/translation_en2.tbx', 'de', array('clear' => true));
        $this->assertEquals('Nachricht 1', $adapter->translate('Message 1'));
        $this->assertEquals('Message 4', $adapter->translate('Message 4'));
    }

    public function testLocale()
    {
        $adapter = new Adapter\Tbx(__DIR__ . '/_files/translation_en.tbx', 'fr');
        $this->assertEquals('fr', $adapter->getLocale());
        $locale = new Locale\Locale('fr');
        $adapter->setLocale($locale);
        $this->assertEquals('fr', $adapter->getLocale());
    }

    public function testLocale2()
    {
        $adapter = new Adapter\Tbx(__DIR__ . '/_files/translation_en.tbx', 'fr');
        $this->setExpectedException('Zend\Translator\Exception\InvalidArgumentException');
        $adapter->setLocale('nolocale');
    }

    public function testLocale3()
    {
        $adapter = new Adapter\Tbx(__DIR__ . '/_files/translation_en.tbx', 'fr');
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $adapter->setLocale('ru');
        restore_error_handler();
        $this->assertEquals('ru', $adapter->getLocale());
    }

    public function testList()
    {
        $adapter = new Adapter\Tbx(__DIR__ . '/_files/translation_en.tbx', 'en');
        $this->assertEquals(array('en' => 'en', 'fr' => 'fr'), $adapter->getList());
        $adapter->addTranslation(__DIR__ . '/_files/translation_en2.tbx', 'de');
        $this->assertEquals(array('en' => 'en', 'de' => 'de', 'fr' => 'fr'), $adapter->getList());
        $this->assertTrue($adapter->isAvailable('fr'));
        $locale = new Locale\Locale('en');
        $this->assertTrue( $adapter->isAvailable($locale));
        $this->assertFalse($adapter->isAvailable('sr'));
    }

    public function testOptionLocaleDirectory()
    {
        $adapter = new Adapter\Tbx(__DIR__ . '/_files/testtbx', 'de', array('scan' => Translator\Translator::LOCALE_DIRECTORY));
        $this->assertEquals(array('en' => 'en', 'fr' => 'fr', 'de' => 'de'), $adapter->getList());
        $this->assertEquals('Nachricht 1', $adapter->translate('Message 1'));
    }

    public function testOptionLocaleFilename()
    {
        $adapter = new Adapter\Tbx(__DIR__ . '/_files/testtbx', 'de', array('scan' => Translator\Translator::LOCALE_FILENAME));
        $this->assertEquals(array('en' => 'en', 'fr' => 'fr', 'de' => 'de'), $adapter->getList());
        $this->assertEquals('Nachricht 1', $adapter->translate('Message 1'));
    }

    public function testIsoEncoding()
    {
        $adapter = new Adapter\Tbx(__DIR__ . '/_files/translation_en3.tbx', 'fr');
        $this->assertEquals('Message 1 (fr)', $adapter->translate('Message 1'));

        if (PHP_OS == 'AIX') {
            return;
            // 'Charsets below are not supported on AIX';
        }

        $this->assertEquals(iconv('UTF-8', 'ISO-8859-1', 'Küchen Möbel (en)'), $adapter->translate('Cooking furniture'));
        $this->assertEquals('Cooking furniture (en)', $adapter->translate(iconv('UTF-8', 'ISO-8859-1', 'Küchen Möbel')));
    }

    public function testWithoutEncoding()
    {
        $adapter = new Adapter\Tbx(__DIR__ . '/_files/translation_withoutencoding.tbx', 'fr');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Küchen Möbel (en)', $adapter->translate('Cooking furniture'));
        $this->assertEquals('Cooking furniture (en)', $adapter->translate('Küchen Möbel'));
    }

    /**
     * Ignores a raised PHP error when in effect, but throws a flag to indicate an error occurred
     *
     * @param  integer $errno
     * @param  string  $errstr
     * @param  string  $errfile
     * @param  integer $errline
     * @param  array   $errcontext
     * @return void
     */
    public function errorHandlerIgnore($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        $this->_errorOccurred = true;
    }
}
