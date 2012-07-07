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
 * @package    Zend_Math_BigInteger
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Math\BigInteger;

use Zend\Math\BigInteger\BigInteger as BigInt,
    Zend\Math\BigInteger\Adapter,
    Zend\Math\BigInteger\Adapter\AdapterInterface;

/**
 * @category   Zend
 * @package    Zend_Math_BigInteger
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
