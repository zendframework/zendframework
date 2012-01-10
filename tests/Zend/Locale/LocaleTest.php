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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Locale;

use Zend\Locale\Locale,
    Zend\Locale\Exception\InvalidArgumentException,
    Zend\Locale\Exception\UnexpectedValueException,
    Zend\Cache\StorageFactory as CacheFactory,
    Zend\Cache\Storage\Adapter as CacheAdapter;

/**
 * @category   Zend
 * @package    Zend_Locale
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Locale
 */
class LocaleTest extends \PHPUnit_Framework_TestCase
{
    private $_cache  = null;
    private $_locale = null;

    public function setUp()
    {
        $this->_cacheDir = sys_get_temp_dir() . '/zend_locale';
        $this->_removeRecursive($this->_cacheDir);
        mkdir($this->_cacheDir);

        $this->_locale = setlocale(LC_ALL, 0);
        setlocale(LC_ALL, 'de');
        $this->_cache = CacheFactory::factory(array(
            'adapter' => array(
                'name' => 'Filesystem',
                'options' => array(
                    'ttl'       => 120,
                    'cache_dir' => $this->_cacheDir,
                )
            ),
            'plugins' => array(
                array(
                    'name' => 'serializer',
                    'options' => array(
                        'serializer' => 'php_serialize',
                    ),
                ),
            ),
        ));
        LocaleTestHelper::resetObject();
        LocaleTestHelper::setCache($this->_cache);
        putenv("HTTP_ACCEPT_LANGUAGE=,de,en-UK-US;q=0.5,fr_FR;q=0.2");
    }

    public function tearDown()
    {
        if ($this->_cache instanceof CacheAdapter) {
            $this->_cache->clear(CacheAdapter::MATCH_ALL);
            $this->_removeRecursive($this->_cacheDir);
        }
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

    protected function _removeRecursive($dir)
    {
        if (file_exists($dir)) {
            $dirIt = new \DirectoryIterator($dir);
            foreach ($dirIt as $entry) {
                $fname = $entry->getFilename();
                if ($fname == '.' || $fname == '..') {
                    continue;
                }

                if ($entry->isFile()) {
                    unlink($entry->getPathname());
                } else {
                    $this->_removeRecursive($entry->getPathname());
                }
            }

            rmdir($dir);
        }
    }

    /**
     * test for object creation
     * expected object instance
     */
    public function testObjectCreation()
    {
        $this->assertTrue(LocaleTestHelper::isLocale('de'));

        $this->assertTrue(new LocaleTestHelper() instanceof Locale);
        $this->assertTrue(new LocaleTestHelper('root') instanceof Locale);
        try {
            $locale = new LocaleTestHelper(Locale::ENVIRONMENT);
            $this->assertTrue($locale instanceof Locale);
        } catch (InvalidArgumentException $e) {
            // ignore environments where the locale can not be detected
            $this->assertContains('Autodetection', $e->getMessage());
        }

        try {
            $this->assertTrue(new LocaleTestHelper(Locale::BROWSER) instanceof Locale);
        } catch (InvalidArgumentException $e) {
            // ignore environments where the locale can not be detected
            $this->assertContains('Autodetection', $e->getMessage());
        }

        $locale = new LocaleTestHelper('de');
        $this->assertTrue(new LocaleTestHelper($locale) instanceof Locale);

        $locale = new LocaleTestHelper('auto');
        $this->assertTrue(new LocaleTestHelper($locale) instanceof Locale);
    }

    /**
     * test for serialization
     * expected string
     */
    public function testSerialize()
    {
        $value = new LocaleTestHelper('de_DE');
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
        $value = new LocaleTestHelper('de_DE');
        $this->assertEquals('de_DE', $value->toString());
        $this->assertEquals('de_DE', $value->__toString());
    }

    /**
     * test getEnvironment
     * expected true
     */
    public function testLocaleDetail()
    {
        $value = new LocaleTestHelper('de_AT');
        $this->assertEquals('de', $value->getLanguage());
        $this->assertEquals('AT', $value->getRegion());

        $value = new LocaleTestHelper('en_US');
        $this->assertEquals('en', $value->getLanguage());
        $this->assertEquals('US', $value->getRegion());

        $value = new LocaleTestHelper('en');
        $this->assertEquals('en', $value->getLanguage());
        $this->assertFalse($value->getRegion());
    }

    /**
     * test getEnvironment
     * expected true
     */
    public function testEnvironment()
    {
        $value = new LocaleTestHelper();
        $default = $value->getEnvironment();
        $this->assertTrue(is_array($default));
    }

    /**
     * test getBrowser
     * expected true
     */
    public function testBrowser()
    {
        $value = new LocaleTestHelper();
        $default = $value->getBrowser();
        $this->assertTrue(is_array($default));
    }

    /**
     * test clone
     * expected true
     */
    public function testCloning()
    {
        $value = new LocaleTestHelper('de_DE');
        $newvalue = clone $value;
        $this->assertEquals($value->toString(), $newvalue->toString());
    }

    /**
     * test setLocale
     * expected true
     */
    public function testsetLocale()
    {
        $value = new LocaleTestHelper('de_DE');
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
        } catch (UnexpectedValueException $e) {
            // ignore environments where the locale can not be detected
            $this->assertContains('Autodetection', $e->getMessage());
        }

        try {
            $value->setLocale('environment');
            $this->assertTrue(is_string($value->toString()));
        } catch (UnexpectedValueException $e) {
            // ignore environments where the locale can not be detected
            $this->assertContains('Autodetection', $e->getMessage());
        }
    }

