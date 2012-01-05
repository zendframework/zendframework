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

/**
 * Zend_Translator_Adapter_Ini
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
class IniTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $adapter = new Adapter\Ini(__DIR__ . '/_files/translation_en.ini');
        $this->assertTrue($adapter instanceof Adapter\Ini);
    }

    public function testCreate2()
    {
        $this->setExpectedException('Zend\Translator\Exception\InvalidArgumentException');
        $adapter = new Adapter\Ini(__DIR__ . '/_files/nofile.ini', 'en');
    }

    public function testCreate3()
    {
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $adapter = new Adapter\Ini(__DIR__ . '/_files/failed.ini', 'en');
        restore_error_handler();
    }

    public function testToString()
    {
        $adapter = new Adapter\Ini(__DIR__ . '/_files/translation_en.ini');
        $this->assertEquals('Ini', $adapter->toString());
    }

    public function testTranslate()
    {
        $adapter = new Adapter\Ini(__DIR__ . '/_files/translation_en.ini', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message_1'));
        $this->assertEquals('Message 1 (en)', $adapter->_('Message_1'));
        $this->assertEquals('Message_6', $adapter->translate('Message_6'));
        $this->assertEquals('Küchen Möbel (en)', $adapter->translate('Cooking_furniture'));
        if (version_compare(PHP_VERSION, '5.3.0', '<>')) {
            $this->assertEquals('Cooking furniture (en)', $adapter->translate('Küchen_Möbel'), var_export($adapter->getMessages('en'), 1));
        } else {
            $this->markTestSkipped('PHP 5.3.0 cannot utilize non-ASCII characters for INI option keys. This PHP bug has been fixed with PHP 5.3.1');
        }
    }

    public function testIsTranslated()
    {
        $adapter = new Adapter\Ini(__DIR__ . '/_files/translation_en.ini', 'en');
        $this->assertTrue($adapter->isTranslated('Message_1'));
        $this->assertFalse($adapter->isTranslated('Message_6'));
        $this->assertTrue($adapter->isTranslated('Message_1', true));
        $this->assertTrue($adapter->isTranslated('Message_1', true, 'en'));
        $this->assertFalse($adapter->isTranslated('Message_1', false, 'es'));
    }

    public function testLoadTranslationData()
    {
        $adapter = new Adapter\Ini(__DIR__ . '/_files/translation_en.ini', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message_1'));
        $this->assertEquals('Message 4 (en)', $adapter->translate('Message_4'));
        $this->assertEquals('Message_2', $adapter->translate('Message_2', 'ru'));
        $this->assertEquals('Message_1', $adapter->translate('Message_1', 'xx'));
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message_1', 'en_US'));
    }

    public function testLoadTranslationData2()
    {
        $adapter = new Adapter\Ini(__DIR__ . '/_files/translation_en.ini', 'en');
        $this->setExpectedException('Zend\Translator\Exception\InvalidArgumentException');
        $adapter->addTranslation(__DIR__ . '/_files/translation_en.ini', 'xx');
    }

    public function testLoadTranslationData3()
    {
        $adapter = new Adapter\Ini(__DIR__ . '/_files/translation_en.ini', 'en');
        $adapter->addTranslation(__DIR__ . '/_files/translation_en2.ini', 'de', array('clear' => true));
        $this->assertEquals('Nachricht 1', $adapter->translate('Message_1'));
        $this->assertEquals('Nachricht 8', $adapter->translate('Message_8'));
    }

    public function testOptions()
    {
        $adapter = new Adapter\Ini(__DIR__ . '/_files/translation_en.ini', 'en');
        $adapter->setOptions(array('testoption' => 'testkey'));
        $expected = array(
            'testoption'      => 'testkey',
            'clear'           => false,
            'content'         => __DIR__ . '/_files/translation_en.ini',
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
        $adapter = new Adapter\Ini(__DIR__ . '/_files/translation_en.ini', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message_1'));
        $this->assertEquals('Message_6', $adapter->translate('Message_6'));
        $adapter->addTranslation(__DIR__ . '/_files/translation_en2.ini', 'de', array('clear' => true));
        $this->assertEquals('Nachricht 1', $adapter->translate('Message_1'));
        $this->assertEquals('Message_4', $adapter->translate('Message_4'));
    }

    public function testLocale()
    {
        $adapter = new Adapter\Ini(__DIR__ . '/_files/translation_en.ini', 'en');
        $this->assertEquals('en', $adapter->getLocale());
        $locale = new Locale\Locale('en');
        $adapter->setLocale($locale);
        $this->assertEquals('en', $adapter->getLocale());
    }

    public function testLocale2()
    {
        $adapter = new Adapter\Ini(__DIR__ . '/_files/translation_en.ini', 'en');
        $this->setExpectedException('Zend\Translator\Exception\InvalidArgumentException');
        $adapter->setLocale('nolocale');
    }

    public function testLocale3()
    {
        $adapter = new Adapter\Ini(__DIR__ . '/_files/translation_en.ini', 'en');
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $adapter->setLocale('de');
        restore_error_handler();
        $this->assertEquals('de', $adapter->getLocale());
    }

    public function testList()
    {
        $adapter = new Adapter\Ini(__DIR__ . '/_files/translation_en.ini', 'en');
        $this->assertEquals(array('en' => 'en'), $adapter->getList());
        $adapter->addTranslation(__DIR__ . '/_files/translation_en.ini', 'de');
        $this->assertEquals(array('en' => 'en', 'de' => 'de'), $adapter->getList());
        $this->assertTrue($adapter->isAvailable('de'));
        $locale = new Locale\Locale('en');
        $this->assertTrue($adapter->isAvailable($locale));
        $this->assertFalse($adapter->isAvailable('sr'));
    }

    public function testOptionLocaleDirectory()
    {
        $adapter = new Adapter\Ini(__DIR__ . '/_files/testini', 'de_AT', array('scan' => Translator\Translator::LOCALE_DIRECTORY));
        $this->assertEquals(array('de_AT' => 'de_AT', 'en_GB' => 'en_GB'), $adapter->getList());
        $this->assertEquals('Nachricht 8', $adapter->translate('Message_8'));
    }

    public function testOptionLocaleFilename()
    {
        $adapter = new Adapter\Ini(__DIR__ . '/_files/testini', 'de_DE', array('scan' => Translator\Translator::LOCALE_FILENAME));
        $this->assertEquals(array('de_DE' => 'de_DE', 'en_US' => 'en_US'), $adapter->getList());
        $this->assertEquals('Nachricht 8', $adapter->translate('Message_8'));
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
