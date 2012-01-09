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
 * @category   Zend
 * @package    Zend_Translator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Translator
 */
class GettextTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (Adapter\Gettext::hasCache()) {
            Adapter\Gettext::removeCache();
        }
    }

    public function testCreate()
    {
        $adapter = new Adapter\Gettext(__DIR__ . '/_files/translation_en.mo');
        $this->assertTrue($adapter instanceof Adapter\Gettext);
    }

    public function testCreate2()
    {
        $this->setExpectedException('Zend\Translator\Exception\InvalidArgumentException');
        $adapter = new Adapter\Gettext(__DIR__ . '/_files/nofile.mo', 'en');
    }

    public function testCreate3()
    {
        $this->setExpectedException('Zend\Translator\Exception\InvalidFileTypeException');
        $adapter = new Adapter\Gettext(__DIR__ . '/_files/failed.mo', 'en');
    }

    public function testToString()
    {
        $adapter = new Adapter\Gettext(__DIR__ . '/_files/translation_en.mo');
        $this->assertEquals('Gettext', $adapter->toString());
    }

    public function testTranslate()
    {
        $adapter = new Adapter\Gettext(__DIR__ . '/_files/translation_en.mo', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 1 (en)', $adapter->_('Message 1'));
        $this->assertEquals('Message 6', $adapter->translate('Message 6'));
        $this->assertEquals('Küchen Möbel (en)', $adapter->translate('Cooking furniture'));
        $this->assertEquals('Cooking furniture (en)', $adapter->translate('Küchen Möbel'));
    }

    public function testIsTranslated()
    {
        $adapter = new Adapter\Gettext(__DIR__ . '/_files/translation_en.mo', 'en');
        $this->assertTrue($adapter->isTranslated('Message 1'));
        $this->assertFalse($adapter->isTranslated('Message 6'));
        $this->assertTrue($adapter->isTranslated('Message 1', true));
        $this->assertTrue($adapter->isTranslated('Message 1', true, 'en'));
        $this->assertFalse($adapter->isTranslated('Message 1', false, 'es'));
    }

    public function testLoadTranslationData()
    {
        $adapter = new Adapter\Gettext(__DIR__ . '/_files/translation_en.mo', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 4 (en)', $adapter->translate('Message 4'));
        $this->assertEquals('Message 2', $adapter->translate('Message 2', 'ru'));
        $this->assertEquals('Message 1', $adapter->translate('Message 1', 'xx'));
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1', 'en_US'));
    }

    public function testLoadTranslationData2()
    {
        $adapter = new Adapter\Gettext(__DIR__ . '/_files/translation_en.mo', 'en');
        $this->setExpectedException('Zend\Translator\Exception\InvalidArgumentException');
        $adapter->addTranslation(__DIR__ . '/_files/translation_en2.mo', 'xx');
    }

    public function testLoadTranslationData3()
    {
        $adapter = new Adapter\Gettext(__DIR__ . '/_files/translation_en.mo', 'en');
        $adapter->addTranslation(__DIR__ . '/_files/translation_en2.mo', 'de', array('clear' => true));
        $this->assertEquals('Nachricht 1', $adapter->translate('Message 1'));
        $this->assertEquals('Nachricht 8', $adapter->translate('Message 8'));
    }

    public function testOptions()
    {
        $adapter = new Adapter\Gettext(__DIR__ . '/_files/translation_en.mo', 'en');
        $adapter->setOptions(array('testoption' => 'testkey'));
        $expected = array(
            'testoption'      => 'testkey',
            'clear'           => false,
            'content'         => __DIR__ . '/_files/translation_en.mo',
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
        $adapter = new Adapter\Gettext(__DIR__ . '/_files/translation_en.mo', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 6', $adapter->translate('Message 6'));
        $adapter->addTranslation(__DIR__ . '/_files/translation_en2.mo', 'de', array('clear' => true));
        $this->assertEquals('Nachricht 1', $adapter->translate('Message 1'));
        $this->assertEquals('Message 4', $adapter->translate('Message 4'));
    }

    public function testLocale()
    {
        $adapter = new Adapter\Gettext(__DIR__ . '/_files/translation_en.mo', 'en');
        $this->assertEquals('en', $adapter->getLocale());
        $locale = new Locale\Locale('en');
        $adapter->setLocale($locale);
        $this->assertEquals('en', $adapter->getLocale());
    }

    public function testLocale2()
    {
        $adapter = new Adapter\Gettext(__DIR__ . '/_files/translation_en.mo', 'en');
        $this->setExpectedException('Zend\Translator\Exception\InvalidArgumentException');
        $adapter->setLocale('nolocale');
    }

    public function testLocale3()
    {
        $adapter = new Adapter\Gettext(__DIR__ . '/_files/translation_en.mo', 'en');
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $adapter->setLocale('de');
        restore_error_handler();
        $this->assertEquals('de', $adapter->getLocale());
    }

    public function testList()
    {
        $adapter = new Adapter\Gettext(__DIR__ . '/_files/translation_en.mo', 'en');
        $this->assertEquals(array('en' => 'en'), $adapter->getList());
        $adapter->addTranslation(__DIR__ . '/_files/translation_en.mo', 'de');
        $this->assertEquals(array('en' => 'en', 'de' => 'de'), $adapter->getList());
        $this->assertTrue($adapter->isAvailable('de'));
        $locale = new Locale\Locale('en');
        $this->assertTrue($adapter->isAvailable($locale));
        $this->assertFalse($adapter->isAvailable('sr'));
    }

    public function testOptionLocaleDirectory()
    {
        $adapter = new Adapter\Gettext(__DIR__ . '/_files/testmo/', 'de_AT', array('scan' => Translator\Translator::LOCALE_DIRECTORY));
        $this->assertEquals(array('de_AT' => 'de_AT', 'en_GB' => 'en_GB'), $adapter->getList());
        $this->assertEquals('Nachricht 8', $adapter->translate('Message 8'));
    }

    public function testOptionLocaleFilename()
    {
        $adapter = new Adapter\Gettext(__DIR__ . '/_files/testmo/', 'de_DE', array('scan' => Translator\Translator::LOCALE_FILENAME));
        $this->assertEquals(array('de_DE' => 'de_DE', 'en_US' => 'en_US'), $adapter->getList());
        $this->assertEquals('Nachricht 8', $adapter->translate('Message 8'));
    }

    public function testBigEndian()
    {
        $adapter = new Adapter\Gettext(__DIR__ . '/_files/translation_bigendian.mo', 'sr');
        $this->assertEquals('Informacje', $adapter->translate('Informacje'));
    }

    public function testAdapterInfo()
    {
        $adapter = new Adapter\Gettext(__DIR__ . '/_files/translation_en.mo');
        $this->assertEquals('', $adapter->translate(''));
        $info = $adapter->getAdapterInfo();
        $this->assertContains('Last-Translator: Thomas Weidner <thomas.weidner@voxtronic.com>', $info[__DIR__ . '/_files/translation_en.mo']);
    }

    public function testOtherEncoding()
    {
        if (PHP_OS == 'AIX') {
            $this->markTestSkipped('These charsets are not supported on AIX');
        }

        $adapter = new Adapter\Gettext(__DIR__ . '/_files/translation_otherencoding.mo', 'ru');
        $adapter->addTranslation(__DIR__ . '/_files/translation_otherencoding.mo', 'ru');
        // Original message is in KOI8-R.. as unit tests are done in UTF8 we have to convert
        // the returned KOI8-R string into UTF-8
        $translation = iconv("KOI8-R", "UTF-8", $adapter->translate('Message 2', 'ru'));
        $this->assertEquals('Сообщение 2 (ru)', $translation);
        $this->assertEquals('Message 5', $adapter->translate('Message 5'));
        $this->assertEquals('Message 5', $adapter->translate('Message 5', 'ru_RU'));
    }

    public function testFailedFile()
    {
        $this->setExpectedException('Zend\Translator\Exception\InvalidArgumentException');
        $adapter = new Adapter\Gettext(__DIR__ . '/_files/failed2.mo', 'en');
    }

    public function testMissingAdapterInfo()
    {
        $adapter = new Adapter\Gettext(__DIR__ . '/_files/failed3.mo', 'en');
        $values = $adapter->getAdapterInfo();
        $this->assertContains('No adapter information available', current($values));
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
