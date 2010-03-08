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
 * Zend_Translate_Adapter_Tbx
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
class Zend_Translate_Adapter_TbxTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Translate_Adapter_TbxTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testCreate()
    {
        $adapter = new Zend_Translate_Adapter_Tbx(dirname(__FILE__) . '/_files/translation_en.tbx', 'en');
        $this->assertTrue($adapter instanceof Zend_Translate_Adapter_Tbx);

        try {
            $adapter = new Zend_Translate_Adapter_Tbx(dirname(__FILE__) . '/_files/nofile.tbx', 'en');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            $this->assertContains('is not readable', $e->getMessage());
        }

        try {
            $adapter = new Zend_Translate_Adapter_Tbx(dirname(__FILE__) . '/_files/failed.tbx', 'en');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            $this->assertContains('Mismatched tag at line', $e->getMessage());
        }
    }

    public function testToString()
    {
        $adapter = new Zend_Translate_Adapter_Tbx(dirname(__FILE__) . '/_files/translation_en.tbx', 'fr');
        $this->assertEquals('Tbx', $adapter->toString());
    }

    public function testTranslate()
    {
        $adapter = new Zend_Translate_Adapter_Tbx(dirname(__FILE__) . '/_files/translation_en.tbx', 'fr');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 1 (en)', $adapter->_('Message 1'));
        $this->assertEquals('Message 6', $adapter->translate('Message 6'));
        $this->assertEquals('Küchen Möbel (en)', $adapter->translate('Cooking furniture'));
        $this->assertEquals('Cooking furniture (en)', $adapter->translate('Küchen Möbel'));
    }

    public function testIsTranslated()
    {
        $adapter = new Zend_Translate_Adapter_Tbx(dirname(__FILE__) . '/_files/translation_en.tbx', 'en');
        $this->assertTrue($adapter->isTranslated('Message 1'));
        $this->assertFalse($adapter->isTranslated('Message 6'));
        $this->assertTrue($adapter->isTranslated('Message 1', true));
        $this->assertTrue($adapter->isTranslated('Message 1', true, 'en'));
        $this->assertFalse($adapter->isTranslated('Message 1', false, 'es'));
    }

    public function testLoadTranslationData()
    {
        $adapter = new Zend_Translate_Adapter_Tbx(dirname(__FILE__) . '/_files/translation_en.tbx', 'fr');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 4 (en)', $adapter->translate('Message 4'));
        $this->assertEquals('Message 2', $adapter->translate('Message 2', 'ru'));
        $this->assertEquals('Message 1', $adapter->translate('Message 1', 'xx'));

        try {
            $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en.tbx', 'xx');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            $this->assertContains('does not exist', $e->getMessage());
        }

        $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en2.tbx', 'de', array('clear' => true));
        $this->assertEquals('Nachricht 1', $adapter->translate('Message 1'));
        $this->assertEquals('Nachricht 8', $adapter->translate('Message 8'));
    }

    public function testOptions()
    {
        $adapter = new Zend_Translate_Adapter_Tbx(dirname(__FILE__) . '/_files/translation_en.tbx', 'en');
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
        $adapter = new Zend_Translate_Adapter_Tbx(dirname(__FILE__) . '/_files/translation_en.tbx', 'fr');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 4 (en)', $adapter->translate('Message 4'));
        $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en2.tbx', 'de', array('clear' => true));
        $this->assertEquals('Nachricht 1', $adapter->translate('Message 1'));
        $this->assertEquals('Message 4', $adapter->translate('Message 4'));
    }

    public function testLocale()
    {
        $adapter = new Zend_Translate_Adapter_Tbx(dirname(__FILE__) . '/_files/translation_en.tbx', 'fr');
        $this->assertEquals('fr', $adapter->getLocale());
        $locale = new Zend_Locale('fr');
        $adapter->setLocale($locale);
        $this->assertEquals('fr', $adapter->getLocale());

        try {
            $adapter->setLocale('nolocale');
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            $this->assertContains('does not exist', $e->getMessage());
        }

        set_error_handler(array($this, 'errorHandlerIgnore'));
        $adapter->setLocale('ru');
        restore_error_handler();
        $this->assertEquals('ru', $adapter->getLocale());
    }

    public function testList()
    {
        $adapter = new Zend_Translate_Adapter_Tbx(dirname(__FILE__) . '/_files/translation_en.tbx', 'en');
        $this->assertEquals(array('en' => 'en', 'fr' => 'fr'), $adapter->getList());
        $adapter->addTranslation(dirname(__FILE__) . '/_files/translation_en2.tbx', 'de');
        $this->assertEquals(array('en' => 'en', 'de' => 'de', 'fr' => 'fr'), $adapter->getList());
        $this->assertTrue($adapter->isAvailable('fr'));
        $locale = new Zend_Locale('en');
        $this->assertTrue( $adapter->isAvailable($locale));
        $this->assertFalse($adapter->isAvailable('sr'));
    }

    public function testOptionLocaleDirectory()
    {
        $adapter = new Zend_Translate_Adapter_Tbx(dirname(__FILE__) . '/_files/testtbx', 'de', array('scan' => Zend_Translate::LOCALE_DIRECTORY));
        $this->assertEquals(array('en' => 'en', 'fr' => 'fr', 'de' => 'de'), $adapter->getList());
        $this->assertEquals('Nachricht 1', $adapter->translate('Message 1'));
    }

    public function testOptionLocaleFilename()
    {
        $adapter = new Zend_Translate_Adapter_Tbx(dirname(__FILE__) . '/_files/testtbx', 'de', array('scan' => Zend_Translate::LOCALE_FILENAME));
        $this->assertEquals(array('en' => 'en', 'fr' => 'fr', 'de' => 'de'), $adapter->getList());
        $this->assertEquals('Nachricht 1', $adapter->translate('Message 1'));
    }

    public function testIsoEncoding()
    {
        $adapter = new Zend_Translate_Adapter_Tbx(dirname(__FILE__) . '/_files/translation_en3.tbx', 'fr');
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
        $adapter = new Zend_Translate_Adapter_Tbx(dirname(__FILE__) . '/_files/translation_withoutencoding.tbx', 'fr');
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

// Call Zend_Translate_Adapter_TbxTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Translate_Adapter_TbxTest::main") {
    Zend_Translate_Adapter_TbxTest::main();
}
