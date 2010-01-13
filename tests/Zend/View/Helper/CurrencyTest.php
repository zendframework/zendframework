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
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: TranslateTest.php 18387 2010-09-23 21:00:00Z thomas $
 */

// Call Zend_View_Helper_CurrencyTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_CurrencyTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';

/** Zend_View_Helper_Currency */
require_once 'Zend/View/Helper/Currency.php';

/** Zend_Registry */
require_once 'Zend/Registry.php';

/** Zend_Currency */
require_once 'Zend/Currency.php';

/**
 * Test class for Zend_View_Helper_Currency
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class Zend_View_Helper_CurrencyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_View_Helper_Currency
     */
    public $helper;

    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_CurrencyTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function clearRegistry()
    {
        $regKey = 'Zend_Currency';
        if (Zend_Registry::isRegistered($regKey)) {
            $registry = Zend_Registry::getInstance();
            unset($registry[$regKey]);
        }
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->clearRegistry();
        require_once 'Zend/Cache.php';
        $this->_cache = Zend_Cache::factory('Core', 'File',
                 array('lifetime' => 120, 'automatic_serialization' => true),
                 array('cache_dir' => dirname(__FILE__) . '/../../_files/'));
        Zend_Currency::setCache($this->_cache);

        $this->helper = new Zend_View_Helper_Currency('de_AT');
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->helper);
        $this->_cache->clean(Zend_Cache::CLEANING_MODE_ALL);
        $this->clearRegistry();
    }

    public function testCurrencyObjectPassedToConstructor()
    {
        $curr = new Zend_Currency('de_AT');

        $helper = new Zend_View_Helper_Currency($curr);
        $this->assertEquals('€ 1.234,56', $helper->currency(1234.56));
        $this->assertEquals('€ 0,12', $helper->currency(0.123));
    }

    public function testLocalCurrencyObjectUsedWhenPresent()
    {
        $curr = new Zend_Currency('de_AT');

        $this->helper->setCurrency($curr);
        $this->assertEquals('€ 1.234,56', $this->helper->currency(1234.56));
        $this->assertEquals('€ 0,12', $this->helper->currency(0.123));
    }

    public function testCurrencyObjectInRegistryUsedInAbsenceOfLocalCurrencyObject()
    {
        $curr = new Zend_Currency('de_AT');
        Zend_Registry::set('Zend_Currency', $curr);
        $this->assertEquals('€ 1.234,56', $this->helper->currency(1234.56));
    }

    public function testPassingNonNullNonCurrencyObjectToConstructorThrowsException()
    {
        try {
            $helper = new Zend_View_Helper_Currency('something');
        } catch (Exception $e) {
            $this->assertContains('not found', $e->getMessage());
        }
    }

    public function testPassingNonCurrencyObjectToSetCurrencyThrowsException()
    {
        try {
            $this->helper->setCurrency('something');
        } catch (Exception $e) {
            $this->assertContains('not found', $e->getMessage());
        }
    }

    public function testCanOutputCurrencyWithOptions()
    {
        $curr = new Zend_Currency('de_AT');

        $this->helper->setCurrency($curr);
        $this->assertEquals("€ 1.234,56", $this->helper->currency(1234.56, "de_AT"));
    }

    public function testCurrencyObjectNullByDefault()
    {
        $this->assertNotNull($this->helper->getCurrency());
    }

    public function testLocalCurrencyObjectIsPreferredOverRegistry()
    {
        $currReg = new Zend_Currency('de_AT');
        Zend_Registry::set('Zend_Currency', $currReg);

        $this->helper = new Zend_View_Helper_Currency();
        $this->assertSame($currReg, $this->helper->getCurrency());

        $currLoc = new Zend_Currency('en_US');
        $this->helper->setCurrency($currLoc);
        $this->assertSame($currLoc, $this->helper->getCurrency());
        $this->assertNotSame($currLoc, $currReg);
    }

    public function testHelperObjectReturnedWhenNoArgumentsPassed()
    {
        $helper = $this->helper->currency();
        $this->assertSame($this->helper, $helper);

        $currLoc = new Zend_Currency('de_AT');
        $this->helper->setCurrency($currLoc);
        $helper = $this->helper->currency();
        $this->assertSame($this->helper, $helper);
    }
}

// Call Zend_View_Helper_TranslateTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_TranslateTest::main") {
    Zend_View_Helper_TranslateTest::main();
}
