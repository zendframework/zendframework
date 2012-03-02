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
 * Zend_Translator_Adapter_Tmx
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
class TmxTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $adapter = new Adapter\Tmx(__DIR__ . '/_files/translation_en.tmx', 'en');
        $this->assertTrue($adapter instanceof Adapter\Tmx);
    }

    public function testCreate2()
    {
        $adapter = new Adapter\Tmx(__DIR__ . '/_files/translation_en.tmx', 'en');
        $this->setExpectedException('Zend\Translator\Exception\InvalidArgumentException');
        $adapter = new Adapter\Tmx(__DIR__ . '/_files/nofile.tmx', 'en');
    }

    public function testCreate3()
    {
        $this->setExpectedException('Zend\Translator\Exception\InvalidFileTypeException');
        $adapter = new Adapter\Tmx(__DIR__ . '/_files/failed.tmx', 'en');
    }
    
    /**
     * @group ZF-12012
     */
    public function testErrorOnCreateIncludesFilename()
    {
        try {
            $adapter = new Adapter\Tmx(__DIR__ . '/_files/failed.tmx', 'en');
            $this->fail("exception expected");
        } catch (InvalidFileTypeException $e) {
            $this->assertContains('failed.tmx', $e->getMessage());
        }
    }

    public function testToString()
    {
        $adapter = new Adapter\Tmx(__DIR__ . '/_files/translation_en.tmx', 'en');
        $this->assertEquals('Tmx', $adapter->toString());
    }

    public function testTranslate()
    {
        $adapter = new Adapter\Tmx(__DIR__ . '/_files/translation_en.tmx', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 1 (en)', $adapter->_('Message 1'));
        $this->assertEquals('Message 6', $adapter->translate('Message 6'));
        $this->assertEquals('Küchen Möbel (en)', $adapter->translate('Cooking furniture'));
        $this->assertEquals('Cooking furniture (en)', $adapter->translate('Küchen Möbel'));
    }

    public function testIsTranslated()
    {
        $adapter = new Adapter\Tmx(__DIR__ . '/_files/translation_en.tmx', 'en');
        $this->assertTrue($adapter->isTranslated('Message 1'));
        $this->assertFalse($adapter->isTranslated('Message 6'));
        $this->assertTrue($adapter->isTranslated('Message 1', true));
        $this->assertTrue($adapter->isTranslated('Message 1', true, 'en'));
        $this->assertFalse($adapter->isTranslated('Message 1', false, 'es'));
    }

    public function testLoadTranslationData()
    {
        $adapter = new Adapter\Tmx(__DIR__ . '/_files/translation_en.tmx', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 4 (en)', $adapter->translate('Message 4'));
        $this->assertEquals('Message 2', $adapter->translate('Message 2', 'ru'));
        $this->assertEquals('Message 1', $adapter->translate('Message 1', 'xx'));
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1', 'en_US'));
    }

    public function testLoadTranslationData2()
    {
        $adapter = new Adapter\Tmx(__DIR__ . '/_files/translation_en.tmx', 'en');
        $this->setExpectedException('Zend\Translator\Exception\InvalidArgumentException');
        $adapter->addTranslation(__DIR__ . '/_files/translation_en.tmx', 'xx');
    }

    public function testLoadTranslationData3()
    {
        $adapter = new Adapter\Tmx(__DIR__ . '/_files/translation_en.tmx', 'en');
        $adapter->addTranslation(__DIR__ . '/_files/translation_en2.tmx', 'de', array('clear' => true));
        $this->assertEquals('Nachricht 1', $adapter->translate('Message 1'));
        $this->assertEquals('Nachricht 8', $adapter->translate('Message 8'));
    }

    public function testOptions()
    {
        $adapter = new Adapter\Tmx(__DIR__ . '/_files/translation_en.tmx', 'en');
        $adapter->setOptions(array('testoption' => 'testkey'));
        $expected = array(
            'testoption'      => 'testkey',
            'clear'           => false,
            'content'         => __DIR__ . '/_files/translation_en.tmx',
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
        $adapter = new Adapter\Tmx(__DIR__ . '/_files/translation_en.tmx', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 5 (en)', $adapter->translate('Message 5'));
        $adapter->addTranslation(__DIR__ . '/_files/translation_en2.tmx', 'de', array('clear' => true));
        $this->assertEquals('Nachricht 1', $adapter->translate('Message 1'));
        $this->assertEquals('Message 4', $adapter->translate('Message 4'));
    }

    public function testLocale()
    {
        $adapter = new Adapter\Tmx(__DIR__ . '/_files/translation_en.tmx', 'en');
        $this->assertEquals('en', $adapter->getLocale());
        $locale = new Locale\Locale('en');
        $adapter->setLocale($locale);
        $this->assertEquals('en', $adapter->getLocale());
    }

    public function testLocale2()
    {
        $adapter = new Adapter\Tmx(__DIR__ . '/_files/translation_en.tmx', 'en');
        $this->setExpectedException('Zend\Translator\Exception\InvalidArgumentException');
        $adapter->setLocale('nolocale');
    }

    public function testLocale3()
    {
        $adapter = new Adapter\Tmx(__DIR__ . '/_files/translation_en.tmx', 'en');
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $adapter->setLocale('it');
        restore_error_handler();
        $this->assertEquals('it', $adapter->getLocale());
    }

    public function testList()
    {
        $adapter = new Adapter\Tmx(__DIR__ . '/_files/translation_en.tmx', 'en');
        $this->assertEquals(array('en' => 'en', 'fr' => 'fr'), $adapter->getList());
        $adapter->addTranslation(__DIR__ . '/_files/translation_en2.tmx', 'fr');
        $this->assertEquals(array('en' => 'en', 'de' => 'de', 'fr' => 'fr'), $adapter->getList());
        $this->assertTrue($adapter->isAvailable('fr'));
        $locale = new Locale\Locale('en');
        $this->assertTrue($adapter->isAvailable($locale));
        $this->assertFalse($adapter->isAvailable('sr'));
    }

    public function testOptionLocaleDirectory()
    {
        $adapter = new Adapter\Tmx(__DIR__ . '/_files/testtmx', 'de', array('scan' => Translator\Translator::LOCALE_DIRECTORY));
        $this->assertEquals(array('de' => 'de', 'en' => 'en', 'fr' => 'fr'), $adapter->getList());
        $this->assertEquals('Nachricht 8', $adapter->translate('Message 8'));
    }

    public function testOptionLocaleFilename()
    {
        $adapter = new Adapter\Tmx(__DIR__ . '/_files/testtmx', 'de', array('scan' => Translator\Translator::LOCALE_FILENAME));
        $this->assertEquals(array('de' => 'de', 'en' => 'en', 'fr' => 'fr'), $adapter->getList());
        $this->assertEquals('Nachricht 8', $adapter->translate('Message 8'));
    }

    public function testIsoEncoding()
    {
        $adapter = new Adapter\Tmx(__DIR__ . '/_files/translation_en3.tmx', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 1 (it)', $adapter->_('Message 1', 'it'));

        if (PHP_OS == 'AIX') {
            return;
            // 'Charsets below are not supported on AIX';
        }

        $this->assertEquals(iconv('UTF-8', 'ISO-8859-1', 'Küchen Möbel (en)'), $adapter->translate('Cooking furniture'));
        $this->assertEquals('Cooking furniture (en)', $adapter->translate(iconv('UTF-8', 'ISO-8859-1', 'Küchen Möbel')));
    }

    public function testWithoutEncoding()
    {
        $adapter = new Adapter\Tmx(__DIR__ . '/_files/translation_withoutencoding.tmx', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Küchen Möbel (en)', $adapter->translate('Cooking furniture'));
        $this->assertEquals('Cooking furniture (en)', $adapter->translate('Küchen Möbel'));
    }

    /**
     * @group ZF-8375
     */
    public function testTranslate_ZF8375()
    {
        $adapter = new Adapter\Tmx(__DIR__ . '/_files/translation_en_8375.tmx', 'en', array('disableNotices' => true));
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 1 (en)', $adapter->_('Message 1'));
        $this->assertEquals('Message 6', $adapter->translate('Message 6'));
        $this->assertEquals('Küchen Möbel (en)', $adapter->translate('Cooking furniture'));
        $this->assertEquals('Cooking furniture (en)', $adapter->translate('Küchen Möbel'));
        $this->assertEquals('Message 1 (fr)', $adapter->translate('Message 1', 'fr_FR'));
        $this->assertEquals('Message 1 (fr)', $adapter->_('Message 1', 'fr_FR'));
    }

    public function testUseId()
    {
        $adapter = new Adapter\Tmx(__DIR__ . '/_files/translation_en2.tmx', 'en', array('useId' => false));
        $this->assertEquals(false, $adapter->getOptions('useId'));
        $this->assertEquals('Message 1 (en)', $adapter->translate('Nachricht 1'));
        $this->assertEquals('Message 1 (en)', $adapter->_('Nachricht 1'));
        $this->assertEquals('Nachricht 6', $adapter->translate('Nachricht 6'));
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