    /**
     * test getTranslationList('language')
     * expected true
     */
    public function testgetLanguageTranslationList()
    {
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $list = LocaleTestHelper::getTranslationList('language');
        $this->assertTrue(is_array($list));
        $list = LocaleTestHelper::getTranslationList('language', 'de');
        $this->assertTrue(is_array($list));
        restore_error_handler();
    }

    /**
     * test getTranslation('language')
     * expected true
     */
    public function testgetLanguageTranslation()
    {
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $this->assertEquals('Deutsch', LocaleTestHelper::getTranslation('de', 'language', 'de_AT'));
        $this->assertEquals('German',  LocaleTestHelper::getTranslation('de', 'language', 'en'));
        $this->assertFalse(LocaleTestHelper::getTranslation('xyz', 'language'));
        $this->assertTrue(is_string(LocaleTestHelper::getTranslation('de', 'language', 'auto')));
        restore_error_handler();
    }

    /**
     * test getTranslationList('script')
     * expected true
     */
    public function testgetScriptTranslationList()
    {
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $list = LocaleTestHelper::getTranslationList('script');
        $this->assertTrue(is_array($list));

        $list = LocaleTestHelper::getTranslationList('script', 'de');
        $this->assertTrue(is_array($list));
        restore_error_handler();
    }

    /**
     * test getTranslation('script')
     * expected true
     */
    public function testgetScriptTranslation()
    {
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $this->assertEquals('Arabisch', LocaleTestHelper::getTranslation('Arab', 'script', 'de_AT'));
        $this->assertEquals('Arabic', LocaleTestHelper::getTranslation('Arab', 'script', 'en'));
        $this->assertFalse(LocaleTestHelper::getTranslation('xyz', 'script'));
        restore_error_handler();
    }

    /**
     * test getTranslationList('country')
     * expected true
     */
    public function testgetCountryTranslationList()
    {
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $list = LocaleTestHelper::getTranslationList('territory');
        $this->assertTrue(is_array($list));

        $list = LocaleTestHelper::getTranslationList('territory', 'de');
        $this->assertEquals("Vereinigte Staaten", $list['US']);
        restore_error_handler();
    }

