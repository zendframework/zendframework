<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Math\BigInteger;

use Zend\Math\BigInteger\BigInteger as BigInt;

/**
 * @group      Zend_Math
 */
class BigIntegerTest extends \PHPUnit_Framework_TestCase
{
    public function testFactoryCreatesBcmathAdapter()
    {
        if (!extension_loaded('bcmath')) {
            $this->markTestSkipped('Missing bcmath extensions');
        }

        $bigInt = BigInt::factory('Bcmath');
        $this->assertInstanceOf('Zend\Math\BigInteger\Adapter\Bcmath', $bigInt);
    }

    public function testFactoryCreatesGmpAdapter()
    {
        if (!extension_loaded('gmp')) {
            $this->markTestSkipped('Missing gmp extensions');
        }

        $bigInt = BigInt::factory('Gmp');
        $this->assertInstanceOf('Zend\Math\BigInteger\Adapter\Gmp', $bigInt);
    }

    public function testFactoryUsesDefaultAdapter()
    {
        if (!extension_loaded('bcmath') && !extension_loaded('gmp')) {
            $this->markTestSkipped('Missing bcmath or gmp extensions');
        }
        $this->assertInstanceOf('Zend\Math\BigInteger\Adapter\AdapterInterface', BigInt::factory());
    }

    public function testFactoryUnknownAdapterRaisesServiceManagerException()
    {
        $this->setExpectedException('Zend\ServiceManager\Exception\ExceptionInterface');
        BigInt::factory('unknown');
    }
}
