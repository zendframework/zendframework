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
 * @package    Zend_Currency
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Currency;

use Zend\Cache\StorageFactory as CacheFactory,
    Zend\Cache\Storage\Adapter as CacheAdapter,
    Zend\Currency,
    Zend\Locale;

/**
 * @category   Zend
 * @package    Zend_Currency
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Currency
 */
class CurrencyTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_cacheDir = sys_get_temp_dir() . '/zend_currency';
        $this->_removeRecursive($this->_cacheDir);
        mkdir($this->_cacheDir);

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

        Currency\Currency::setCache($this->_cache);
    }

    public function tearDown()
    {
        Currency\Currency::clearCache();
        $this->_cache->clear(CacheAdapter::MATCH_ALL);
        $this->_removeRecursive($this->_cacheDir);
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
     * tests the creation of Zend/Currency/Currency
     */
    public function testSingleCreation()
    {
        // look if locale is detectable
        try {
            $locale = new Locale\Locale();
        } catch (Locale\Exception $e) {
            $this->markTestSkipped('Autodetection of locale failed');
            return;
        }

        $locale = new Locale\Locale('de_AT');

        try {
            $currency = new Currency\Currency();
            $this->assertTrue($currency instanceof Currency\Currency);
        } catch (Currency\Exception\InvalidArgumentException $e) {
            $this->assertContains('No region found within the locale', $e->getMessage());
        }

        $currency = new Currency\Currency('de_AT');
        $this->assertTrue($currency instanceof Currency\Currency);
        $this->assertSame('€ 1.000,00', $currency->toCurrency(1000));

        $currency = new Currency\Currency('de_DE');
        $this->assertTrue($currency instanceof Currency\Currency);
        $this->assertSame('1.000,00 €', $currency->toCurrency(1000));

        $currency = new Currency\Currency($locale);
        $this->assertTrue($currency instanceof Currency\Currency);
        $this->assertSame('€ 1.000,00', $currency->toCurrency(1000));

        try {
            $currency = new Currency\Currency('de_XX');
            $this->fail("Locale without region should not been recognised");
        } catch (Currency\Exception\InvalidArgumentException $e) {
            // success
        }

        try {
            $currency = new Currency\Currency('xx_XX');
        } catch (Currency\Exception\InvalidArgumentException $e) {
            // success
        }

        try {
            $currency = new Currency\Currency(array('currency' => 'EUR'));
            $this->assertTrue($currency instanceof Currency\Currency);
        } catch (Currency\Exception\InvalidArgumentException $e) {
            $this->assertContains('No region found within the locale', $e->getMessage());
        }

        try {
            $currency = new Currency\Currency(array('currency' => 'USD'));
            $this->assertTrue($currency instanceof Currency\Currency);
        } catch (Currency\Exception\InvalidArgumentException $e) {
            $this->assertContains('No region found within the locale', $e->getMessage());
        }

        try {
            $currency = new Currency\Currency(array('currency' => 'AWG'));
            $this->assertTrue($currency instanceof Currency\Currency);
        } catch (Currency\Exception\InvalidArgumentException $e) {
            $this->assertContains('No region found within the locale', $e->getMessage());
        }
    }

    /**
     * tests the creation of Currency\Currency
     */
    public function testDualCreation()
    {
        $locale = new Locale\Locale('de_AT');

        $currency = new Currency\Currency('USD', 'de_AT');
        $this->assertTrue($currency instanceof Currency\Currency);
        $this->assertSame('$ 1.000,00', $currency->toCurrency(1000));

        $currency = new Currency\Currency('USD', $locale);
        $this->assertTrue($currency instanceof Currency\Currency);
        $this->assertSame('$ 1.000,00', $currency->toCurrency(1000));

        $currency = new Currency\Currency('de_AT', 'USD');
        $this->assertTrue($currency instanceof Currency\Currency);
        $this->assertSame('$ 1.000,00', $currency->toCurrency(1000));

        $currency = new Currency\Currency($locale, 'USD');
        $this->assertTrue($currency instanceof Currency\Currency);
        $this->assertSame('$ 1.000,00', $currency->toCurrency(1000));

        $currency = new Currency\Currency('EUR', 'de_AT');
        $this->assertTrue($currency instanceof Currency\Currency);
        $this->assertSame('€ 1.000,00', $currency->toCurrency(1000));

        try {
            $currency = new Currency\Currency('EUR', 'xx_YY');
            $this->fail("unknown locale should not have been recognised");
        } catch (Currency\Exception\InvalidArgumentException $e) {
            // success
        }
    }

    /**
     * tests the creation of Currency\Currency
     */
    public function testTripleCreation()
    {
        $locale = new Locale\Locale('de_AT');

        $currency = new Currency\Currency('USD', 'de_AT');
        $this->assertTrue($currency instanceof Currency\Currency);
        $this->assertSame('$ 1.000,00', $currency->toCurrency(1000));

        $currency = new Currency\Currency('USD', $locale);
        $this->assertTrue($currency instanceof Currency\Currency);
        $this->assertSame('$ 1.000,00', $currency->toCurrency(1000));

        try {
            $currency = new Currency\Currency('XXX', 'Latin', $locale);
            $this->fail("unknown shortname should not have been recognised");
        } catch (Currency\Exception\InvalidArgumentException $e) {
            // success
        }
        try {
            $currency = new Currency\Currency('USD', 'Xyzz', $locale);
            $this->fail("unknown script should not have been recognised");
        } catch (Currency\Exception\InvalidArgumentException $e) {
            // success
        }
        try {
            $currency = new Currency\Currency('USD', 'Latin', 'xx_YY');
            $this->fail("unknown locale should not have been recognised");
        } catch (Currency\Exception\InvalidArgumentException $e) {
            // success
        }

        $currency = new Currency\Currency('USD', 'de_AT');
        $this->assertTrue($currency instanceof Currency\Currency);
        $this->assertSame('$ 1.000,00', $currency->toCurrency(1000));

        $currency = new Currency\Currency('Euro', 'de_AT');
        $this->assertTrue($currency instanceof Currency\Currency);
        $this->assertSame('EUR 1.000,00', $currency->toCurrency(1000));

        $currency = new Currency\Currency('USD', $locale);
        $this->assertTrue($currency instanceof Currency\Currency);
        $this->assertSame('$ 1.000,00', $currency->toCurrency(1000));

        $currency = new Currency\Currency('de_AT', 'EUR');
        $this->assertTrue($currency instanceof Currency\Currency);
        $this->assertSame('€ 1.000,00', $currency->toCurrency(1000));

        $currency = new Currency\Currency($locale, 'USD');
        $this->assertTrue($currency instanceof Currency\Currency);
        $this->assertSame('$ 1.000,00', $currency->toCurrency(1000));

        $currency = new Currency\Currency('EUR', 'en_US');
        $this->assertTrue($currency instanceof Currency\Currency);
        $this->assertSame('€1,000.00', $currency->toCurrency(1000));

        $currency = new Currency\Currency('en_US', 'USD');
        $this->assertTrue($currency instanceof Currency\Currency);
        $this->assertSame('$1,000.00', $currency->toCurrency(1000));

        $currency = new Currency\Currency($locale, 'EUR');
        $this->assertTrue($currency instanceof Currency\Currency);
        $this->assertSame('€ 1.000,00', $currency->toCurrency(1000));
    }

    /**
     * tests failed creation of Currency\Currency
     */
    public function testFailedCreation()
    {
        $locale = new Locale\Locale('de_AT');

        try {
            $currency = new Currency\Currency('de_AT', 'en_US');
            $this->fail("exception expected");
        } catch (Currency\Exception\InvalidArgumentException $e) {
            // success
        }
        try {
            $currency = new Currency\Currency('USD', 'EUR');
            $this->fail("exception expected");
        } catch (Currency\Exception\InvalidArgumentException $e) {
            // success
        }
        try {
            $currency = new Currency\Currency('Arab', 'Latn');
            $this->fail("exception expected");
        } catch (Currency\Exception\InvalidArgumentException $e) {
            // success
        }
        try {
            $currency = new Currency\Currency('EUR');
            $currency->toCurrency('value');
            $this->fail("exception expected");
        } catch (Currency\Exception\InvalidArgumentException $e) {
            // success
        }

        $currency = new Currency\Currency('EUR', 'de_AT');
        $currency->setFormat(array('display' => 'SIGN'));
        $this->assertSame('SIGN 1.000,00', $currency->toCurrency(1000));

        try {
            $currency = new Currency\Currency('EUR');
            $currency->setFormat(array('format' => 'xy_ZY'));
            $this->fail("exception expected");
        } catch (Currency\Exception\InvalidArgumentException $e) {
            // success
        }
    }

    /*
     * testing toCurrency
     */
    public function testToCurrency()
    {
        $USD = new Currency\Currency('USD','en_US');
        $EGP = new Currency\Currency('EGP','ar_EG');

        $this->assertSame('$53,292.18', $USD->toCurrency(53292.18));
        $this->assertSame('$٥٣,٢٩٢.١٨', $USD->toCurrency(53292.18, array('script' => 'Arab' )));
        $this->assertSame('$ ٥٣.٢٩٢,١٨', $USD->toCurrency(53292.18, array('script' => 'Arab', 'format' => 'de_AT')));
        $this->assertSame('$ 53.292,18', $USD->toCurrency(53292.18, array('format' => 'de_AT')));

        $this->assertSame('ج.م.‏ 53.292,18', $EGP->toCurrency(53292.18));
        $this->assertSame('ج.م.‏ ٥٣.٢٩٢,١٨', $EGP->toCurrency(53292.18, array('script' => 'Arab' )));
        $this->assertSame('ج.م.‏ ٥٣.٢٩٢,١٨', $EGP->toCurrency(53292.18, array('script' =>'Arab', 'format' => 'de_AT')));
        $this->assertSame('ج.م.‏ 53.292,18', $EGP->toCurrency(53292.18, array('format' => 'de_AT')));

        $USD = new Currency\Currency('en_US');
        $this->assertSame('$53,292.18', $USD->toCurrency(53292.18));
        try {
            $this->assertSame('$ 53,292.18', $USD->toCurrency('nocontent'));
            $this->fail("No currency expected");
        } catch (Currency\Exception\InvalidArgumentException $e) {
            $this->assertContains("has to be numeric", $e->getMessage());
        }

        $INR = new Currency\Currency('INR', 'de_AT');
        $this->assertSame('Rs 1,20', $INR->toCurrency(1.2));
        $this->assertSame('Rs 1,00', $INR->toCurrency(1));
        $this->assertSame('Rs 0,00', $INR->toCurrency(0));
        $this->assertSame('-Rs 3,00', $INR->toCurrency(-3));
    }

    /**
     * testing setFormat
     *
     */
    public function testSetFormat()
    {
        $locale = new Locale\Locale('en_US');
        $USD    = new Currency\Currency('USD','en_US');

        $USD->setFormat(array('script' => 'Arab'));
        $this->assertSame('$٥٣,٢٩٢.١٨', $USD->toCurrency(53292.18));

        $USD->setFormat(array('script' => 'Arab', 'format' => 'de_AT'));
        $this->assertSame('$ ٥٣.٢٩٢,١٨', $USD->toCurrency(53292.18));

        $USD->setFormat(array('script' => 'Latn', 'format' => 'de_AT'));
        $this->assertSame('$ 53.292,18', $USD->toCurrency(53292.18));

        $USD->setFormat(array('script' => 'Latn', 'format' => $locale));
        $this->assertSame('$53,292.18', $USD->toCurrency(53292.18));

        // allignment of currency signs
        $USD->setFormat(array('position' => Currency\Currency::RIGHT, 'format' => 'de_AT'));
        $this->assertSame('53.292,18 $', $USD->toCurrency(53292.18));

        $USD->setFormat(array('position' => Currency\Currency::RIGHT, 'format' => $locale));
        $this->assertSame('53,292.18$', $USD->toCurrency(53292.18));

        $USD->setFormat(array('position' => Currency\Currency::LEFT, 'format' => 'de_AT'));
        $this->assertSame('$ 53.292,18', $USD->toCurrency(53292.18));

        $USD->setFormat(array('position' => Currency\Currency::LEFT, 'format' => $locale));
        $this->assertSame('$53,292.18', $USD->toCurrency(53292.18));

        $USD->setFormat(array('position' => Currency\Currency::STANDARD, 'format' => 'de_AT'));
        $this->assertSame('$ 53.292,18', $USD->toCurrency(53292.18));

        $USD->setFormat(array('position' => Currency\Currency::STANDARD, 'format' => $locale));
        $this->assertSame('$53,292.18', $USD->toCurrency(53292.18));

        // enable/disable currency symbols & currency names
        $USD->setFormat(array('display' => Currency\Currency::NO_SYMBOL, 'format' => 'de_AT'));
        $this->assertSame('53.292,18', $USD->toCurrency(53292.18));

        $USD->setFormat(array('display' => Currency\Currency::NO_SYMBOL, 'format' => $locale));
        $this->assertSame('53,292.18', $USD->toCurrency(53292.18));

        $USD->setFormat(array('display' => Currency\Currency::USE_SHORTNAME, 'format' => 'de_AT'));
        $this->assertSame('USD 53.292,18', $USD->toCurrency(53292.18));

        $USD->setFormat(array('display' => Currency\Currency::USE_SHORTNAME, 'format' => $locale));
        $this->assertSame('USD53,292.18', $USD->toCurrency(53292.18));

        $USD->setFormat(array('display' => Currency\Currency::USE_NAME, 'format' => 'de_AT'));
        $this->assertSame('US Dollar 53.292,18', $USD->toCurrency(53292.18));

        $USD->setFormat(array('display' => Currency\Currency::USE_NAME, 'format' => $locale));
        $this->assertSame('US Dollar53,292.18', $USD->toCurrency(53292.18));

        $USD->setFormat(array('display' => Currency\Currency::USE_SYMBOL, 'format' => 'de_AT'));
        $this->assertSame('$ 53.292,18', $USD->toCurrency(53292.18));

        $USD->setFormat(array('display' => Currency\Currency::USE_SYMBOL, 'format' => $locale));
        $this->assertSame('$53,292.18', $USD->toCurrency(53292.18));

        try {
            $USD->setFormat(array('position' => 'unknown'));
            $this->fail("Exception expected");
        } catch (Currency\Exception\InvalidArgumentException $e) {
            $this->assertContains("Unknown position", $e->getMessage());
        }

        try {
            $USD->setFormat(array('format' => 'unknown'));
            $this->fail("Exception expected");
        } catch (Currency\Exception\InvalidArgumentException $e) {
            $this->assertContains("is no format token", $e->getMessage());
        }

        try {
            $USD->setFormat(array('display' => -14));
            $this->fail("Exception expected");
        } catch (Currency\Exception\InvalidArgumentException $e) {
            $this->assertContains("Unknown display", $e->getMessage());
        }

        try {
            $USD->setFormat(array('script' => 'unknown'));
            $this->fail("Exception expected");
        } catch (Currency\Exception\InvalidArgumentException $e) {
            $this->assertContains("Unknown script", $e->getMessage());
        }

        $USD->setFormat(array('precision' => null));

        try {
            $USD->setFormat(array('precision' => -14));
            $this->fail("Exception expected");
        } catch (Currency\Exception\InvalidArgumentException $e) {
            $this->assertContains("precision has to be between", $e->getMessage());
        }
    }

    /**
     * test getSign
     */
    public function testGetSign()
    {
        $locale   = new Locale\Locale('ar_EG');
        $currency = new Currency\Currency('ar_EG');

        $this->assertSame('ج.م.‏', $currency->getSymbol('EGP','ar_EG'));
        $this->assertSame('€',    $currency->getSymbol('EUR','de_AT'));
        $this->assertSame('ج.م.‏', $currency->getSymbol('ar_EG'      ));
        $this->assertSame('€',    $currency->getSymbol('de_AT'      ));
        $this->assertSame('ج.م.‏',    $currency->getSymbol());

        try {
            $currency->getSymbol('EGP', 'de_XX');
            $this->fail("exception expected");
        } catch (Currency\Exception\InvalidArgumentException $e) {
            // success
        }
    }

    /**
     * test getName
     */
    public function testGetName()
    {
        $locale   = new Locale\Locale('ar_EG');
        $currency = new Currency\Currency('ar_EG');

        $this->assertSame('جنيه مصري',       $currency->getName('EGP','ar_EG'));
        $this->assertSame('Estnische Krone', $currency->getName('EEK','de_AT'));
        $this->assertSame('جنيه مصري',       $currency->getName('EGP',$locale));
        $this->assertSame('جنيه مصري',       $currency->getName('ar_EG'      ));
        $this->assertSame('Euro',            $currency->getName('de_AT'      ));
        $this->assertSame('جنيه مصري',       $currency->getName());

        try {
            $currency->getName('EGP', 'xy_XY');
            $this->fail("exception expected");
        } catch (Currency\Exception\InvalidArgumentException $e) {
            // success
        }
    }

    /**
     * test getShortName
     */
    public function testGetShortName()
    {
        $locale   = new Locale\Locale('de_AT');
        $currency = new Currency\Currency('de_AT');

        $this->assertSame('EUR', $currency->getShortName('Euro',     'de_AT'));
        $this->assertSame('EUR', $currency->getShortName('Euro',     $locale));
        $this->assertSame('USD', $currency->getShortName('US-Dollar','de_AT'));
        $this->assertSame('EUR', $currency->getShortName('de_AT'            ));
        $this->assertSame('EUR', $currency->getShortName());

        try {
            $currency->getShortName('EUR', 'xy_ZT');
            $this->fail("exception expected");
        } catch (Currency\Exception\InvalidArgumentException $e) {
            // success
        }
    }

    /**
     * testing getRegionList
     */
    public function testGetRegionList()
    {
        // look if locale is detectable
        try {
            $locale = new Locale\Locale();
        } catch (Locale\Exception $e) {
            $this->markTestSkipped('Autodetection of locale failed');
            return;
        }

        try {
            $currency = new Currency\Currency(array('currency' => 'USD'));
            $this->assertTrue(in_array('US', $currency->getRegionList()));
        } catch (Currency\Exception\InvalidArgumentException $e) {
            $this->assertContains('No region found within the locale', $e->getMessage());
        }

        $currency = new Currency\Currency(array('currency' => 'USD'), 'en_US');
        $currency->setFormat(array('currency' => null));
        try {
            $this->assertTrue(in_array('US', $currency->getRegionList()));
            $this->fail("Exception expected");
        } catch (Currency\Exception\InvalidArgumentException $e) {
            $this->assertContains("No currency defined", $e->getMessage());
        }

        $currency = new Currency\Currency(array('currency' => 'USD'), 'en_US');
        $this->assertEquals(array(0 => 'AS', 1 => 'EC', 2 => 'FM', 3 => 'GU', 4 => 'IO', 5 => 'MH', 6 => 'MP',
            7 => 'PR', 8 => 'PW', 9 => "SV", 10 => 'TC', 11 => 'TL', 12 => 'UM', 13 => 'US', 14 => 'VG', 15 => 'VI', 16 => 'ZW'), $currency->getRegionList());
    }

    /**
     * testing getCurrencyList
     */
    public function testGetCurrencyList()
    {
        // look if locale is detectable
        try {
            $locale = new Locale\Locale();
        } catch (Locale\Exception $e) {
            $this->markTestSkipped('Autodetection of locale failed');
            return;
        }

        $currency = new Currency\Currency('ar_EG');
        $this->assertTrue(in_array('EGP', $currency->getCurrencyList()));
    }

    /**
     * testing toString
     *
     */
    public function testToString()
    {
        $USD = new Currency\Currency('USD','en_US');
        $this->assertSame('$0.00', $USD->toString());
        $this->assertSame('$0.00', $USD->__toString());
    }

    /**
     * testing registry Locale
     * ZF-3676
     */
    public function testRegistryLocale()
    {
        $locale = new Locale\Locale('de_AT');
        \Zend\Registry::set('Zend_Locale', $locale);

        $currency = new Currency\Currency('EUR');
        $this->assertSame('de_AT', $currency->getLocale());
    }

    /**
     * Caching method tests
     */
    public function testCaching()
    {
        $cache = Currency\Currency::getCache();
        $this->assertTrue($cache instanceof CacheAdapter);
        $this->assertTrue(Currency\Currency::hasCache());

        Currency\Currency::clearCache();
        $this->assertTrue(Currency\Currency::hasCache());

        Currency\Currency::removeCache();
        $this->assertFalse(Currency\Currency::hasCache());
    }

    /**
     * @group ZF-6560
     */
    public function testPrecisionForCurrency()
    {
        $currency = new Currency\Currency(null, 'de_DE');

        $this->assertEquals('75 €', $currency->toCurrency(74.95, array('precision' => 0)));
        $this->assertEquals('75,0 €', $currency->toCurrency(74.95, array('precision' => 1)));
        $this->assertEquals('74,95 €', $currency->toCurrency(74.95, array('precision' => 2)));
        $this->assertEquals('74,950 €', $currency->toCurrency(74.95, array('precision' => 3)));
        $this->assertEquals('74,9500 €', $currency->toCurrency(74.95, array('precision' => 4)));
        $this->assertEquals('74,95000 €', $currency->toCurrency(74.95, array('precision' => 5)));
    }

    /**
     * @group ZF-6561
     */
    public function testNegativeRendering()
    {
        $currency = new Currency\Currency(null, 'de_DE');
        $this->assertEquals('-74,9500 €', $currency->toCurrency(-74.95, array('currency' => 'EUR', 'precision' => 4)));

        $currency = new Currency\Currency(null, 'en_US');
        $this->assertEquals('-$74.9500', $currency->toCurrency(-74.95, array('currency' => 'USD', 'precision' => 4)));
    }

    /**
     * @group ZF-7359
     */
    public function testPHPsScientificBug()
    {
        $currency = new Currency\Currency("USD", "en_US");
        $this->assertEquals('$0.00', $currency->toCurrency(1.0E-4));
        $this->assertEquals('$0.00', $currency->toCurrency(1.0E-5));
    }

    /**
     * @group ZF-7864
     */
    public function testCurrencyToToCurrency()
    {
        $currency = new Currency\Currency("de_DE");
        $this->assertEquals('2,3000 $', $currency->toCurrency(2.3, array('currency' => 'USD', 'precision' => 4)));

        $currency = new Currency\Currency("USD", "de_DE");
        $this->assertEquals('2,3000 $', $currency->toCurrency(2.3, array('precision' => 4)));
    }

    /**
     * Testing options at initiation
     */
    public function testOptionsWithConstructor()
    {
        $currency = new Currency\Currency(array('currency' => 'EUR', 'locale' => 'de_AT'));
        $this->assertEquals('de_AT', $currency->getLocale());
        $this->assertEquals('EUR', $currency->getShortName());
    }

    /**
     * Testing value at initiation
     */
    public function testValueWithConstructor()
    {
        $currency = new Currency\Currency(array('currency' => 'EUR', 'locale' => 'de_AT', 'value' => 100));
        $this->assertEquals('de_AT', $currency->getLocale());
        $this->assertEquals('EUR', $currency->getShortName());
        $this->assertEquals('€ 100,00', $currency->toCurrency());
    }

    /**
     * Add values
     */
    public function testAddValues()
    {
        $currency = new Currency\Currency(array('currency' => 'EUR', 'locale' => 'de_AT'));
        $currency->add(100);
        $this->assertEquals('€ 100,00', $currency->toCurrency());

        $currency->add(100)->add(100);
        $this->assertEquals('€ 300,00', $currency->toCurrency());
    }

    /**
     * Substract values
     */
    public function testSubValues()
    {
        $currency = new Currency\Currency(array('currency' => 'EUR', 'locale' => 'de_AT'));
        $currency->sub(100);
        $this->assertEquals('-€ 100,00', $currency->toCurrency());

        $currency->sub(100)->sub(100);
        $this->assertEquals('-€ 300,00', $currency->toCurrency());
    }

    /**
     * Multiply values
     */
    public function testMulValues()
    {
        $currency = new Currency\Currency(array('currency' => 'EUR', 'locale' => 'de_AT'));
        $currency->add(100);
        $currency->mul(2);
        $this->assertEquals('€ 200,00', $currency->toCurrency());

        $currency->mul(2)->mul(2);
        $this->assertEquals('€ 800,00', $currency->toCurrency());
    }

    /**
     * Divide values
     */
    public function testDivValues()
    {
        $currency = new Currency\Currency(array('currency' => 'EUR', 'locale' => 'de_AT'));
        $currency->add(800);
        $currency->div(2);
        $this->assertEquals('€ 400,00', $currency->toCurrency());

        $currency->div(2)->div(2);
        $this->assertEquals('€ 100,00', $currency->toCurrency());
    }

    /**
     * Modulo values
     */
    public function testModValues()
    {
        $currency = new Currency\Currency(array('currency' => 'EUR', 'locale' => 'de_AT'));
        $currency->add(801);
        $currency->mod(2);
        $this->assertEquals('€ 1,00', $currency->toCurrency());
    }

    /**
     * Compare values
     */
    public function testCompareValues()
    {
        $currency  = new Currency\Currency(array('currency' => 'EUR', 'locale' => 'de_AT', 'value' => 100));
        $currency2 = new Currency\Currency(array('currency' => 'EUR', 'locale' => 'de_AT', 'value' => 100));
        $this->assertEquals(0, $currency->compare($currency2));

        $currency3 = new Currency\Currency(array('currency' => 'EUR', 'locale' => 'de_AT', 'value' => 101));
        $this->assertEquals(-1, $currency->compare($currency3));

        $currency4 = new Currency\Currency(array('currency' => 'EUR', 'locale' => 'de_AT', 'value' => 99));
        $this->assertEquals(1, $currency->compare($currency4));
    }

    /**
     * Equals values
     */
    public function testEqualsValues()
    {
        $currency  = new Currency\Currency(array('currency' => 'EUR', 'locale' => 'de_AT', 'value' => 100));
        $currency2 = new Currency\Currency(array('currency' => 'EUR', 'locale' => 'de_AT', 'value' => 100));
        $this->assertTrue($currency->equals($currency2));

        $currency3 = new Currency\Currency(array('currency' => 'EUR', 'locale' => 'de_AT', 'value' => 101));
        $this->assertFalse($currency->equals($currency3));
    }

    /**
     * IsMore values
     */
    public function testIsMoreValues()
    {
        $currency  = new Currency\Currency(array('currency' => 'EUR', 'locale' => 'de_AT', 'value' => 100));
        $currency2 = new Currency\Currency(array('currency' => 'EUR', 'locale' => 'de_AT', 'value' => 100));
        $this->assertFalse($currency->isMore($currency2));

        $currency3 = new Currency\Currency(array('currency' => 'EUR', 'locale' => 'de_AT', 'value' => 99));
        $this->assertTrue($currency->isMore($currency3));
    }

    /**
     * IsLess values
     */
    public function testIsLessValues()
    {
        $currency  = new Currency\Currency(array('currency' => 'EUR', 'locale' => 'de_AT', 'value' => 100));
        $currency2 = new Currency\Currency(array('currency' => 'EUR', 'locale' => 'de_AT', 'value' => 100));
        $this->assertFalse($currency->isLess($currency2));

        $currency3 = new Currency\Currency(array('currency' => 'EUR', 'locale' => 'de_AT', 'value' => 101));
        $this->assertTrue($currency->isLess($currency3));
    }

    /**
     * Exchange tests
     */
    public function testExchangeValues()
    {
        $currency  = new Currency\Currency(array('currency' => 'EUR', 'locale' => 'de_AT', 'value' => 100));
        $currency2 = new Currency\Currency(array('currency' => 'EUR', 'locale' => 'de_AT', 'value' => 100));

        require_once __DIR__ . '/ExchangeTest.php';

        $this->assertEquals(null, $currency->getService());
        $currency->setService(new ExchangeTest());
        $this->assertTrue($currency->getService() instanceof Currency\CurrencyService);

        $currency->setService('ZendTest\Currency\ExchangeTest');
        $this->assertTrue($currency->getService() instanceof Currency\CurrencyService);
    }

    /**
     * IsLess values
     */
    public function testConstructingPrecisionValues()
    {
        $currency  = new Currency\Currency(array('value' => 100.5));
        $this->assertEquals('€ 100,50', $currency->toString('de_AT'));
    }

    /**
     * @ZF-9491
     */
    public function testCurrencyWithSelfPattern()
    {
        $currency  = new Currency\Currency(array('value' => 10000, 'format' => '#,#0', 'locale' => 'de_DE'));
        $this->assertEquals('1.00.00', $currency->toString());
    }

    /**
     * @ZF-9519
     */
    public function testSetValueWithoutLocale()
    {
        $currency = new Currency\Currency('RUB', 'ru_RU');
        require_once __DIR__ . '/ExchangeTest.php';

        $this->assertEquals(null, $currency->getService());
        $currency->setService(new ExchangeTest());
        $this->assertTrue($currency->getService() instanceof Currency\CurrencyService);

        $currency->setValue(100, 'USD');
        $this->assertEquals(50, $currency->getValue());
        $this->assertEquals('RUB', $currency->getShortName());
    }

    /**
     * @ZF-9941
     */
    public function testSetValueByOutput()
    {
        $currency = new Currency\Currency(array('value' => 1000, 'locale' => 'de_AT'));
        $this->assertEquals('€ 2.000,00', $currency->toCurrency(null, array('value' => 2000)));
    }
}