    /**
     * test getTranslation('country')
     * expected true
     */
    public function testgetCountryTranslation()
    {
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $this->assertEquals('Deutschland', LocaleTestHelper::getTranslation('DE', 'country', 'de_DE'));
        $this->assertEquals('Germany', LocaleTestHelper::getTranslation('DE', 'country', 'en'));
        $this->assertFalse(LocaleTestHelper::getTranslation('xyz', 'country'));
        restore_error_handler();
    }

    /**
     * test getTranslationList('territory')
     * expected true
     */
    public function testgetTerritoryTranslationList()
    {
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $list = LocaleTestHelper::getTranslationList('territory');
        $this->assertTrue(is_array($list));

        $list = LocaleTestHelper::getTranslationList('territory', 'de');
        $this->assertTrue(is_array($list));
        restore_error_handler();
    }

    /**
     * test getTranslation('territory')
     * expected true
     */
    public function testgetTerritoryTranslation()
    {
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $this->assertEquals('Afrika', LocaleTestHelper::getTranslation('002', 'territory', 'de_AT'));
        $this->assertEquals('Africa', LocaleTestHelper::getTranslation('002', 'territory', 'en'));
        $this->assertFalse(LocaleTestHelper::getTranslation('xyz', 'territory'));
        $this->assertTrue(is_string(LocaleTestHelper::getTranslation('002', 'territory', 'auto')));
        restore_error_handler();
    }

