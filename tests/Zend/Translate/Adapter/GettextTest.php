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
 * @package    Zend_Translate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Zend_Translate_Adapter_Gettext
 */
require_once 'Zend/Translate/Adapter/Gettext.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Translate
 */
class Zend_Translate_Adapter_GettextTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Translate_Adapter_GettextTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
        Zend_Translate_Adapter_Gettext::removeCache();
    }

    public function setUp()
    {
        if (Zend_Translate_Adapter_Gettext::hasCache()) {
            Zend_Translate_Adapter_Gettext::removeCache();
        }
    }

    public function testCreate()
    {
        $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/translation_en.mo');
        $this->assertTrue($adapter instanceof Zend_Translate_Adapter_Gettext);

        try {
            $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/nofile.mo', 'en');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            $this->assertContains('Error opening translation file', $e->getMessage());
        }

        try {
            $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/failed.mo', 'en');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            $this->assertContains('is not a gettext file', $e->getMessage());
        }
    }

    public function testToString()
    {
        $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/translation_en.mo');
        $this->assertEquals('Gettext', $adapter->toString());
    }

    public function testTranslate()
    {
        $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/translation_en.mo', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 1 (en)', $adapter->_('Message 1'));
        $this->assertEquals('Message 6', $adapter->translate('Message 6'));
        $this->assertEquals('Küchen Möbel (en)', $adapter->translate('Cooking furniture'));
        $this->assertEquals('Cooking furniture (en)', $adapter->translate('Küchen Möbel'));
    }

    public function testIsTranslated()
    {
        $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/translation_en.mo', 'en');
        $this->assertTrue($adapter->isTranslated('Message 1'));
        $this->assertFalse($adapter->isTranslated('Message 6'));
        $this->assertTrue($adapter->isTranslated('Message 1', true));
        $this->assertTrue($adapter->isTranslated('Message 1', true, 'en'));
        $this->assertFalse($adapter->isTranslated('Message 1', false, 'es'));
    }

    public function testLoadTranslationData()
    {
        $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/translation_en.mo', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 4 (en)', $adapter->translate('Message 4'));
        $this->assertEquals('Message 2', $adapter->translate('Message 2', 'ru'));
        $this->assertEquals('Message 1', $adapter->translate('Message 1', 'xx'));
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1', 'en_US'));

        try {
            $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en2.mo', 'xx');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            $this->assertContains('does not exist', $e->getMessage());
        }

        $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en2.mo', 'de', array('clear' => true));
        $this->assertEquals('Nachricht 1', $adapter->translate('Message 1'));
        $this->assertEquals('Nachricht 8', $adapter->translate('Message 8'));
    }

    public function testOptions()
    {
        $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/translation_en.mo', 'en');
        $adapter->setOptions(array('testoption' => 'testkey'));
        $this->assertEquals(
            array(
                'testoption'      => 'testkey',
                'clear'           => false,
                'scan'            => null,
                'locale'          => 'en',
                'ignore'          => '.',
                'disableNotices'  => false,
                'log'             => false,
                'logMessage'      => 'Untranslated message within \'%locale%\': %message%',
                'logUntranslated' => false,
                'reload'          => false),
            $adapter->getOptions());
        $this->assertEquals('testkey', $adapter->getOptions('testoption'));
        $this->assertTrue(is_null($adapter->getOptions('nooption')));
    }

    public function testClearing()
    {
        $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/translation_en.mo', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 6', $adapter->translate('Message 6'));
        $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en2.mo', 'de', array('clear' => true));
        $this->assertEquals('Nachricht 1', $adapter->translate('Message 1'));
        $this->assertEquals('Message 4', $adapter->translate('Message 4'));
    }

    public function testLocale()
    {
        $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/translation_en.mo', 'en');
        $this->assertEquals('en', $adapter->getLocale());
        $locale = new Zend_Locale('en');
        $adapter->setLocale($locale);
        $this->assertEquals('en', $adapter->getLocale());

        try {
            $adapter->setLocale('nolocale');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            $this->assertContains('does not exist', $e->getMessage());
        }

        set_error_handler(array($this, 'errorHandlerIgnore'));
        $adapter->setLocale('de');
        restore_error_handler();
        $this->assertEquals('de', $adapter->getLocale());
    }

    public function testList()
    {
        $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/translation_en.mo', 'en');
        $this->assertEquals(array('en' => 'en'), $adapter->getList());
        $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en.mo', 'de');
        $this->assertEquals(array('en' => 'en', 'de' => 'de'), $adapter->getList());
        $this->assertTrue($adapter->isAvailable('de'));
        $locale = new Zend_Locale('en');
        $this->assertTrue($adapter->isAvailable($locale));
        $this->assertFalse($adapter->isAvailable('sr'));
    }

    public function testOptionLocaleDirectory()
    {
        require_once 'Zend/Translate.php';
        $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/testmo/', 'de_AT', array('scan' => Zend_Translate::LOCALE_DIRECTORY));
        $this->assertEquals(array('de_AT' => 'de_AT', 'en_GB' => 'en_GB'), $adapter->getList());
        $this->assertEquals('Nachricht 8', $adapter->translate('Message 8'));
    }

    public function testOptionLocaleFilename()
    {
        require_once 'Zend/Translate.php';
        $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/testmo/', 'de_DE', array('scan' => Zend_Translate::LOCALE_FILENAME));
        $this->assertEquals(array('de_DE' => 'de_DE', 'en_US' => 'en_US'), $adapter->getList());
        $this->assertEquals('Nachricht 8', $adapter->translate('Message 8'));
    }

    public function testBigEndian()
    {
        $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/translation_bigendian.mo', 'sr');
        $this->assertEquals('Informacje', $adapter->translate('Informacje'));
    }

    public function testAdapterInfo()
    {
        $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/translation_en.mo');
        $this->assertEquals('', $adapter->translate(''));
        $info = $adapter->getAdapterInfo();
        $this->assertContains('Last-Translator: Thomas Weidner <thomas.weidner@voxtronic.com>', $info[dirname(__FILE__) . '/_files/translation_en.mo']);
    }

    public function testOtherEncoding()
    {
        if (PHP_OS == 'AIX') {
            $this->markTestSkipped('These charsets are not supported on AIX');
        }

        $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/translation_otherencoding.mo', 'ru');
        $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_otherencoding.mo', 'ru');
        // Original message is in KOI8-R.. as unit tests are done in UTF8 we have to convert
        // the returned KOI8-R string into UTF-8
        $translation = iconv("KOI8-R", "UTF-8", $adapter->translate('Message 2', 'ru'));
        $this->assertEquals('Сообщение 2 (ru)', $translation);
        $this->assertEquals('Message 5', $adapter->translate('Message 5'));
        $this->assertEquals('Message 5', $adapter->translate('Message 5', 'ru_RU'));
    }

    public function testFailedFile()
    {
        try {
            $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/failed2.mo', 'en');
            $this->fail('Exception expected');
        } catch (Zend_Translate_Exception $e) {
            $this->assertContains('is not a gettext file', $e->getMessage());
        }
    }

    public function testMissingAdapterInfo()
    {
        $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/_files/failed3.mo', 'en');
        $this->assertContains('No adapter information available', current($adapter->getAdapterInfo()));
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

// Call Zend_Translate_Adapter_GettextTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Translate_Adapter_GettextTest::main") {
    Zend_Translate_GettextTest::main();
}
