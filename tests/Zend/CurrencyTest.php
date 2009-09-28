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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'TestHelper.php';

/**
 * Zend_Currency
 */
require_once 'Zend/Locale.php';
require_once 'Zend/Currency.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework.php';

/**
 * @category   Zend
 * @package    Zend_Currency
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Currency
 */
class Zend_CurrencyTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        require_once 'Zend/Cache.php';
        $cache = Zend_Cache::factory('Core', 'File',
                 array('lifetime' => 120, 'automatic_serialization' => true),
                 array('cache_dir' => dirname(__FILE__) . '/_files/'));
        Zend_Currency::setCache($cache);
    }

    /**
     * tests the creation of Zend_Currency
     */
    public function testSingleCreation()
    {
        // look if locale is detectable
        try {
            $locale = new Zend_Locale();
        } catch (Zend_Locale_Exception $e) {
            $this->markTestSkipped('Autodetection of locale failed');
            return;
        }

        $locale = new Zend_Locale('de_AT');

        try {
            $currency = new Zend_Currency();
            $this->assertTrue($currency instanceof Zend_Currency);
        } catch (Zend_Currency_Exception $e) {
            $this->assertContains('No region found within the locale', $e->getMessage());
        }

        $currency = new Zend_Currency('de_AT');
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame('€ 1.000,00', $currency->toCurrency(1000));

        $currency = new Zend_Currency('de_DE');
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame('1.000,00 €', $currency->toCurrency(1000));

        $currency = new Zend_Currency($locale);
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame('€ 1.000,00', $currency->toCurrency(1000));

        try {
            $currency = new Zend_Currency('de_XX');
            $this->fail("locale should always include region and therefor not been recognised");
        } catch (Zend_Currency_Exception $e) {
            // success
        }
        try {
            $currency = new Zend_Currency('xx_XX');
            $this->fail("unknown locale should not have been recognised");
        } catch (Zend_Currency_Exception $e) {
            // success
        }

        try {
            $currency = new Zend_Currency('EUR');
            $this->assertTrue($currency instanceof Zend_Currency);
        } catch (Zend_Currency_Exception $e) {
            $this->assertContains('No region found within the locale', $e->getMessage());
        }

        try {
            $currency = new Zend_Currency('USD');
            $this->assertTrue($currency instanceof Zend_Currency);
        } catch (Zend_Currency_Exception $e) {
            $this->assertContains('No region found within the locale', $e->getMessage());
        }

        try {
            $currency = new Zend_Currency('AWG');
            $this->assertTrue($currency instanceof Zend_Currency);
        } catch (Zend_Currency_Exception $e) {
            $this->assertContains('No region found within the locale', $e->getMessage());
        }

        try {
            $currency = new Zend_Currency('XYZ');
            $this->fail("unknown shortname should not have been recognised");
        } catch (Zend_Currency_Exception $e) {
            // success
        }
    }

    /**
     * tests the creation of Zend_Currency
     */
    public function testDualCreation()
    {
        $locale = new Zend_Locale('de_AT');

        $currency = new Zend_Currency('USD', 'de_AT');
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame('$ 1.000,00', $currency->toCurrency(1000));

        $currency = new Zend_Currency('USD', $locale);
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame('$ 1.000,00', $currency->toCurrency(1000));

        $currency = new Zend_Currency('de_AT', 'USD');
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame('$ 1.000,00', $currency->toCurrency(1000));

        $currency = new Zend_Currency($locale, 'USD');
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame('$ 1.000,00', $currency->toCurrency(1000));

        $currency = new Zend_Currency('EUR', 'de_AT');
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame('€ 1.000,00', $currency->toCurrency(1000));

        try {
            $currency = new Zend_Currency('EUR', 'xx_YY');
            $this->fail("unknown locale should not have been recognised");
        } catch (Zend_Currency_Exception $e) {
            // success
        }
    }

    /**
     * tests the creation of Zend_Currency
     */
    public function testTripleCreation()
    {
        $locale = new Zend_Locale('de_AT');

        $currency = new Zend_Currency('USD', 'de_AT');
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame('$ 1.000,00', $currency->toCurrency(1000));

        $currency = new Zend_Currency('USD', $locale);
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame('$ 1.000,00', $currency->toCurrency(1000));

        try {
            $currency = new Zend_Currency('XXX', 'Latin', $locale);
            $this->fail("unknown shortname should not have been recognised");
        } catch (Zend_Currency_Exception $e) {
            // success
        }
        try {
            $currency = new Zend_Currency('USD', 'Xyzz', $locale);
            $this->fail("unknown script should not have been recognised");
        } catch (Zend_Currency_Exception $e) {
            // success
        }
        try {
            $currency = new Zend_Currency('USD', 'Latin', 'xx_YY');
            $this->fail("unknown locale should not have been recognised");
        } catch (Zend_Currency_Exception $e) {
            // success
        }

        $currency = new Zend_Currency('USD', 'de_AT');
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame('$ 1.000,00', $currency->toCurrency(1000));

        $currency = new Zend_Currency('Euro', 'de_AT');
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame('EUR 1.000,00', $currency->toCurrency(1000));

        $currency = new Zend_Currency('USD', $locale);
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame('$ 1.000,00', $currency->toCurrency(1000));

        $currency = new Zend_Currency('de_AT', 'EUR');
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame('€ 1.000,00', $currency->toCurrency(1000));

        $currency = new Zend_Currency($locale, 'USD');
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame('$ 1.000,00', $currency->toCurrency(1000));

        $currency = new Zend_Currency('EUR', 'en_US');
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame('€1,000.00', $currency->toCurrency(1000));

        $currency = new Zend_Currency('en_US', 'USD');
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame('$1,000.00', $currency->toCurrency(1000));

        $currency = new Zend_Currency($locale, 'EUR');
        $this->assertTrue($currency instanceof Zend_Currency);
        $this->assertSame('€ 1.000,00', $currency->toCurrency(1000));
    }

    /**
     * tests failed creation of Zend_Currency
     */
    public function testFailedCreation()
    {
        $locale = new Zend_Locale('de_AT');

        try {
            $currency = new Zend_Currency('de_AT', 'en_US');
            $this->fail("exception expected");
        } catch (Zend_Currency_Exception $e) {
            // success
        }
        try {
            $currency = new Zend_Currency('USD', 'EUR');
            $this->fail("exception expected");
        } catch (Zend_Currency_Exception $e) {
            // success
        }
        try {
            $currency = new Zend_Currency('Arab', 'Latn');
            $this->fail("exception expected");
        } catch (Zend_Currency_Exception $e) {
            // success
        }
        try {
            $currency = new Zend_Currency('EUR');
            $currency->toCurrency('value');
            $this->fail("exception expected");
        } catch (Zend_Currency_Exception $e) {
            // success
        }

        $currency = new Zend_Currency('EUR', 'de_AT');
        $currency->setFormat(array('display' => 'SIGN'));
        $this->assertSame('SIGN 1.000,00', $currency->toCurrency(1000));

        try {
            $currency = new Zend_Currency('EUR');
            $currency->setFormat(array('format' => 'xy_ZY'));
            $this->fail("exception expected");
        } catch (Zend_Currency_Exception $e) {
            // success
        }
    }

    /*
     * testing toCurrency
     */
    public function testToCurrency()
    {
        $USD = new Zend_Currency('USD','en_US');
        $EGP = new Zend_Currency('EGP','ar_EG');

        $this->assertSame('$53,292.18', $USD->toCurrency(53292.18));
        $this->assertSame('$٥٣,٢٩٢.١٨', $USD->toCurrency(53292.18, array('script' => 'Arab' )));
        $this->assertSame('$ ٥٣.٢٩٢,١٨', $USD->toCurrency(53292.18, array('script' => 'Arab', 'format' => 'de_AT')));
        $this->assertSame('$ 53.292,18', $USD->toCurrency(53292.18, array('format' => 'de_AT')));

        $this->assertSame('ج.م.‏ 53.292,18', $EGP->toCurrency(53292.18));
        $this->assertSame('ج.م.‏ ٥٣٬٢٩٢٫١٨', $EGP->toCurrency(53292.18, array('script' => 'Arab' )));
        $this->assertSame('ج.م.‏ ٥٣.٢٩٢,١٨', $EGP->toCurrency(53292.18, array('script' =>'Arab', 'format' => 'de_AT')));
        $this->assertSame('ج.م.‏ 53.292,18', $EGP->toCurrency(53292.18, array('format' => 'de_AT')));

        $USD = new Zend_Currency('en_US');
        $this->assertSame('$53,292.18', $USD->toCurrency(53292.18));
        try {
            $this->assertSame('$ 53,292.18', $USD->toCurrency('nocontent'));
            $this->fail("No currency expected");
        } catch (Zend_Currency_Exception $e) {
            $this->assertContains("has to be numeric", $e->getMessage());
        }

        $INR = new Zend_Currency('INR', 'de_AT');
        $this->assertSame('₨ 1,20', $INR->toCurrency(1.2));
        $this->assertSame('₨ 1,00', $INR->toCurrency(1));
        $this->assertSame('₨ 0,00', $INR->toCurrency(0));
        $this->assertSame('-₨ 3,00', $INR->toCurrency(-3));
    }

    /**
     * testing setFormat
     *
     */
    public function testSetFormat()
    {
        $locale = new Zend_Locale('en_US');
        $USD    = new Zend_Currency('USD','en_US');

        $USD->setFormat(array('script' => 'Arab'));
        $this->assertSame('$٥٣,٢٩٢.١٨', $USD->toCurrency(53292.18));

        $USD->setFormat(array('script' => 'Arab', 'format' => 'de_AT'));
        $this->assertSame('$ ٥٣.٢٩٢,١٨', $USD->toCurrency(53292.18));

        $USD->setFormat(array('script' => 'Latn', 'format' => 'de_AT'));
        $this->assertSame('$ 53.292,18', $USD->toCurrency(53292.18));

        $USD->setFormat(array('script' => 'Latn', 'format' => $locale));
        $this->assertSame('$53,292.18', $USD->toCurrency(53292.18));

        // allignment of currency signs
        $USD->setFormat(array('position' => Zend_Currency::RIGHT, 'format' => 'de_AT'));
        $this->assertSame('53.292,18 $', $USD->toCurrency(53292.18));

        $USD->setFormat(array('position' => Zend_Currency::RIGHT, 'format' => $locale));
        $this->assertSame('53,292.18$', $USD->toCurrency(53292.18));

        $USD->setFormat(array('position' => Zend_Currency::LEFT, 'format' => 'de_AT'));
        $this->assertSame('$ 53.292,18', $USD->toCurrency(53292.18));

        $USD->setFormat(array('position' => Zend_Currency::LEFT, 'format' => $locale));
        $this->assertSame('$53,292.18', $USD->toCurrency(53292.18));

        $USD->setFormat(array('position' => Zend_Currency::STANDARD, 'format' => 'de_AT'));
        $this->assertSame('$ 53.292,18', $USD->toCurrency(53292.18));

        $USD->setFormat(array('position' => Zend_Currency::STANDARD, 'format' => $locale));
        $this->assertSame('$53,292.18', $USD->toCurrency(53292.18));

        // enable/disable currency symbols & currency names
        $USD->setFormat(array('display' => Zend_Currency::NO_SYMBOL, 'format' => 'de_AT'));
        $this->assertSame('53.292,18', $USD->toCurrency(53292.18));

        $USD->setFormat(array('display' => Zend_Currency::NO_SYMBOL, 'format' => $locale));
        $this->assertSame('53,292.18', $USD->toCurrency(53292.18));

        $USD->setFormat(array('display' => Zend_Currency::USE_SHORTNAME, 'format' => 'de_AT'));
        $this->assertSame('USD 53.292,18', $USD->toCurrency(53292.18));

        $USD->setFormat(array('display' => Zend_Currency::USE_SHORTNAME, 'format' => $locale));
        $this->assertSame('USD53,292.18', $USD->toCurrency(53292.18));

        $USD->setFormat(array('display' => Zend_Currency::USE_NAME, 'format' => 'de_AT'));
        $this->assertSame('US Dollar 53.292,18', $USD->toCurrency(53292.18));

        $USD->setFormat(array('display' => Zend_Currency::USE_NAME, 'format' => $locale));
        $this->assertSame('US Dollar53,292.18', $USD->toCurrency(53292.18));

        $USD->setFormat(array('display' => Zend_Currency::USE_SYMBOL, 'format' => 'de_AT'));
        $this->assertSame('$ 53.292,18', $USD->toCurrency(53292.18));

        $USD->setFormat(array('display' => Zend_Currency::USE_SYMBOL, 'format' => $locale));
        $this->assertSame('$53,292.18', $USD->toCurrency(53292.18));

        try {
            $USD->setFormat(array('position' => 'unknown'));
            $this->fail("Exception expected");
        } catch (Zend_Currency_Exception $e) {
            $this->assertContains("Unknown position", $e->getMessage());
        }

        try {
            $USD->setFormat(array('format' => 'unknown'));
            $this->fail("Exception expected");
        } catch (Zend_Currency_Exception $e) {
            $this->assertContains("is not a known locale", $e->getMessage());
        }

        try {
            $USD->setFormat(array('display' => -14));
            $this->fail("Exception expected");
        } catch (Zend_Currency_Exception $e) {
            $this->assertContains("Unknown display", $e->getMessage());
        }

        try {
            $USD->setFormat(array('script' => 'unknown'));
            $this->fail("Exception expected");
        } catch (Zend_Currency_Exception $e) {
            $this->assertContains("Unknown script", $e->getMessage());
        }

        try {
            $USD->setFormat(array('unknown' => 'unknown'));
            $this->fail("Exception expected");
        } catch (Zend_Currency_Exception $e) {
            $this->assertContains("Unknown option", $e->getMessage());
        }

        $USD->setFormat(array('precision' => null));

        try {
            $USD->setFormat(array('precision' => -14));
            $this->fail("Exception expected");
        } catch (Zend_Currency_Exception $e) {
            $this->assertContains("precision has to be between", $e->getMessage());
        }
    }

    /**
     * test getSign
     */
    public function testGetSign()
    {
        $locale   = new Zend_Locale('ar_EG');
        $currency = new Zend_Currency('ar_EG');

        $this->assertSame('ج.م.‏', $currency->getSymbol('EGP','ar_EG'));
        $this->assertSame('€',    $currency->getSymbol('EUR','de_AT'));
        $this->assertSame('ج.م.‏', $currency->getSymbol('ar_EG'      ));
        $this->assertSame('€',    $currency->getSymbol('de_AT'      ));
        $this->assertSame('ج.م.‏',    $currency->getSymbol());

        try {
            $currency->getSymbol('EGP', 'de_XX');
            $this->fail("exception expected");
        } catch (Zend_Currency_Exception $e) {
            // success
        }
    }

    /**
     * test getName
     */
    public function testGetName()
    {
        $locale   = new Zend_Locale('ar_EG');
        $currency = new Zend_Currency('ar_EG');

        $this->assertSame('جنيه مصرى',       $currency->getName('EGP','ar_EG'));
        $this->assertSame('Estnische Krone', $currency->getName('EEK','de_AT'));
        $this->assertSame('جنيه مصرى',       $currency->getName('EGP',$locale));
        $this->assertSame('جنيه مصرى',       $currency->getName('ar_EG'      ));
        $this->assertSame('Euro',            $currency->getName('de_AT'      ));
        $this->assertSame('جنيه مصرى',       $currency->getName());

        try {
            $currency->getName('EGP', 'xy_XY');
            $this->fail("exception expected");
        } catch (Zend_Currency_Exception $e) {
            // success
        }
    }

    /**
     * test getShortName
     */
    public function testGetShortName()
    {
        $locale   = new Zend_Locale('de_AT');
        $currency = new Zend_Currency('de_AT');

        $this->assertSame('EUR', $currency->getShortName('Euro',     'de_AT'));
        $this->assertSame('EUR', $currency->getShortName('Euro',     $locale));
        $this->assertSame('USD', $currency->getShortName('US-Dollar','de_AT'));
        $this->assertSame('EUR', $currency->getShortName('de_AT'            ));
        $this->assertSame('EUR', $currency->getShortName());

        try {
            $currency->getShortName('EUR', 'xy_ZT');
            $this->fail("exception expected");
        } catch (Zend_Currency_Exception $e) {
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
            $locale = new Zend_Locale();
        } catch (Zend_Locale_Exception $e) {
            $this->markTestSkipped('Autodetection of locale failed');
            return;
        }

        try {
            $currency = new Zend_Currency('USD');
            $this->assertTrue(is_array($currency->getRegionList()));
        } catch (Zend_Currency_Exception $e) {
            $this->assertContains('No region found within the locale', $e->getMessage());
        }

        $currency = new Zend_Currency('USD', 'en_US');
        $currency->setFormat(array('currency' => null));
        try {
            $this->assertEquals('US', $currency->getRegionList());
            $this->fail("Exception expected");
        } catch (Zend_Currency_Exception $e) {
            $this->assertContains("No currency defined", $e->getMessage());
        }

        $currency = new Zend_Currency('USD', 'en_US');
        $this->assertEquals(array(0 => 'AS', 1 => 'EC', 2 => 'FM', 3 => 'GU', 4 => 'IO', 5 => 'MH', 6 => 'MP',
            7 => 'PR', 8 => 'PW', 9 => "SV", 10 => 'TC', 11 => 'TL', 12 => 'UM', 13 => 'US', 14 => 'VG', 15 => 'VI'), $currency->getRegionList());
    }

    /**
     * testing getCurrencyList
     */
    public function testGetCurrencyList()
    {
        // look if locale is detectable
        try {
            $locale = new Zend_Locale();
        } catch (Zend_Locale_Exception $e) {
            $this->markTestSkipped('Autodetection of locale failed');
            return;
        }

        $currency = new Zend_Currency('ar_EG');
        $this->assertTrue(array_key_exists('EGP', $currency->getCurrencyList()));
    }

    /**
     * testing toString
     *
     */
    public function testToString()
    {
        $USD = new Zend_Currency('USD','en_US');
        $this->assertSame('US Dollar', $USD->toString());
        $this->assertSame('US Dollar', $USD->__toString());
    }

    /**
     * testing registry Locale
     * ZF-3676
     */
    public function testRegistryLocale()
    {
        $locale = new Zend_Locale('de_AT');
        require_once 'Zend/Registry.php';
        Zend_Registry::set('Zend_Locale', $locale);

        $currency = new Zend_Currency('EUR');
        $this->assertSame('de_AT', $currency->getLocale());
    }

    /**
     * Caching method tests
     */
    public function testCaching()
    {
        $cache = Zend_Currency::getCache();
        $this->assertTrue($cache instanceof Zend_Cache_Core);
        $this->assertTrue(Zend_Currency::hasCache());

        Zend_Currency::clearCache();
        $this->assertTrue(Zend_Currency::hasCache());

        Zend_Currency::removeCache();
        $this->assertFalse(Zend_Currency::hasCache());
    }

    /**
     * @see ZF-6560
     */
    public function testPrecisionForCurrency()
    {
        $currency = new Zend_Currency(null, 'de_DE');

        $this->assertEquals('75 €', $currency->toCurrency(74.95, array('precision' => 0)));
        $this->assertEquals('75,0 €', $currency->toCurrency(74.95, array('precision' => 1)));
        $this->assertEquals('74,95 €', $currency->toCurrency(74.95, array('precision' => 2)));
        $this->assertEquals('74,950 €', $currency->toCurrency(74.95, array('precision' => 3)));
        $this->assertEquals('74,9500 €', $currency->toCurrency(74.95, array('precision' => 4)));
        $this->assertEquals('74,95000 €', $currency->toCurrency(74.95, array('precision' => 5)));
    }

    /**
     * @see ZF-6561
     */
    public function testNegativeRendering()
    {
        $currency = new Zend_Currency(null, 'de_DE');
        $this->assertEquals('-74,9500 €', $currency->toCurrency(-74.95, array('currency' => 'EUR', 'precision' => 4)));

        $currency = new Zend_Currency(null, 'en_US');
        $this->assertEquals('-$74.9500', $currency->toCurrency(-74.95, array('currency' => 'USD', 'precision' => 4)));
    }

    /**
     * @see ZF-7359
     */
    public function testPHPsScientificBug()
    {
        $currency = new Zend_Currency("USD", "en_US");
        $this->assertEquals('$0.00', $currency->toCurrency(1.0E-4));
        $this->assertEquals('$0.00', $currency->toCurrency(1.0E-5));
    }
}