    /**
     * test getTranslation
     * expected true
     */
    public function testgetTranslation()
    {
        try {
            $temp = LocaleTestHelper::getTranslation('xx');
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertContains('Unknown detail (', $e->getMessage());
        }

        $this->assertEquals('Deutsch', LocaleTestHelper::getTranslation('de', 'language', 'de_DE'));
        $this->assertEquals('German', LocaleTestHelper::getTranslation('de', 'language', 'en'));
        $this->assertFalse(LocaleTestHelper::getTranslation('xx', 'language'));

        $this->assertEquals('Lateinisch', LocaleTestHelper::getTranslation('Latn', 'script', 'de_DE'));
        $this->assertEquals('Latin', LocaleTestHelper::getTranslation('Latn', 'script', 'en'));
        $this->assertFalse(LocaleTestHelper::getTranslation('xyxy', 'script'));

        $this->assertEquals('Österreich', LocaleTestHelper::getTranslation('AT', 'country', 'de_DE'));
        $this->assertEquals('Austria', LocaleTestHelper::getTranslation('AT', 'country', 'en'));
        $this->assertFalse(LocaleTestHelper::getTranslation('xx', 'country'));

        $this->assertEquals('Afrika', LocaleTestHelper::getTranslation('002', 'territory', 'de_DE'));
        $this->assertEquals('Africa', LocaleTestHelper::getTranslation('002', 'territory', 'en'));
        $this->assertFalse(LocaleTestHelper::getTranslation('xxx', 'territory'));

        $this->assertEquals('Januar', LocaleTestHelper::getTranslation('1', 'month', 'de_DE'));
        $this->assertEquals('January', LocaleTestHelper::getTranslation('1', 'month', 'en'));
        $this->assertFalse(LocaleTestHelper::getTranslation('x', 'month'));

        $this->assertEquals('Jan', LocaleTestHelper::getTranslation(array('gregorian', 'format', 'abbreviated', '1'), 'month', 'de_DE'));
        $this->assertEquals('Jan', LocaleTestHelper::getTranslation(array('gregorian', 'format', 'abbreviated', '1'), 'month', 'en'));
        $this->assertFalse(LocaleTestHelper::getTranslation(array('gregorian', 'format', 'abbreviated', 'x'), 'month'));

        $this->assertEquals('J', LocaleTestHelper::getTranslation(array('gregorian', 'stand-alone', 'narrow', '1'), 'month', 'de_DE'));
        $this->assertEquals('J', LocaleTestHelper::getTranslation(array('gregorian', 'stand-alone', 'narrow', '1'), 'month', 'en'));
        $this->assertFalse(LocaleTestHelper::getTranslation(array('gregorian', 'stand-alone', 'narrow', 'x'), 'month'));

        $this->assertEquals('Sonntag', LocaleTestHelper::getTranslation('sun', 'day', 'de_DE'));
        $this->assertEquals('Sunday', LocaleTestHelper::getTranslation('sun', 'day', 'en'));
        $this->assertFalse(LocaleTestHelper::getTranslation('xxx', 'day'));

        $this->assertEquals('So.', LocaleTestHelper::getTranslation(array('gregorian', 'format', 'abbreviated', 'sun'), 'day', 'de_DE'));
        $this->assertEquals('Sun', LocaleTestHelper::getTranslation(array('gregorian', 'format', 'abbreviated', 'sun'), 'day', 'en'));
        $this->assertFalse(LocaleTestHelper::getTranslation(array('gregorian', 'format', 'abbreviated', 'xxx'), 'day'));

        $this->assertEquals('S', LocaleTestHelper::getTranslation(array('gregorian', 'stand-alone', 'narrow', 'sun'), 'day', 'de_DE'));
        $this->assertEquals('S', LocaleTestHelper::getTranslation(array('gregorian', 'stand-alone', 'narrow', 'sun'), 'day', 'en'));
        $this->assertFalse(LocaleTestHelper::getTranslation(array('gregorian', 'stand-alone', 'narrow', 'xxx'), 'day'));

        $this->assertEquals('EEEE, d. MMMM y', LocaleTestHelper::getTranslation('full', 'date', 'de_DE'));
        $this->assertEquals('EEEE, MMMM d, y', LocaleTestHelper::getTranslation('full', 'date', 'en'));
        $this->assertFalse(LocaleTestHelper::getTranslation('xxxx', 'date'));

        $this->assertEquals("HH:mm:ss zzzz", LocaleTestHelper::getTranslation('full', 'time', 'de_DE'));
        $this->assertEquals('h:mm:ss a zzzz', LocaleTestHelper::getTranslation('full', 'time', 'en'));
        $this->assertFalse(LocaleTestHelper::getTranslation('xxxx', 'time'));

        $this->assertEquals('Wien', LocaleTestHelper::getTranslation('Europe/Vienna', 'citytotimezone', 'de_DE'));
        $this->assertEquals("St. John's", LocaleTestHelper::getTranslation('America/St_Johns', 'citytotimezone', 'en'));
        $this->assertFalse(LocaleTestHelper::getTranslation('xxxx', 'citytotimezone'));

        $this->assertEquals('Euro', LocaleTestHelper::getTranslation('EUR', 'nametocurrency', 'de_DE'));
        $this->assertEquals('Euro', LocaleTestHelper::getTranslation('EUR', 'nametocurrency', 'en'));
        $this->assertFalse(LocaleTestHelper::getTranslation('xxx', 'nametocurrency'));

        $this->assertEquals('EUR', LocaleTestHelper::getTranslation('Euro', 'currencytoname', 'de_DE'));
        $this->assertEquals('EUR', LocaleTestHelper::getTranslation('Euro', 'currencytoname', 'en'));
        $this->assertFalse(LocaleTestHelper::getTranslation('xxx', 'currencytoname'));

        $this->assertEquals('Fr.', LocaleTestHelper::getTranslation('CHF', 'currencysymbol', 'de_DE'));
        $this->assertEquals('Fr.', LocaleTestHelper::getTranslation('CHF', 'currencysymbol', 'en'));
        $this->assertFalse(LocaleTestHelper::getTranslation('xxx', 'currencysymbol'));

        $this->assertEquals('EUR', LocaleTestHelper::getTranslation('AT', 'currencytoregion', 'de_DE'));
        $this->assertEquals('EUR', LocaleTestHelper::getTranslation('AT', 'currencytoregion', 'en'));
        $this->assertFalse(LocaleTestHelper::getTranslation('xxx', 'currencytoregion'));

        $this->assertEquals('011 014 015 017 018', LocaleTestHelper::getTranslation('002', 'regiontoterritory', 'de_DE'));
        $this->assertEquals('011 014 015 017 018', LocaleTestHelper::getTranslation('002', 'regiontoterritory', 'en'));
        $this->assertFalse(LocaleTestHelper::getTranslation('xxx', 'regiontoterritory'));

        $this->assertEquals('AT BE CH DE LI LU', LocaleTestHelper::getTranslation('de', 'territorytolanguage', 'de_DE'));
        $this->assertEquals('AT BE CH DE LI LU', LocaleTestHelper::getTranslation('de', 'territorytolanguage', 'en'));
        $this->assertFalse(LocaleTestHelper::getTranslation('xxx', 'territorytolanguage'));
    }

