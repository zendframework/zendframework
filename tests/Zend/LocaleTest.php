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
 * @package    Zend_Locale
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_LocaleTest::main');
}

require_once dirname(__FILE__) . '/../TestHelper.php';

// define('TESTS_ZEND_LOCALE_BCMATH_ENABLED', false); // uncomment to disable use of bcmath extension by Zend_Date

/**
 * Zend_Locale
 */
require_once 'Zend/Locale.php';
require_once 'Zend/Cache.php';

/**
 * @category   Zend
 * @package    Zend_Locale
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Locale
 */
class Zend_LocaleTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_LocaleTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    private $_cache  = null;
    private $_locale = null;

    public function setUp()
    {
        $this->_locale = setlocale(LC_ALL, 0);
        setlocale(LC_ALL, 'de');
        require_once 'Zend/Cache.php';
        $this->_cache = Zend_Cache::factory('Core', 'File',
                 array('lifetime' => 120, 'automatic_serialization' => true),
                 array('cache_dir' => dirname(__FILE__) . '/_files/'));
        Zend_LocaleTestHelper::resetObject();
        Zend_LocaleTestHelper::setCache($this->_cache);

        // compatibilityMode is true until 1.8 therefor we have to change it
        Zend_LocaleTestHelper::$compatibilityMode = false;
        putenv("HTTP_ACCEPT_LANGUAGE=,de,en-UK-US;q=0.5,fr_FR;q=0.2");
    }

    public function tearDown()
    {
        $this->_cache->clean(Zend_Cache::CLEANING_MODE_ALL);
        if (is_string($this->_locale) && strpos($this->_locale, ';')) {
            $locales = array();
            foreach (explode(';', $this->_locale) as $l) {
                $tmp = explode('=', $l);
                $locales[$tmp[0]] = $tmp[1];
            }
            setlocale(LC_ALL, $locales);
            return;
        }
        setlocale(LC_ALL, $this->_locale);
    }

    /**
     * test for object creation
     * expected object instance
     */
    public function testObjectCreation()
    {
        $this->assertTrue(Zend_LocaleTestHelper::isLocale('de'));

        $this->assertTrue(new Zend_LocaleTestHelper() instanceof Zend_Locale);
        $this->assertTrue(new Zend_LocaleTestHelper('root') instanceof Zend_Locale);
        try {
            $locale = new Zend_LocaleTestHelper(Zend_Locale::ENVIRONMENT);
            $this->assertTrue($locale instanceof Zend_Locale);
        } catch (Zend_Locale_Exception $e) {
            // ignore environments where the locale can not be detected
            $this->assertContains('Autodetection', $e->getMessage());
        }

        try {
            $this->assertTrue(new Zend_LocaleTestHelper(Zend_Locale::BROWSER) instanceof Zend_Locale);
        } catch (Zend_Locale_Exception $e) {
            // ignore environments where the locale can not be detected
            $this->assertContains('Autodetection', $e->getMessage());
        }

        $locale = new Zend_LocaleTestHelper('de');
        $this->assertTrue(new Zend_LocaleTestHelper($locale) instanceof Zend_Locale);

        $locale = new Zend_LocaleTestHelper('auto');
        $this->assertTrue(new Zend_LocaleTestHelper($locale) instanceof Zend_Locale);

        // compatibility tests
        set_error_handler(array($this, 'errorHandlerIgnore'));
        Zend_LocaleTestHelper::$compatibilityMode = true;
        $this->assertEquals('de', Zend_LocaleTestHelper::isLocale('de'));
        restore_error_handler();
    }

    /**
     * test for serialization
     * expected string
     */
    public function testSerialize()
    {
        $value = new Zend_LocaleTestHelper('de_DE');
        $serial = $value->serialize();
        $this->assertTrue(!empty($serial));

        $newvalue = unserialize($serial);
        $this->assertTrue($value->equals($newvalue));
    }

    /**
     * test toString
     * expected string
     */
    public function testToString()
    {
        $value = new Zend_LocaleTestHelper('de_DE');
        $this->assertEquals('de_DE', $value->toString());
        $this->assertEquals('de_DE', $value->__toString());
    }

    /**
     * test getOrder
     * expected true
     */
    public function testgetOrder()
    {
        Zend_LocaleTestHelper::setDefault('de');
        $value = new Zend_LocaleTestHelper();
        $default = $value->getOrder();
        $this->assertTrue(array_key_exists('de', $default));

        $default = $value->getOrder(Zend_Locale::BROWSER);
        $this->assertTrue(is_array($default));

        $default = $value->getOrder(Zend_Locale::ENVIRONMENT);
        $this->assertTrue(is_array($default));

        $default = $value->getOrder(Zend_Locale::ZFDEFAULT);
        $this->assertTrue(is_array($default));
    }

    /**
     * test getEnvironment
     * expected true
     */
    public function testLocaleDetail()
    {
        $value = new Zend_LocaleTestHelper('de_AT');
        $this->assertEquals('de', $value->getLanguage());
        $this->assertEquals('AT', $value->getRegion());

        $value = new Zend_LocaleTestHelper('en_US');
        $this->assertEquals('en', $value->getLanguage());
        $this->assertEquals('US', $value->getRegion());

        $value = new Zend_LocaleTestHelper('en');
        $this->assertEquals('en', $value->getLanguage());
        $this->assertFalse($value->getRegion());
    }

    /**
     * test getEnvironment
     * expected true
     */
    public function testEnvironment()
    {
        $value = new Zend_LocaleTestHelper();
        $default = $value->getEnvironment();
        $this->assertTrue(is_array($default));
    }

    /**
     * test getBrowser
     * expected true
     */
    public function testBrowser()
    {
        $value = new Zend_LocaleTestHelper();
        $default = $value->getBrowser();
        $this->assertTrue(is_array($default));
    }

    /**
     * test clone
     * expected true
     */
    public function testCloning()
    {
        $value = new Zend_LocaleTestHelper('de_DE');
        $newvalue = clone $value;
        $this->assertEquals($value->toString(), $newvalue->toString());
    }

    /**
     * test setLocale
     * expected true
     */
    public function testsetLocale()
    {
        $value = new Zend_LocaleTestHelper('de_DE');
        $value->setLocale('en_US');
        $this->assertEquals('en_US', $value->toString());

        $value->setLocale('en_AA');
        $this->assertEquals('en', $value->toString());

        $value->setLocale('xx_AA');
        $this->assertEquals('root', $value->toString());

        $value->setLocale('auto');
        $this->assertTrue(is_string($value->toString()));

        try {
            $value->setLocale('browser');
            $this->assertTrue(is_string($value->toString()));
        } catch (Zend_Locale_Exception $e) {
            // ignore environments where the locale can not be detected
            $this->assertContains('Autodetection', $e->getMessage());
        }

        try {
            $value->setLocale('environment');
            $this->assertTrue(is_string($value->toString()));
        } catch (Zend_Locale_Exception $e) {
            // ignore environments where the locale can not be detected
            $this->assertContains('Autodetection', $e->getMessage());
        }
    }

    /**
     * test getLanguageTranslationList
     * expected true
     */
    public function testgetLanguageTranslationList()
    {
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $list = Zend_LocaleTestHelper::getLanguageTranslationList();
        $this->assertTrue(is_array($list));
        $list = Zend_LocaleTestHelper::getLanguageTranslationList('de');
        $this->assertTrue(is_array($list));
        restore_error_handler();
    }

    /**
     * test getLanguageTranslation
     * expected true
     */
    public function testgetLanguageTranslation()
    {
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $this->assertEquals('Deutsch', Zend_LocaleTestHelper::getLanguageTranslation('de', 'de_AT'));
        $this->assertEquals('German',  Zend_LocaleTestHelper::getLanguageTranslation('de', 'en'));
        $this->assertFalse(Zend_LocaleTestHelper::getLanguageTranslation('xyz'));
        $this->assertTrue(is_string(Zend_LocaleTestHelper::getLanguageTranslation('de', 'auto')));
        restore_error_handler();
    }

    /**
     * test getScriptTranslationList
     * expected true
     */
    public function testgetScriptTranslationList()
    {
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $list = Zend_LocaleTestHelper::getScriptTranslationList();
        $this->assertTrue(is_array($list));

        $list = Zend_LocaleTestHelper::getScriptTranslationList('de');
        $this->assertTrue(is_array($list));
        restore_error_handler();
    }

    /**
     * test getScriptTranslationList
     * expected true
     */
    public function testgetScriptTranslation()
    {
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $this->assertEquals('Arabisch', Zend_LocaleTestHelper::getScriptTranslation('Arab', 'de_AT'));
        $this->assertEquals('Arabic', Zend_LocaleTestHelper::getScriptTranslation('Arab', 'en'));
        $this->assertFalse(Zend_LocaleTestHelper::getScriptTranslation('xyz'));
        restore_error_handler();
    }

    /**
     * test getCountryTranslationList
     * expected true
     */
    public function testgetCountryTranslationList()
    {
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $list = Zend_LocaleTestHelper::getCountryTranslationList();
        $this->assertTrue(is_array($list));

        $list = Zend_LocaleTestHelper::getCountryTranslationList('de');
        $this->assertEquals("Vereinigte Staaten", $list['US']);
        restore_error_handler();
    }

    /**
     * test getCountryTranslation
     * expected true
     */
    public function testgetCountryTranslation()
    {
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $this->assertEquals('Deutschland', Zend_LocaleTestHelper::getCountryTranslation('DE', 'de_DE'));
        $this->assertEquals('Germany', Zend_LocaleTestHelper::getCountryTranslation('DE', 'en'));
        $this->assertFalse(Zend_LocaleTestHelper::getCountryTranslation('xyz'));
        restore_error_handler();
    }

    /**
     * test getTerritoryTranslationList
     * expected true
     */
    public function testgetTerritoryTranslationList()
    {
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $list = Zend_LocaleTestHelper::getTerritoryTranslationList();
        $this->assertTrue(is_array($list));

        $list = Zend_LocaleTestHelper::getTerritoryTranslationList('de');
        $this->assertTrue(is_array($list));
        restore_error_handler();
    }

    /**
     * test getTerritoryTranslation
     * expected true
     */
    public function testgetTerritoryTranslation()
    {
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $this->assertEquals('Afrika', Zend_LocaleTestHelper::getTerritoryTranslation('002', 'de_AT'));
        $this->assertEquals('Africa', Zend_LocaleTestHelper::getTerritoryTranslation('002', 'en'));
        $this->assertFalse(Zend_LocaleTestHelper::getTerritoryTranslation('xyz'));
        $this->assertTrue(is_string(Zend_LocaleTestHelper::getTerritoryTranslation('002', 'auto')));
        restore_error_handler();
    }

    /**
     * test getTranslation
     * expected true
     */
    public function testgetTranslation()
    {
        try {
            $temp = Zend_LocaleTestHelper::getTranslation('xx');
            $this->fail();
        } catch (Zend_Locale_Exception $e) {
            $this->assertContains('Unknown detail (', $e->getMessage());
        }

        $this->assertEquals('Deutsch', Zend_LocaleTestHelper::getTranslation('de', 'language', 'de_DE'));
        $this->assertEquals('German', Zend_LocaleTestHelper::getTranslation('de', 'language', 'en'));
        $this->assertFalse(Zend_LocaleTestHelper::getTranslation('xx', 'language'));

        $this->assertEquals('Lateinisch', Zend_LocaleTestHelper::getTranslation('Latn', 'script', 'de_DE'));
        $this->assertEquals('Latin', Zend_LocaleTestHelper::getTranslation('Latn', 'script', 'en'));
        $this->assertFalse(Zend_LocaleTestHelper::getTranslation('xyxy', 'script'));

        $this->assertEquals('Österreich', Zend_LocaleTestHelper::getTranslation('AT', 'country', 'de_DE'));
        $this->assertEquals('Austria', Zend_LocaleTestHelper::getTranslation('AT', 'country', 'en'));
        $this->assertFalse(Zend_LocaleTestHelper::getTranslation('xx', 'country'));

        $this->assertEquals('Afrika', Zend_LocaleTestHelper::getTranslation('002', 'territory', 'de_DE'));
        $this->assertEquals('Africa', Zend_LocaleTestHelper::getTranslation('002', 'territory', 'en'));
        $this->assertFalse(Zend_LocaleTestHelper::getTranslation('xxx', 'territory'));

        $this->assertEquals('Januar', Zend_LocaleTestHelper::getTranslation('1', 'month', 'de_DE'));
        $this->assertEquals('January', Zend_LocaleTestHelper::getTranslation('1', 'month', 'en'));
        $this->assertFalse(Zend_LocaleTestHelper::getTranslation('x', 'month'));

        $this->assertEquals('Jan', Zend_LocaleTestHelper::getTranslation(array('gregorian', 'format', 'abbreviated', '1'), 'month', 'de_DE'));
        $this->assertEquals('Jan', Zend_LocaleTestHelper::getTranslation(array('gregorian', 'format', 'abbreviated', '1'), 'month', 'en'));
        $this->assertFalse(Zend_LocaleTestHelper::getTranslation(array('gregorian', 'format', 'abbreviated', 'x'), 'month'));

        $this->assertEquals('J', Zend_LocaleTestHelper::getTranslation(array('gregorian', 'stand-alone', 'narrow', '1'), 'month', 'de_DE'));
        $this->assertEquals('J', Zend_LocaleTestHelper::getTranslation(array('gregorian', 'stand-alone', 'narrow', '1'), 'month', 'en'));
        $this->assertFalse(Zend_LocaleTestHelper::getTranslation(array('gregorian', 'stand-alone', 'narrow', 'x'), 'month'));

        $this->assertEquals('Sonntag', Zend_LocaleTestHelper::getTranslation('sun', 'day', 'de_DE'));
        $this->assertEquals('Sunday', Zend_LocaleTestHelper::getTranslation('sun', 'day', 'en'));
        $this->assertFalse(Zend_LocaleTestHelper::getTranslation('xxx', 'day'));

        $this->assertEquals('So.', Zend_LocaleTestHelper::getTranslation(array('gregorian', 'format', 'abbreviated', 'sun'), 'day', 'de_DE'));
        $this->assertEquals('Sun', Zend_LocaleTestHelper::getTranslation(array('gregorian', 'format', 'abbreviated', 'sun'), 'day', 'en'));
        $this->assertFalse(Zend_LocaleTestHelper::getTranslation(array('gregorian', 'format', 'abbreviated', 'xxx'), 'day'));

        $this->assertEquals('S', Zend_LocaleTestHelper::getTranslation(array('gregorian', 'stand-alone', 'narrow', 'sun'), 'day', 'de_DE'));
        $this->assertEquals('S', Zend_LocaleTestHelper::getTranslation(array('gregorian', 'stand-alone', 'narrow', 'sun'), 'day', 'en'));
        $this->assertFalse(Zend_LocaleTestHelper::getTranslation(array('gregorian', 'stand-alone', 'narrow', 'xxx'), 'day'));

        $this->assertEquals('EEEE, d. MMMM y', Zend_LocaleTestHelper::getTranslation('full', 'date', 'de_DE'));
        $this->assertEquals('EEEE, MMMM d, y', Zend_LocaleTestHelper::getTranslation('full', 'date', 'en'));
        $this->assertFalse(Zend_LocaleTestHelper::getTranslation('xxxx', 'date'));

        $this->assertEquals("HH:mm:ss zzzz", Zend_LocaleTestHelper::getTranslation('full', 'time', 'de_DE'));
        $this->assertEquals('h:mm:ss a zzzz', Zend_LocaleTestHelper::getTranslation('full', 'time', 'en'));
        $this->assertFalse(Zend_LocaleTestHelper::getTranslation('xxxx', 'time'));

        $this->assertEquals('Wien', Zend_LocaleTestHelper::getTranslation('Europe/Vienna', 'citytotimezone', 'de_DE'));
        $this->assertEquals("St. John's", Zend_LocaleTestHelper::getTranslation('America/St_Johns', 'citytotimezone', 'en'));
        $this->assertFalse(Zend_LocaleTestHelper::getTranslation('xxxx', 'citytotimezone'));

        $this->assertEquals('Euro', Zend_LocaleTestHelper::getTranslation('EUR', 'nametocurrency', 'de_DE'));
        $this->assertEquals('Euro', Zend_LocaleTestHelper::getTranslation('EUR', 'nametocurrency', 'en'));
        $this->assertFalse(Zend_LocaleTestHelper::getTranslation('xxx', 'nametocurrency'));

        $this->assertEquals('EUR', Zend_LocaleTestHelper::getTranslation('Euro', 'currencytoname', 'de_DE'));
        $this->assertEquals('EUR', Zend_LocaleTestHelper::getTranslation('Euro', 'currencytoname', 'en'));
        $this->assertFalse(Zend_LocaleTestHelper::getTranslation('xxx', 'currencytoname'));

        $this->assertEquals('Fr.', Zend_LocaleTestHelper::getTranslation('CHF', 'currencysymbol', 'de_DE'));
        $this->assertEquals('Fr.', Zend_LocaleTestHelper::getTranslation('CHF', 'currencysymbol', 'en'));
        $this->assertFalse(Zend_LocaleTestHelper::getTranslation('xxx', 'currencysymbol'));

        $this->assertEquals('EUR', Zend_LocaleTestHelper::getTranslation('AT', 'currencytoregion', 'de_DE'));
        $this->assertEquals('EUR', Zend_LocaleTestHelper::getTranslation('AT', 'currencytoregion', 'en'));
        $this->assertFalse(Zend_LocaleTestHelper::getTranslation('xxx', 'currencytoregion'));

        $this->assertEquals('011 014 015 017 018', Zend_LocaleTestHelper::getTranslation('002', 'regiontoterritory', 'de_DE'));
        $this->assertEquals('011 014 015 017 018', Zend_LocaleTestHelper::getTranslation('002', 'regiontoterritory', 'en'));
        $this->assertFalse(Zend_LocaleTestHelper::getTranslation('xxx', 'regiontoterritory'));

        $this->assertEquals('AT BE CH DE LI LU', Zend_LocaleTestHelper::getTranslation('de', 'territorytolanguage', 'de_DE'));
        $this->assertEquals('AT BE CH DE LI LU', Zend_LocaleTestHelper::getTranslation('de', 'territorytolanguage', 'en'));
        $this->assertFalse(Zend_LocaleTestHelper::getTranslation('xxx', 'territorytolanguage'));
    }

    /**
     * test getTranslationList
     * expected true
     */
    public function testgetTranslationList()
    {
        try {
            $temp = Zend_LocaleTestHelper::getTranslationList();
            $this->fail();
        } catch (Zend_Locale_Exception $e) {
            $this->assertContains('Unknown list (', $e->getMessage());
        }

        $this->assertTrue(in_array('Deutsch', Zend_LocaleTestHelper::getTranslationList('language', 'de_DE')));
        $this->assertTrue(in_array('German', Zend_LocaleTestHelper::getTranslationList('language', 'en')));

        $this->assertTrue(in_array('Lateinisch', Zend_LocaleTestHelper::getTranslationList('script', 'de_DE')));
        $this->assertTrue(in_array('Latin', Zend_LocaleTestHelper::getTranslationList('script', 'en')));

        $this->assertTrue(in_array('Afrika', Zend_LocaleTestHelper::getTranslationList('territory', 'de_DE')));
        $this->assertTrue(in_array('Africa', Zend_LocaleTestHelper::getTranslationList('territory', 'en')));

        $this->assertTrue(in_array('Chinesischer Kalender', Zend_LocaleTestHelper::getTranslationList('type', 'de_DE', 'calendar')));
        $this->assertTrue(in_array('Chinese Calendar', Zend_LocaleTestHelper::getTranslationList('type', 'en', 'calendar')));

        $this->assertTrue(in_array('Januar', Zend_LocaleTestHelper::getTranslationList('month', 'de_DE')));
        $this->assertTrue(in_array('January', Zend_LocaleTestHelper::getTranslationList('month', 'en')));

        $this->assertTrue(in_array('Jan', Zend_LocaleTestHelper::getTranslationList('month', 'de_DE', array('gregorian', 'format', 'abbreviated'))));
        $this->assertTrue(in_array('Jan', Zend_LocaleTestHelper::getTranslationList('month', 'en', array('gregorian', 'format', 'abbreviated'))));

        $this->assertTrue(in_array('J', Zend_LocaleTestHelper::getTranslationList('month', 'de_DE', array('gregorian', 'stand-alone', 'narrow'))));
        $this->assertTrue(in_array('J', Zend_LocaleTestHelper::getTranslationList('month', 'en', array('gregorian', 'stand-alone', 'narrow'))));

        $this->assertTrue(in_array('Sonntag', Zend_LocaleTestHelper::getTranslationList('day', 'de_DE')));
        $this->assertTrue(in_array('Sunday', Zend_LocaleTestHelper::getTranslationList('day', 'en')));

        $this->assertTrue(in_array('So.', Zend_LocaleTestHelper::getTranslationList('day', 'de_DE', array('gregorian', 'format', 'abbreviated'))));
        $this->assertTrue(in_array('Sun', Zend_LocaleTestHelper::getTranslationList('day', 'en', array('gregorian', 'format', 'abbreviated'))));

        $this->assertTrue(in_array('S', Zend_LocaleTestHelper::getTranslationList('day', 'de_DE', array('gregorian', 'stand-alone', 'narrow'))));
        $this->assertTrue(in_array('S', Zend_LocaleTestHelper::getTranslationList('day', 'en', array('gregorian', 'stand-alone', 'narrow'))));

        $this->assertTrue(in_array('EEEE, d. MMMM y', Zend_LocaleTestHelper::getTranslationList('date', 'de_DE')));
        $this->assertTrue(in_array('EEEE, MMMM d, y', Zend_LocaleTestHelper::getTranslationList('date', 'en')));

        $this->assertTrue(in_array("HH:mm:ss zzzz", Zend_LocaleTestHelper::getTranslationList('time', 'de_DE')));
        $this->assertTrue(in_array("h:mm:ss a z", Zend_LocaleTestHelper::getTranslationList('time', 'en')));

        $this->assertTrue(in_array('Wien', Zend_LocaleTestHelper::getTranslationList('citytotimezone', 'de_DE')));
        $this->assertTrue(in_array("St. John's", Zend_LocaleTestHelper::getTranslationList('citytotimezone', 'en')));

        $this->assertTrue(in_array('Euro', Zend_LocaleTestHelper::getTranslationList('nametocurrency', 'de_DE')));
        $this->assertTrue(in_array('Euro', Zend_LocaleTestHelper::getTranslationList('nametocurrency', 'en')));

        $this->assertTrue(in_array('EUR', Zend_LocaleTestHelper::getTranslationList('currencytoname', 'de_DE')));
        $this->assertTrue(in_array('EUR', Zend_LocaleTestHelper::getTranslationList('currencytoname', 'en')));

        $this->assertTrue(in_array('Fr.', Zend_LocaleTestHelper::getTranslationList('currencysymbol', 'de_DE')));
        $this->assertTrue(in_array('Fr.', Zend_LocaleTestHelper::getTranslationList('currencysymbol', 'en')));

        $this->assertTrue(in_array('EUR', Zend_LocaleTestHelper::getTranslationList('currencytoregion', 'de_DE')));
        $this->assertTrue(in_array('EUR', Zend_LocaleTestHelper::getTranslationList('currencytoregion', 'en')));

        $this->assertTrue(in_array('AU NF NZ', Zend_LocaleTestHelper::getTranslationList('regiontoterritory', 'de_DE')));
        $this->assertTrue(in_array('AU NF NZ', Zend_LocaleTestHelper::getTranslationList('regiontoterritory', 'en')));

        $this->assertTrue(in_array('CZ', Zend_LocaleTestHelper::getTranslationList('territorytolanguage', 'de_DE')));
        $this->assertTrue(in_array('CZ', Zend_LocaleTestHelper::getTranslationList('territorytolanguage', 'en')));

        $char = Zend_LocaleTestHelper::getTranslationList('characters', 'de_DE');
        $this->assertEquals("[a ä b-o ö p-s ß t u ü v-z]", $char['characters']);
        $this->assertEquals("[á à ă â å ā æ ç é è ĕ ê ë ē í ì ĭ î ï ī ñ ó ò ŏ ô ø ō œ ú ù ŭ û ū ÿ]", $char['auxiliary']);
        $this->assertEquals("[a-z]", $char['currencySymbol']);

        $char = Zend_LocaleTestHelper::getTranslationList('characters', 'en');
        $this->assertEquals("[a-z]", $char['characters']);
        $this->assertEquals("[á à ă â å ä ã ā æ ç é è ĕ ê ë ē í ì ĭ î ï ī ñ ó ò ŏ ô ö ø ō œ ß ú ù ŭ û ü ū ÿ]", $char['auxiliary']);
        $this->assertEquals("[a-c č d-l ł m-z]", $char['currencySymbol']);
    }

    /**
     * test for equality
     * expected string
     */
    public function testEquals()
    {
        $value = new Zend_LocaleTestHelper('de_DE');
        $serial = new Zend_LocaleTestHelper('de_DE');
        $serial2 = new Zend_LocaleTestHelper('de_AT');
        $this->assertTrue($value->equals($serial));
        $this->assertFalse($value->equals($serial2));
    }

    /**
     * test getQuestion
     * expected true
     */
    public function testgetQuestion()
    {
        $list = Zend_LocaleTestHelper::getQuestion();
        $this->assertTrue(isset($list['yes']));

        $list = Zend_LocaleTestHelper::getQuestion('de');
        $this->assertEquals('ja', $list['yes']);

        $this->assertTrue(is_array(Zend_LocaleTestHelper::getQuestion('auto')));

        try {
            $this->assertTrue(is_array(Zend_LocaleTestHelper::getQuestion('browser')));
        } catch (Zend_Locale_Exception $e) {
            $this->assertContains('Autodetection', $e->getMessage());
        }

        try {
            $this->assertTrue(is_array(Zend_LocaleTestHelper::getQuestion('environment')));
        } catch (Zend_Locale_Exception $e) {
            $this->assertContains('ocale', $e->getMessage());
        }
    }

    /**
     * test getBrowser
     * expected true
     */
    public function testgetBrowser()
    {
        Zend_LocaleTestHelper::resetObject();
        $value = new Zend_LocaleTestHelper();
        $list = $value->getBrowser();
        if (empty($list)) {
            $this->markTestSkipped('Browser autodetection not possible in current environment');
        }
        $this->assertTrue(isset($list['de']));
        $this->assertEquals(array('de' => 1, 'en_UK' => 0.5, 'en_US' => 0.5,
                                  'en' => 0.5, 'fr_FR' => 0.2, 'fr' => 0.2), $list);

        Zend_LocaleTestHelper::resetObject();
        putenv("HTTP_ACCEPT_LANGUAGE=");

        $value = new Zend_LocaleTestHelper();
        $list = $value->getBrowser();
        $this->assertEquals(array(), $list);
    }

    /**
     * test getHttpCharset
     * expected true
     */
    public function testgetHttpCharset()
    {
        Zend_LocaleTestHelper::resetObject();
        putenv("HTTP_ACCEPT_CHARSET=");
        $value = new Zend_LocaleTestHelper();
        $list = $value->getHttpCharset();
        $this->assertTrue(empty($list));

        Zend_LocaleTestHelper::resetObject();
        putenv("HTTP_ACCEPT_CHARSET=,iso-8859-1, utf-8, utf-16, *;q=0.1");
        $value = new Zend_LocaleTestHelper();
        $list = $value->getHttpCharset();
        $this->assertTrue(isset($list['utf-8']));
    }

    /**
     * test isLocale
     * expected boolean
     */
    public function testIsLocale()
    {
        $locale = new Zend_LocaleTestHelper('ar');
        $this->assertTrue(Zend_LocaleTestHelper::isLocale($locale));
        $this->assertTrue(Zend_LocaleTestHelper::isLocale('de'));
        $this->assertTrue(Zend_LocaleTestHelper::isLocale('de_AT'));
        $this->assertTrue(Zend_LocaleTestHelper::isLocale('de_xx'));
        $this->assertFalse(Zend_LocaleTestHelper::isLocale('yy'));
        $this->assertFalse(Zend_LocaleTestHelper::isLocale(1234));
        $this->assertFalse(Zend_LocaleTestHelper::isLocale('', true));
        $this->assertFalse(Zend_LocaleTestHelper::isLocale('', false));
        $this->assertTrue(Zend_LocaleTestHelper::isLocale('auto'));
        $this->assertTrue(Zend_LocaleTestHelper::isLocale('browser'));
        if (count(Zend_Locale::getEnvironment()) != 0) {
            $this->assertTrue(Zend_LocaleTestHelper::isLocale('environment'));
        }

        set_error_handler(array($this, 'errorHandlerIgnore'));
        Zend_LocaleTestHelper::$compatibilityMode = true;
        $this->assertEquals('ar', Zend_LocaleTestHelper::isLocale($locale));
        $this->assertEquals('de', Zend_LocaleTestHelper::isLocale('de'));
        $this->assertEquals('de_AT', Zend_LocaleTestHelper::isLocale('de_AT'));
        $this->assertEquals('de', Zend_LocaleTestHelper::isLocale('de_xx'));
        $this->assertFalse(Zend_LocaleTestHelper::isLocale('yy'));
        $this->assertFalse(Zend_LocaleTestHelper::isLocale(1234));
        $this->assertFalse(Zend_LocaleTestHelper::isLocale('', true));
        $this->assertFalse(Zend_LocaleTestHelper::isLocale('', false));
        $this->assertTrue(is_string(Zend_LocaleTestHelper::isLocale('auto')));
        $this->assertTrue(is_string(Zend_LocaleTestHelper::isLocale('browser')));
        if (count(Zend_Locale::getEnvironment()) != 0) {
            $this->assertTrue(is_string(Zend_LocaleTestHelper::isLocale('environment')));
        }
        restore_error_handler();
    }

    /**
     * test isLocale
     * expected boolean
     */
    public function testGetLocaleList()
    {
        $this->assertTrue(is_array(Zend_LocaleTestHelper::getLocaleList()));
    }

    /**
     * test setDefault
     * expected true
     */
    public function testsetDefault()
    {
        try {
            Zend_LocaleTestHelper::setDefault('auto');
            $this->fail();
        } catch (Zend_Locale_Exception $e) {
            $this->assertContains("full qualified locale", $e->getMessage());
        }

        try {
            Zend_LocaleTestHelper::setDefault('de_XX');
            $locale = new Zend_LocaleTestHelper();
            $this->assertTrue($locale instanceof Zend_Locale); // should defer to 'de' or any other standard locale
        } catch (Zend_Locale_Exception $e) {
            $this->fail(); // de_XX should automatically degrade to 'de'
        }

        try {
            Zend_LocaleTestHelper::setDefault('xy_ZZ');
            $this->fail();
        } catch (Zend_Locale_Exception $e) {
            $this->assertContains("Unknown locale", $e->getMessage());
        }

        try {
            Zend_LocaleTestHelper::setDefault('de', 101);
            $this->fail();
        } catch (Zend_Locale_Exception $e) {
            $this->assertContains("Quality must be between", $e->getMessage());
        }

        try {
            Zend_LocaleTestHelper::setDefault('de', 90);
            $locale = new Zend_LocaleTestHelper();
            $this->assertTrue($locale instanceof Zend_Locale); // should defer to 'de' or any other standard locale
        } catch (Zend_Locale_Exception $e) {
            $this->fail();
        }

        try {
            Zend_LocaleTestHelper::setDefault('de-AT', 90);
            $locale = new Zend_LocaleTestHelper();
            $this->assertTrue($locale instanceof Zend_Locale);
        } catch (Zend_Locale_Exception $e) {
            $this->fail();
        }
    }

    /**
     * Test getDefault
     */
    public function testgetDefault() {
        Zend_LocaleTestHelper::setDefault('de');
        $this->assertTrue(array_key_exists('de', Zend_LocaleTestHelper::getDefault()));

        // compatibility tests
        set_error_handler(array($this, 'errorHandlerIgnore'));
        Zend_LocaleTestHelper::$compatibilityMode = true;
        $this->assertTrue(array_key_exists('de', Zend_LocaleTestHelper::getDefault(Zend_Locale::BROWSER)));
        restore_error_handler();
    }

    /**
     * Caching method tests
     */
    public function testCaching()
    {
        $cache = Zend_LocaleTestHelper::getCache();
        $this->assertTrue($cache instanceof Zend_Cache_Core);
        $this->assertTrue(Zend_LocaleTestHelper::hasCache());

        Zend_LocaleTestHelper::clearCache();
        $this->assertTrue(Zend_LocaleTestHelper::hasCache());

        Zend_LocaleTestHelper::removeCache();
        $this->assertFalse(Zend_LocaleTestHelper::hasCache());
    }

    /**
     * Caching method tests
     */
    public function testFindingTheProperLocale()
    {
        $this->assertTrue(is_string(Zend_LocaleTestHelper::findLocale()));
        $this->assertEquals('de', Zend_LocaleTestHelper::findLocale('de'));
        $this->assertEquals('de', Zend_LocaleTestHelper::findLocale('de_XX'));

        try {
            $locale = Zend_LocaleTestHelper::findLocale('xx_YY');
            $this->fail();
        } catch (Zend_Locale_Exception $e) {
            $this->assertContains('is no known locale', $e->getMessage());
        }

        Zend_Registry::set('Zend_Locale', 'de');
        $this->assertEquals('de', Zend_LocaleTestHelper::findLocale());
    }

    /**
     * test isLocale
     * expected boolean
     */
    public function testZF3617() {
        $value = new Zend_LocaleTestHelper('en-US');
        $this->assertEquals('en_US', $value->toString());
    }

    /**
     * @ZF4963
     */
    public function testZF4963() {
        $value = new Zend_LocaleTestHelper();
        $locale = $value->toString();
        $this->assertTrue(!empty($locale));

        $this->assertFalse(Zend_LocaleTestHelper::isLocale(null));

        $value = new Zend_LocaleTestHelper(0);
        $value = $value->toString();
        $this->assertTrue(!empty($value));

        $this->assertFalse(Zend_LocaleTestHelper::isLocale(0));
    }

    /**
     * test MultiPartLocales
     * expected boolean
     */
    public function testLongLocale()
    {
        $locale = new Zend_LocaleTestHelper('de_Latn_DE');
        $this->assertEquals('de_DE', $locale->toString());
        $this->assertTrue(Zend_LocaleTestHelper::isLocale('de_Latn_CAR_DE_sup3_win'));

        $locale = new Zend_LocaleTestHelper('de_Latn_DE');
        $this->assertEquals('de_DE', $locale->toString());

        $this->assertEquals('fr_FR', Zend_Locale::findLocale('fr-Arab-FR'));
    }

    /**
     * test SunLocales
     * expected boolean
     */
    public function testSunLocale()
    {
        $this->assertTrue(Zend_LocaleTestHelper::isLocale('de_DE.utf8'));
        $this->assertFalse(Zend_LocaleTestHelper::isLocale('de.utf8.DE'));
    }

    /**
     * @ZF-8030
     */
    public function testFailedLocaleOnPreTranslations()
    {
        $this->assertEquals('Andorra', Zend_LocaleTestHelper::getTranslation('AD', 'country', 'gl_GL'));
    }

    /**
     * @ZF-9488
     */
    public function testTerritoryToGetLocale() {
        $value = Zend_Locale::findLocale('US');
        $this->assertEquals('en_US', $value);

        $value = new Zend_Locale('US');
        $this->assertEquals('en_US', $value->toString());

        $value = new Zend_Locale('TR');
        $this->assertEquals('tr_TR', $value->toString());
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

class Zend_LocaleTestHelper extends Zend_Locale
{
    public static function resetObject()
    {
        self::$_auto        = null;
        self::$_environment = null;
        self::$_browser     = null;
    }
}

// Call Zend_LocaleTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_LocaleTest::main") {
    Zend_LocaleTest::main();
}
