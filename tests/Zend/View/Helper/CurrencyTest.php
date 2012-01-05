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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\View\Helper;

use Zend\Cache\StorageFactory as CacheFactory,
    Zend\Cache\Storage\Adapter as CacheAdapter,
    Zend\Currency,
    Zend\View\Helper;

/**
 * Test class for Zend_View_Helper_Currency
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class CurrencyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_View_Helper_Currency
     */
    public $helper;

    public function clearRegistry()
    {
        $regKey = 'Zend_Currency';
        if (\Zend\Registry::isRegistered($regKey)) {
            $registry = \Zend\Registry::getInstance();
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

        $this->_cacheDir = sys_get_temp_dir() . '/zend_view_helper_currency';
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

        $this->helper = new Helper\Currency('de_AT');
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
        $this->_cache->clear(CacheAdapter::MATCH_ALL);
        $this->_removeRecursive($this->_cacheDir);
        $this->clearRegistry();
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

    public function testCurrencyObjectPassedToConstructor()
    {
        $curr = new Currency\Currency('de_AT');

        $helper = new Helper\Currency($curr);
        $this->assertEquals('€ 1.234,56', $helper->__invoke(1234.56));
        $this->assertEquals('€ 0,12', $helper->__invoke(0.123));
    }

    public function testLocalCurrencyObjectUsedWhenPresent()
    {
        $curr = new Currency\Currency('de_AT');

        $this->helper->setCurrency($curr);
        $this->assertEquals('€ 1.234,56', $this->helper->__invoke(1234.56));
        $this->assertEquals('€ 0,12', $this->helper->__invoke(0.123));
    }

    public function testCurrencyObjectInRegistryUsedInAbsenceOfLocalCurrencyObject()
    {
        $curr = new Currency\Currency('de_AT');
        \Zend\Registry::set('Zend_Currency', $curr);
        $this->assertEquals('€ 1.234,56', $this->helper->__invoke(1234.56));
    }

    public function testPassingNonNullNonCurrencyObjectToConstructorThrowsException()
    {
        try {
            $helper = new Helper\Currency('something');
        } catch (\Exception $e) {
            if (substr($e->getMessage(), 0, 15) == 'No region found') {
                $this->assertContains('within the locale', $e->getMessage());
            } else {
                $this->assertContains('not found', $e->getMessage());
            }
        }
    }

    public function testPassingNonCurrencyObjectToSetCurrencyThrowsException()
    {
        try {
            $this->helper->setCurrency('something');
        } catch (\Exception $e) {
            if (substr($e->getMessage(), 0, 15) == 'No region found') {
                $this->assertContains('within the locale', $e->getMessage());
            } else {
                $this->assertContains('not found', $e->getMessage());
            }
        }
    }

    public function testCanOutputCurrencyWithOptions()
    {
        $curr = new Currency\Currency('de_AT');

        $this->helper->setCurrency($curr);
        $this->assertEquals("€ 1.234,56", $this->helper->__invoke(1234.56, "de_AT"));
    }

    public function testCurrencyObjectNullByDefault()
    {
        $this->assertNotNull($this->helper->getCurrency());
    }

    public function testLocalCurrencyObjectIsPreferredOverRegistry()
    {
        $currReg = new Currency\Currency('de_AT');
        \Zend\Registry::set('Zend_Currency', $currReg);

        $this->helper = new Helper\Currency();
        $this->assertSame($currReg, $this->helper->getCurrency());

        $currLoc = new Currency\Currency('en_US');
        $this->helper->setCurrency($currLoc);
        $this->assertSame($currLoc, $this->helper->getCurrency());
        $this->assertNotSame($currLoc, $currReg);
    }

    public function testHelperObjectReturnedWhenNoArgumentsPassed()
    {
        $helper = $this->helper->__invoke();
        $this->assertSame($this->helper, $helper);

        $currLoc = new Currency\Currency('de_AT');
        $this->helper->setCurrency($currLoc);
        $helper = $this->helper->__invoke();
        $this->assertSame($this->helper, $helper);
    }
}