    /**
     * test getTranslationList
     * expected true
     */
    public function testgetTranslationList()
    {
        try {
            $temp = LocaleTestHelper::getTranslationList();
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertContains('Unknown list (', $e->getMessage());
        }

        $this->assertTrue(in_array('Deutsch', LocaleTestHelper::getTranslationList('language', 'de_DE')));
        $this->assertTrue(in_array('German', LocaleTestHelper::getTranslationList('language', 'en')));

        $this->assertTrue(in_array('Lateinisch', LocaleTestHelper::getTranslationList('script', 'de_DE')));
        $this->assertTrue(in_array('Latin', LocaleTestHelper::getTranslationList('script', 'en')));

        $this->assertTrue(in_array('Afrika', LocaleTestHelper::getTranslationList('territory', 'de_DE')));
        $this->assertTrue(in_array('Africa', LocaleTestHelper::getTranslationList('territory', 'en')));

        $this->assertTrue(in_array('Chinesischer Kalender', LocaleTestHelper::getTranslationList('type', 'de_DE', 'calendar')));
        $this->assertTrue(in_array('Chinese Calendar', LocaleTestHelper::getTranslationList('type', 'en', 'calendar')));

        $this->assertTrue(in_array('Januar', LocaleTestHelper::getTranslationList('month', 'de_DE')));
        $this->assertTrue(in_array('January', LocaleTestHelper::getTranslationList('month', 'en')));

        $this->assertTrue(in_array('Jan', LocaleTestHelper::getTranslationList('month', 'de_DE', array('gregorian', 'format', 'abbreviated'))));
        $this->assertTrue(in_array('Jan', LocaleTestHelper::getTranslationList('month', 'en', array('gregorian', 'format', 'abbreviated'))));

        $this->assertTrue(in_array('J', LocaleTestHelper::getTranslationList('month', 'de_DE', array('gregorian', 'stand-alone', 'narrow'))));
        $this->assertTrue(in_array('J', LocaleTestHelper::getTranslationList('month', 'en', array('gregorian', 'stand-alone', 'narrow'))));

        $this->assertTrue(in_array('Sonntag', LocaleTestHelper::getTranslationList('day', 'de_DE')));
        $this->assertTrue(in_array('Sunday', LocaleTestHelper::getTranslationList('day', 'en')));

        $this->assertTrue(in_array('So.', LocaleTestHelper::getTranslationList('day', 'de_DE', array('gregorian', 'format', 'abbreviated'))));
        $this->assertTrue(in_array('Sun', LocaleTestHelper::getTranslationList('day', 'en', array('gregorian', 'format', 'abbreviated'))));

        $this->assertTrue(in_array('S', LocaleTestHelper::getTranslationList('day', 'de_DE', array('gregorian', 'stand-alone', 'narrow'))));
        $this->assertTrue(in_array('S', LocaleTestHelper::getTranslationList('day', 'en', array('gregorian', 'stand-alone', 'narrow'))));

        $this->assertTrue(in_array('EEEE, d. MMMM y', LocaleTestHelper::getTranslationList('date', 'de_DE')));
        $this->assertTrue(in_array('EEEE, MMMM d, y', LocaleTestHelper::getTranslationList('date', 'en')));

        $this->assertTrue(in_array("HH:mm:ss zzzz", LocaleTestHelper::getTranslationList('time', 'de_DE')));
        $this->assertTrue(in_array("h:mm:ss a z", LocaleTestHelper::getTranslationList('time', 'en')));

        $this->assertTrue(in_array('Wien', LocaleTestHelper::getTranslationList('citytotimezone', 'de_DE')));
        $this->assertTrue(in_array("St. John's", LocaleTestHelper::getTranslationList('citytotimezone', 'en')));

        $this->assertTrue(in_array('Euro', LocaleTestHelper::getTranslationList('nametocurrency', 'de_DE')));
        $this->assertTrue(in_array('Euro', LocaleTestHelper::getTranslationList('nametocurrency', 'en')));

        $this->assertTrue(in_array('EUR', LocaleTestHelper::getTranslationList('currencytoname', 'de_DE')));
        $this->assertTrue(in_array('EUR', LocaleTestHelper::getTranslationList('currencytoname', 'en')));

        $this->assertTrue(in_array('Fr.', LocaleTestHelper::getTranslationList('currencysymbol', 'de_DE')));
        $this->assertTrue(in_array('Fr.', LocaleTestHelper::getTranslationList('currencysymbol', 'en')));

        $this->assertTrue(in_array('EUR', LocaleTestHelper::getTranslationList('currencytoregion', 'de_DE')));
        $this->assertTrue(in_array('EUR', LocaleTestHelper::getTranslationList('currencytoregion', 'en')));

        $this->assertTrue(in_array('AU NF NZ', LocaleTestHelper::getTranslationList('regiontoterritory', 'de_DE')));
        $this->assertTrue(in_array('AU NF NZ', LocaleTestHelper::getTranslationList('regiontoterritory', 'en')));

        $this->assertTrue(in_array('CZ', LocaleTestHelper::getTranslationList('territorytolanguage', 'de_DE')));
        $this->assertTrue(in_array('CZ', LocaleTestHelper::getTranslationList('territorytolanguage', 'en')));

        $char = LocaleTestHelper::getTranslationList('characters', 'de_DE');
        $this->assertEquals("[a ä b-o ö p-s ß t u ü v-z]", $char['characters']);
        $this->assertEquals("[á à ă â å ā æ ç é è ĕ ê ë ē í ì ĭ î ï ī ñ ó ò ŏ ô ø ō œ ú ù ŭ û ū ÿ]", $char['auxiliary']);
        $this->assertEquals("[a-z]", $char['currencySymbol']);

        $char = LocaleTestHelper::getTranslationList('characters', 'en');
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
        $value = new LocaleTestHelper('de_DE');
        $serial = new LocaleTestHelper('de_DE');
        $serial2 = new LocaleTestHelper('de_AT');
        $this->assertTrue($value->equals($serial));
        $this->assertFalse($value->equals($serial2));
    }

