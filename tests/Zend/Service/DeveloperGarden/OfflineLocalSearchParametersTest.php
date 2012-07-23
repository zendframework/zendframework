<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

/**
 * Zend_Service_DeveloperGarden test case
 *
 * @category   Zend
 * @package    Zend_Service_DeveloperGarden
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_DeveloperGarden
 */
class Zend_Service_DeveloperGarden_OfflineLocalSearchParametersTest extends PHPUnit_Framework_TestCase
{
    /**
     * @todo add more search param tests
     */

    /**
     * @var Zend_Service_DeveloperGarden_OfflineLocalSearch_Mock
     */
    protected $_service = null;

    public function setUp()
    {
        if (!defined('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_LOGIN')) {
            define('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_LOGIN', 'Unknown');
        }
        if (!defined('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_PASSWORD')) {
            define('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_PASSWORD', 'Unknown');
        }
        $config = array(
            'username' => TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_LOGIN,
            'password' => TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_PASSWORD,
        );
        $this->service = new Zend_Service_DeveloperGarden_OfflineLocalSearch_Mock($config);
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_LocalSearch_Exception
     */
    public function testSetHitsNegative()
    {
        $param = new Zend_Service_DeveloperGarden_LocalSearch_SearchParameters();
        $param->setHits(-1);
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_LocalSearch_Exception
     */
    public function testSetHitsToHigh()
    {
        $param = new Zend_Service_DeveloperGarden_LocalSearch_SearchParameters();
        $param->setHits(1001);
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_LocalSearch_Exception
     */
    public function testSetHitsToHighVeryLarge()
    {
        $param = new Zend_Service_DeveloperGarden_LocalSearch_SearchParameters();
        $param->setHits(100001);
    }

    public function testSetHits()
    {
        $param = new Zend_Service_DeveloperGarden_LocalSearch_SearchParameters();
        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_LocalSearch_SearchParameters',
            $param->setHits(1000)
        );
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_LocalSearch_Exception
     */
    public function testCollapseValuesWrong()
    {
        $param = new Zend_Service_DeveloperGarden_LocalSearch_SearchParameters();
        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_LocalSearch_SearchParameters',
            $param->setCollapse('SomeStrangeValue')
        );
    }

    public function testCollapseValuesTrue()
    {
        $param = new Zend_Service_DeveloperGarden_LocalSearch_SearchParameters();
        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_LocalSearch_SearchParameters',
            $param->setCollapse(true)
        );
    }

    public function testCollapseValuesFalse()
    {
        $param = new Zend_Service_DeveloperGarden_LocalSearch_SearchParameters();
        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_LocalSearch_SearchParameters',
            $param->setCollapse(false)
        );
    }

    public function testCollapseValuesAddressCompany()
    {
        $param = new Zend_Service_DeveloperGarden_LocalSearch_SearchParameters();
        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_LocalSearch_SearchParameters',
            $param->setCollapse('ADDRESS_COMPANY')
        );
    }

    public function testCollapseValuesDomain()
    {
        $param = new Zend_Service_DeveloperGarden_LocalSearch_SearchParameters();
        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_LocalSearch_SearchParameters',
            $param->setCollapse('DOMAIN')
        );
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_LocalSearch_Exception
     */
    public function testWhereEmpty()
    {
        $param = new Zend_Service_DeveloperGarden_LocalSearch_SearchParameters();
        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_LocalSearch_SearchParameters',
            $param->setWhere(null)
        );
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_LocalSearch_Exception
     */
    public function testRadiusWithString()
    {
        $param = new Zend_Service_DeveloperGarden_LocalSearch_SearchParameters();
        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_LocalSearch_SearchParameters',
            $param->setRadius('foobar')
        );
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_LocalSearch_Exception
     */
    public function testRadiusWithStringAndInteger()
    {
        $param = new Zend_Service_DeveloperGarden_LocalSearch_SearchParameters();
        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_LocalSearch_SearchParameters',
            $param->setRadius('1a')
        );
    }

    public function testRadiusWithIntegerAsString()
    {
        $param = new Zend_Service_DeveloperGarden_LocalSearch_SearchParameters();
        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_LocalSearch_SearchParameters',
            $param->setRadius('-100')
        );
    }
}

class Zend_Service_DeveloperGarden_OfflineLocalSearch_Mock
    extends Zend_Service_DeveloperGarden_LocalSearch
{

}
