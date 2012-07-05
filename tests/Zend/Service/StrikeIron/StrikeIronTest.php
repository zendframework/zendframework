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
 * @package    Zend_Service_StrikeIron
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Service\StrikeIron;

/**
 * @category   Zend
 * @package    Zend_Service_StrikeIron
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_StrikeIron
 */
class StrikeIronTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // stub out SOAPClient instance
        $this->soapClient = new \stdclass();
        $this->options    = array('client' => $this->soapClient);
        $this->strikeIron = new \Zend\Service\StrikeIron\StrikeIron($this->options);
    }

    public function testFactoryThrowsOnBadName()
    {
        $this->setExpectedException('Zend\Service\StrikeIron\Exception\ExceptionInterface', 'Class file not found');
        $this->strikeIron->getService(array('class' => 'BadServiceNameHere'));
    }

    public function testFactoryReturnsServiceByStrikeIronClass()
    {
        $base = $this->strikeIron->getService(array('class' => 'Base'));
        $this->assertInstanceOf('Zend\Service\StrikeIron\Base', $base);
        $this->assertSame(null, $base->getWsdl());
        $this->assertSame($this->soapClient, $base->getSoapClient());
    }

    public function testFactoryReturnsServiceAnySlashedClass()
    {
        $class = 'ZendTest\Service\StrikeIron\StrikeIronTest\StubbedBase';
        $stub = $this->strikeIron->getService(array('class' => $class));
        $this->assertInstanceOf($class, $stub);
    }

    public function testFactoryReturnsServiceByWsdl()
    {
        $wsdl = 'http://strikeiron.com/foo';
        $base = $this->strikeIron->getService(array('wsdl' => $wsdl));
        $this->assertEquals($wsdl, $base->getWsdl());
    }

    public function testFactoryPassesOptionsFromConstructor()
    {
        $class = 'ZendTest\Service\StrikeIron\StrikeIronTest\StubbedBase';
        $stub = $this->strikeIron->getService(array('class' => $class));
        $this->assertEquals($this->options, $stub->options);
    }

    public function testFactoryMergesItsOptionsWithConstructorOptions()
    {
        $options = array('class' => 'ZendTest\Service\StrikeIron\StrikeIronTest\StubbedBase',
                         'foo'   => 'bar');

        $mergedOptions = array_merge($options, $this->options);
        unset($mergedOptions['class']);

        $stub = $this->strikeIron->getService($options);
        $this->assertEquals($mergedOptions, $stub->options);
    }

}