    /**
     * test getQuestion
     * expected true
     */
    public function testgetQuestion()
    {
        $list = LocaleTestHelper::getQuestion();
        $this->assertTrue(isset($list['yes']));

        $list = LocaleTestHelper::getQuestion('de');
        $this->assertEquals('ja', $list['yes']);

        $this->assertTrue(is_array(LocaleTestHelper::getQuestion('auto')));

        try {
            $this->assertTrue(is_array(LocaleTestHelper::getQuestion('browser')));
        } catch (InvalidArgumentException $e) {
            $this->assertContains('Autodetection', $e->getMessage());
        }

        try {
            $this->assertTrue(is_array(LocaleTestHelper::getQuestion('environment')));
        } catch (InvalidArgumentException $e) {
            $this->assertContains('ocale', $e->getMessage());
        }
    }

    /**
     * test getBrowser
     * expected true
     */
    public function testgetBrowser()
    {
        LocaleTestHelper::resetObject();
        $value = new LocaleTestHelper();
        $list = $value->getBrowser();
        if (empty($list)) {
            $this->markTestSkipped('Browser autodetection not possible in current environment');
        }
        $this->assertTrue(isset($list['de']));
        $this->assertEquals(array('de' => 1, 'en_UK' => 0.5, 'en_US' => 0.5,
                                  'en' => 0.5, 'fr_FR' => 0.2, 'fr' => 0.2), $list);

        LocaleTestHelper::resetObject();
        putenv("HTTP_ACCEPT_LANGUAGE=");

        $value = new LocaleTestHelper();
        $list = $value->getBrowser();
        $this->assertEquals(array(), $list);
    }

