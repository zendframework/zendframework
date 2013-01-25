<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Math
 */

namespace ZendTest\Math\BigInteger;

use Zend\Math\BigInteger\BigInteger as BigInt;
use Zend\Math\BigInteger\Adapter;
use Zend\Math\BigInteger\Adapter\AdapterInterface;

/**
 * @category   Zend
 * @package    Zend_Math_BigInteger
 * @subpackage UnitTests
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
        $this->assertTrue($bigInt instanceof Adapter\Bcmath);
    }

    public function testFactoryCreatesGmpAdapter()
    {
        if (!extension_loaded('gmp')) {
            $this->markTestSkipped('Missing gmp extensions');
        }

        $bigInt = BigInt::factory('Gmp');
        $this->assertTrue($bigInt instanceof Adapter\Gmp);
    }

    public function testFactoryUsesDefaultAdapter()
    {
        if (!extension_loaded('bcmath') && !extension_loaded('gmp')) {
            $this->markTestSkipped('Missing bcmath or gmp extensions');
        }
        $this->assertTrue(BigInt::factory() instanceof AdapterInterface);
    }

    public function testFactoryUnknownAdapterRaisesServiceManagerException()
    {
        $this->setExpectedException('Zend\ServiceManager\Exception\ExceptionInterface');
        BigInt::factory('unknown');
    }
}
