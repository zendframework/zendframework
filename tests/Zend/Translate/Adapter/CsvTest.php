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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Zend_Translate_Adapter_Csv
 */

/**
 * PHPUnit test case
 */

/**
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Translate
 */
class Zend_Translate_Adapter_CsvTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Translate_Adapter_CsvTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        if (Zend_Translate_Adapter_Csv::hasCache()) {
            Zend_Translate_Adapter_Csv::removeCache();
        }
    }

    public function testCreate()
    {
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_en.csv');
        $this->assertTrue($adapter instanceof Zend_Translate_Adapter_Csv);

        try {
            $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/nofile.csv', 'en');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            $this->assertContains('Error opening translation file', $e->getMessage());
        }

        set_error_handler(array($this, 'errorHandlerIgnore'));
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/failed.csv', 'en');
        restore_error_handler();
    }

    public function testToString()
    {
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_en.csv');
        $this->assertEquals('Csv', $adapter->toString());
    }

    public function testTranslate()
    {
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_en.csv', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 1 (en)', $adapter->_('Message 1'));
        $this->assertEquals('Message 60', $adapter->translate('Message 60'));
        $this->assertEquals('Küchen Möbel (en)', $adapter->translate('Cooking furniture'));
        $this->assertEquals('Cooking furniture (en)', $adapter->translate('Küchen Möbel'));
    }

    public function testIsTranslated()
    {
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_en.csv', 'en');
        $this->assertTrue($adapter->isTranslated('Message 1'));
        $this->assertFalse($adapter->isTranslated('Message 60'));
        $this->assertTrue($adapter->isTranslated('Message 1', true));
        $this->assertTrue($adapter->isTranslated('Message 1', true, 'en'));
        $this->assertFalse($adapter->isTranslated('Message 1', false, 'es'));
    }

    public function testLoadTranslationData()
    {
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_en.csv', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 4 (en)', $adapter->translate('Message 4'));
        $this->assertEquals('Message 2', $adapter->translate('Message 2', 'ru'));
        $this->assertEquals('Message 1', $adapter->translate('Message 1', 'xx'));
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1', 'en_US'));

        try {
            $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en.csv', 'xx');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            $this->assertContains('does not exist', $e->getMessage());
        }

        $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en2.csv', 'de', array('clear' => true));
        $this->assertEquals('Nachricht 1', $adapter->translate('Message 1'));
        $this->assertEquals('Nachricht 8', $adapter->translate('Message 8'));
    }

    public function testOptions()
    {
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_en.csv', 'en');
        $adapter->setOptions(array('testoption' => 'testkey'));
        $this->assertEquals(
            array(
                'delimiter'       => ';',
                'testoption'      => 'testkey',
                'clear'           => false,
                'scan'            => null,
                'locale'          => 'en',
                'length'          => 0,
                'enclosure'       => '"',
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
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_en.csv', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 60', $adapter->translate('Message 60'));
        $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en2.csv', 'de', array('clear' => true));
        $this->assertEquals('Nachricht 1', $adapter->translate('Message 1'));
        $this->assertEquals('Message 4', $adapter->translate('Message 4'));
    }

    public function testLocale()
    {
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_en.csv', 'en');
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
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_en.csv', 'en');
        $this->assertEquals(array('en' => 'en'), $adapter->getList());
        $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en.csv', 'de');
        $this->assertEquals(array('en' => 'en', 'de' => 'de'), $adapter->getList());
        $this->assertTrue($adapter->isAvailable('de'));
        $locale = new Zend_Locale('en');
        $this->assertTrue($adapter->isAvailable($locale));
        $this->assertFalse($adapter->isAvailable('sr'));
    }

    public function testOptionLocaleDirectory()
    {
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/testcsv', 'de_AT', array('scan' => Zend_Translate::LOCALE_DIRECTORY));
        $this->assertEquals(array('de_AT' => 'de_AT', 'en_GB' => 'en_GB'), $adapter->getList());
        $this->assertEquals('Nachricht 8', $adapter->translate('Message 8'));
    }

    public function testOptionLocaleFilename()
    {
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/testcsv', 'de_DE', array('scan' => Zend_Translate::LOCALE_FILENAME));
        $this->assertEquals(array('de_DE' => 'de_DE', 'en_US' => 'en_US'), $adapter->getList());
        $this->assertEquals('Nachricht 8', $adapter->translate('Message 8'));
    }

    public function testOtherDelimiter()
    {
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_otherdelimiter.csv', 'en', array('delimiter' => ','));
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 4 (en)', $adapter->translate('Message 4,'));
        $this->assertEquals('Message 5, (en)', $adapter->translate('Message 5'));
        $this->assertEquals('Message 6,addon (en)', $adapter->translate('Message 6,addon,'));
    }

    public function testSpecialChars()
    {
        $adapter = new Zend_Translate_Adapter_Csv(dirname(__FILE__) . '/_files/translation_specialchars.csv', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 1 (en)', $adapter->_('Message 1'));
        $this->assertEquals('Message 6;" (en)', $adapter->translate('Message 6'));
        $this->assertEquals('Message 7 (en)', $adapter->translate('Message ;" 7'));
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

// Call Zend_Translate_Adapter_CsvTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Translate_Adapter_CsvTest::main") {
    Zend_Translate_Adapter_CsvTest::main();
}
