<?php
/**
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage UnitTests
 */

/**
 * Zend_Translate_Adapter_Ini
 */
require_once 'Zend/Translate/Adapter/Ini.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage UnitTests
 */
class Zend_Translate_Adapter_IniTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Translate_Adapter_IniTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testCreate()
    {
        $adapter = new Zend_Translate_Adapter_Ini(dirname(__FILE__) . '/_files/translation_en.ini');
        $this->assertTrue($adapter instanceof Zend_Translate_Adapter_Ini);

        try {
            $adapter = new Zend_Translate_Adapter_Ini(dirname(__FILE__) . '/_files/nofile.ini', 'en');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            $this->assertContains('not found', $e->getMessage());
        }

        set_error_handler(array($this, 'errorHandlerIgnore'));
        $adapter = new Zend_Translate_Adapter_Ini(dirname(__FILE__) . '/_files/failed.ini', 'en');
        restore_error_handler();
    }

    public function testToString()
    {
        $adapter = new Zend_Translate_Adapter_Ini(dirname(__FILE__) . '/_files/translation_en.ini');
        $this->assertEquals('Ini', $adapter->toString());
    }

    public function testTranslate()
    {
        $adapter = new Zend_Translate_Adapter_Ini(dirname(__FILE__) . '/_files/translation_en.ini', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message_1'));
        $this->assertEquals('Message 1 (en)', $adapter->_('Message_1'));
        $this->assertEquals('Message_6', $adapter->translate('Message_6'));
        $this->assertEquals('Küchen Möbel (en)', $adapter->translate('Cooking_furniture'));
        if (0 > version_compare(PHP_VERSION, '5.3.0')) {
            $this->assertEquals('Cooking furniture (en)', $adapter->translate('Küchen_Möbel'), var_export($adapter->getMessages('en'), 1));
        } else {
            $this->markTestSkipped('PHP 5.3 cannot utilize non-ASCII characters for INI option keys');
        }
    }

    public function testIsTranslated()
    {
        $adapter = new Zend_Translate_Adapter_Ini(dirname(__FILE__) . '/_files/translation_en.ini', 'en');
        $this->assertTrue($adapter->isTranslated('Message_1'));
        $this->assertFalse($adapter->isTranslated('Message_6'));
        $this->assertTrue($adapter->isTranslated('Message_1', true));
        $this->assertTrue($adapter->isTranslated('Message_1', true, 'en'));
        $this->assertFalse($adapter->isTranslated('Message_1', false, 'es'));
    }

    public function testLoadTranslationData()
    {
        $adapter = new Zend_Translate_Adapter_Ini(dirname(__FILE__) . '/_files/translation_en.ini', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message_1'));
        $this->assertEquals('Message 4 (en)', $adapter->translate('Message_4'));
        $this->assertEquals('Message_2', $adapter->translate('Message_2', 'ru'));
        $this->assertEquals('Message_1', $adapter->translate('Message_1', 'xx'));
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message_1', 'en_US'));

        try {
            $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en.ini', 'xx');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            $this->assertContains('The given Language', $e->getMessage());
        }

        $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en2.ini', 'de', array('clear' => true));
        $this->assertEquals('Nachricht 1', $adapter->translate('Message_1'));
        $this->assertEquals('Nachricht 8', $adapter->translate('Message_8'));
    }

    public function testOptions()
    {
        $adapter = new Zend_Translate_Adapter_Ini(dirname(__FILE__) . '/_files/translation_en.ini', 'en');
        $adapter->setOptions(array('testoption' => 'testkey'));
        $this->assertEquals(
            array(
                'testoption' => 'testkey',
                'clear' => false,
                'scan' => null,
                'locale' => 'en',
                'ignore' => '.',
                'disableNotices' => false,
                'log'             => false,
                'logMessage'      => 'Untranslated message within \'%locale%\': %message%',
                'logUntranslated' => false),
            $adapter->getOptions());
        $this->assertEquals('testkey', $adapter->getOptions('testoption'));
        $this->assertTrue(is_null($adapter->getOptions('nooption')));
    }

    public function testClearing()
    {
        $adapter = new Zend_Translate_Adapter_Ini(dirname(__FILE__) . '/_files/translation_en.ini', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message_1'));
        $this->assertEquals('Message_6', $adapter->translate('Message_6'));
        $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en2.ini', 'de', array('clear' => true));
        $this->assertEquals('Nachricht 1', $adapter->translate('Message_1'));
        $this->assertEquals('Message_4', $adapter->translate('Message_4'));
    }

    public function testLocale()
    {
        $adapter = new Zend_Translate_Adapter_Ini(dirname(__FILE__) . '/_files/translation_en.ini', 'en');
        $this->assertEquals('en', $adapter->getLocale());
        $locale = new Zend_Locale('en');
        $adapter->setLocale($locale);
        $this->assertEquals('en', $adapter->getLocale());

        try {
            $adapter->setLocale('nolocale');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            $this->assertContains('The given Language', $e->getMessage());
        }

        set_error_handler(array($this, 'errorHandlerIgnore'));
        $adapter->setLocale('de');
        restore_error_handler();
        $this->assertEquals('de', $adapter->getLocale());
    }

    public function testList()
    {
        $adapter = new Zend_Translate_Adapter_Ini(dirname(__FILE__) . '/_files/translation_en.ini', 'en');
        $this->assertEquals(array('en' => 'en'), $adapter->getList());
        $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en.ini', 'de');
        $this->assertEquals(array('en' => 'en', 'de' => 'de'), $adapter->getList());
        $this->assertTrue($adapter->isAvailable('de'));
        $locale = new Zend_Locale('en');
        $this->assertTrue($adapter->isAvailable($locale));
        $this->assertFalse($adapter->isAvailable('sr'));
    }

    public function testOptionLocaleDirectory()
    {
        require_once 'Zend/Translate.php';
        $adapter = new Zend_Translate_Adapter_Ini(dirname(__FILE__) . '/_files/testini', 'de_AT', array('scan' => Zend_Translate::LOCALE_DIRECTORY));
        $this->assertEquals(array('de_AT' => 'de_AT', 'en_GB' => 'en_GB'), $adapter->getList());
        $this->assertEquals('Nachricht 8', $adapter->translate('Message_8'));
    }

    public function testOptionLocaleFilename()
    {
        require_once 'Zend/Translate.php';
        $adapter = new Zend_Translate_Adapter_Ini(dirname(__FILE__) . '/_files/testini', 'de_DE', array('scan' => Zend_Translate::LOCALE_FILENAME));
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

// Call Zend_Translate_Adapter_IniTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Translate_Adapter_IniTest::main") {
    Zend_Translate_Adapter_IniTest::main();
}