    /**
     * test getHttpCharset
     * expected true
     */
    public function testgetHttpCharset()
    {
        LocaleTestHelper::resetObject();
        putenv("HTTP_ACCEPT_CHARSET=");
        $value = new LocaleTestHelper();
        $list = $value->getHttpCharset();
        $this->assertTrue(empty($list));

        LocaleTestHelper::resetObject();
        putenv("HTTP_ACCEPT_CHARSET=,iso-8859-1, utf-8, utf-16, *;q=0.1");
        $value = new LocaleTestHelper();
        $list = $value->getHttpCharset();
        $this->assertTrue(isset($list['utf-8']));
    }

    /**
     * test isLocale
     * expected boolean
     */
    public function testIsLocale()
    {
        $locale = new LocaleTestHelper('ar');
        $this->assertTrue(LocaleTestHelper::isLocale($locale));
        $this->assertTrue(LocaleTestHelper::isLocale('de'));
        $this->assertTrue(LocaleTestHelper::isLocale('de_AT'));
        $this->assertTrue(LocaleTestHelper::isLocale('de_xx'));
        $this->assertFalse(LocaleTestHelper::isLocale('yy'));
        $this->assertFalse(LocaleTestHelper::isLocale(1234));
        $this->assertFalse(LocaleTestHelper::isLocale('', true));
        $this->assertFalse(LocaleTestHelper::isLocale('', false));
        $this->assertTrue(LocaleTestHelper::isLocale('auto'));
        $this->assertTrue(LocaleTestHelper::isLocale('browser'));
        if (count(Locale::getEnvironment()) != 0) {
            $this->assertTrue(LocaleTestHelper::isLocale('environment'));
        }
    }

    /**
     * test isLocale
     * expected boolean
     */
    public function testGetLocaleList()
    {
        $this->assertTrue(is_array(LocaleTestHelper::getLocaleList()));
    }

    /**
     * test setFallback
     * expected true
     */
    public function testsetFallback()
    {
        try {
            LocaleTestHelper::setFallback('auto');
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertContains("fully qualified locale", $e->getMessage());
        }

        try {
            LocaleTestHelper::setFallback('de_XX');
            $locale = new LocaleTestHelper();
            $this->assertTrue($locale instanceof Locale); // should defer to 'de' or any other standard locale
        } catch (InvalidArgumentException $e) {
            $this->fail(); // de_XX should automatically degrade to 'de'
        }

        try {
            LocaleTestHelper::setFallback('xy_ZZ');
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertContains("Unknown locale", $e->getMessage());
        }

        try {
            LocaleTestHelper::setFallback('de', 101);
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertContains("Quality must be between", $e->getMessage());
        }

        try {
            LocaleTestHelper::setFallback('de', 90);
            $locale = new LocaleTestHelper();
            $this->assertTrue($locale instanceof Locale); // should defer to 'de' or any other standard locale
        } catch (InvalidArgumentException $e) {
            $this->fail();
        }

        try {
            LocaleTestHelper::setFallback('de-AT', 90);
            $locale = new LocaleTestHelper();
            $this->assertTrue($locale instanceof Locale);
        } catch (InvalidArgumentException $e) {
            $this->fail();
        }
    }

