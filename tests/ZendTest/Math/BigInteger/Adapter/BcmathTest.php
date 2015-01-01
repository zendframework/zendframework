<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Math\BigInteger\Adapter;

use Zend\Math\BigInteger\Adapter\Bcmath;

/**
 * @group      Zend_Crypt
 */
class BcmathTest extends AbstractTestCase
{
    public function setUp()
    {
        if (!extension_loaded('bcmath')) {
            $this->markTestSkipped('Missing ext/bcmath');
            return;
        }

        $this->adapter = new Bcmath();
    }

    /**
     * Bcmath adapter test uses common test methods and data providers
     * inherited from abstract @see AbstractTestCase
     */
}
