<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Math
 */

namespace ZendTest\Math\BigInteger\Adapter;

use Zend\Math\BigInteger\Adapter\Gmp;

/**
 * @category   Zend
 * @package    Zend_Math_BigInteger
 * @subpackage UnitTests
 * @group      Zend_Crypt
 */
class GmpTest extends AbstractTestCase
{
    public function setUp()
    {
        if (!extension_loaded('gmp')) {
            $this->markTestSkipped('Missing ext/gmp');
            return;
        }

        $this->adapter = new Gmp();
    }

    /**
     * Gmp adapter test uses common test methods and data providers
     * inherited from abstract @see AbstractTestCase
     */
}