    /**
     * Test getFallback
     */
    public function testgetFallback()
    {
        LocaleTestHelper::setFallback('de');
        $this->assertTrue(array_key_exists('de', LocaleTestHelper::getFallback()));
    }

    /**
     * Caching method tests
     */
    public function testCaching()
    {
        $cache = LocaleTestHelper::getCache();
        $this->assertTrue($cache instanceof CacheAdapter);
        $this->assertTrue(LocaleTestHelper::hasCache());

        LocaleTestHelper::clearCache();
        $this->assertTrue(LocaleTestHelper::hasCache());

        LocaleTestHelper::removeCache();
        $this->assertFalse(LocaleTestHelper::hasCache());
    }

    /**
     * Caching method tests
     */
    public function testFindingTheProperLocale()
    {
        $this->assertTrue(is_string(LocaleTestHelper::findLocale()));
        $this->assertEquals('de', LocaleTestHelper::findLocale('de'));
        $this->assertEquals('de', LocaleTestHelper::findLocale('de_XX'));

        try {
            $locale = LocaleTestHelper::findLocale('xx_YY');
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertContains('is no known locale', $e->getMessage());
        }

        \Zend\Registry::set('Zend_Locale', 'de');
        $this->assertEquals('de', LocaleTestHelper::findLocale());
    }

    /**
     * test isLocale
     * expected boolean
     */
    public function testZF3617()
    {
        $value = new LocaleTestHelper('en-US');
        $this->assertEquals('en_US', $value->toString());
    }

    /**
     * @group ZF4963
     */
    public function testZF4963()
    {
        $value = new LocaleTestHelper();
        $locale = $value->toString();
        $this->assertTrue(!empty($locale));

        $this->assertFalse(LocaleTestHelper::isLocale(null));

        $value = new LocaleTestHelper(0);
        $value = $value->toString();
        $this->assertTrue(!empty($value));

        $this->assertFalse(LocaleTestHelper::isLocale(0));
    }

    /**
     * test MultiPartLocales
     * expected boolean
     */
    public function testLongLocale()
    {
        $locale = new LocaleTestHelper('de_Latn_DE');
        $this->assertEquals('de_DE', $locale->toString());
        $this->assertTrue(LocaleTestHelper::isLocale('de_Latn_CAR_DE_sup3_win'));

        $locale = new LocaleTestHelper('de_Latn_DE');
        $this->assertEquals('de_DE', $locale->toString());

        $this->assertEquals('fr_FR', Locale::findLocale('fr-Arab-FR'));
    }

    /**
     * test SunLocales
     * expected boolean
     */
    public function testSunLocale()
    {
        $this->assertTrue(LocaleTestHelper::isLocale('de_DE.utf8'));
        $this->assertFalse(LocaleTestHelper::isLocale('de.utf8.DE'));
    }

    /**
     * @group ZF-8030
     */
    public function testFailedLocaleOnPreTranslations()
    {
        $this->assertEquals('Andorra', LocaleTestHelper::getTranslation('AD', 'country', 'gl_GL'));
    }

    /**
     * @ZF-9488
     */
    public function testTerritoryToGetLocale()
    {
        $value = Locale::findLocale('US');
        $this->assertEquals('en_US', $value);

        $value = new Locale('US');
        $this->assertEquals('en_US', $value->toString());

        $value = new Locale('TR');
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

class LocaleTestHelper extends Locale
{
    public static function resetObject()
    {
        self::$_auto        = null;
        self::$_environment = null;
        self::$_browser     = null;
    }
}
