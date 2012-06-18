<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Math
 */

namespace Zend\Math\BigInteger;

use Zend\Math\BigInteger\Adapter\AdapterInterface;
use Zend\Loader\PluginBroker;

/**
 * Broker for BigInteger adapter instances
 *
 * @category   Zend
 * @package    Zend_Math
 * @subpackage BigInteger
 */
class AdapterBroker extends PluginBroker
{
    /**
     * @var string Default plugin loading strategy
     */
    protected $defaultClassLoader = 'Zend\Math\BigInteger\AdapterLoader';

    /**
     * Determine if we have a valid adapter
     * 
     * @param  mixed $plugin 
     * @return bool
     * @throws Exception\RuntimeException
     */
    protected function validatePlugin($plugin)
    {
        if (!$plugin instanceof AdapterInterface) {
            throw new Exception\RuntimeException(
                'BigInteger adapters must implement Zend\Math\BigInteger\Adapter\AdapterInterface'
            );
        }
        return true;
    }
}
