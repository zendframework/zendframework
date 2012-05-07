<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Paginator
 */

namespace Zend\Paginator;

use Zend\Loader\PluginBroker,
    Zend\Paginator\ScrollingStyle\ScrollingStyleInterface;

/**
 * Broker for scrolling-style adapter instances
 *
 * @category   Zend
 * @package    Zend_Paginator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ScrollingStyleBroker extends PluginBroker
{
    /**
     * @var string Default plugin loading strategy
     */
    protected $defaultClassLoader = 'Zend\Paginator\ScrollingStyleLoader';

    /**
     * Determine if we have a valid adapter
     * 
     * @param  mixed $plugin
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    protected function validatePlugin($plugin)
    {
        if (!$plugin instanceof ScrollingStyleInterface) {
            throw new Exception\InvalidArgumentException(
                'ScrollingStyle adapters must implement Zend\Paginator\ScrollingStyle\ScrollingStyleInterface'
            );
        }
        return true;
    }
}
