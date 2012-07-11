<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
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
    protected $adapters = array();

    protected $availableAdapter = null;

    public function setUp()
    {
        if (extension_loaded('bcmath')) {
            $this->adapters['bcmath'] = true;
            $this->availableAdapter = 'Bcmath';
        }
        if (extension_loaded('gmp')) {
            $this->adapters['gmp'] = true;
            $this->availableAdapter = 'Gmp';
        }

        if (null == $this->availableAdapter) {
            $this->markTestSkipped('Missing bcmath or gmp extensions');
        }
    }

    public function testFactoryValidAdapter()
    {
        if ($this->availableAdapter === 'Gmp') {
            $bigInt = BigInt::factory('Gmp');
            $this->assertTrue($bigInt instanceof Adapter\Gmp);
        } elseif ($this->availableAdapter === 'Bcmath') {
            $bigInt = BigInt::factory('Bcmath');
            $this->assertTrue($bigInt instanceof Adapter\Bcmath);
        }
    }

    public function testFactoryNoAdapter()
    {
        $this->assertTrue(BigInt::factory() instanceof AdapterInterface);
    }

    public function testFactoryUnknownAdapter()
    {
        $this->setExpectedException('Zend\ServiceManager\Exception\ExceptionInterface');
        BigInt::factory('unknown');
    }
}
