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

use Zend\Loader\PluginBroker;

/**
 * Broker for pagination adapter instances
 *
 * @category   Zend
 * @package    Zend_Paginator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class AdapterBroker extends PluginBroker
{
    /**
     * @var string Default plugin loading strategy
     */
    protected $defaultClassLoader = 'Zend\Paginator\AdapterLoader';

    /**
     * @var boolean Adapters must not be registered on load
     */
    protected $registerPluginsOnLoad = false;

    /**
     * Determine if we have a valid adapter
     * 
     * @param  mixed $plugin 
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    protected function validatePlugin($plugin)
    {
        if (!$plugin instanceof Adapter\AdapterInterface) {
            throw new Exception\InvalidArgumentException(
                'Pagination adapters must implement Zend\Paginator\Adapter\AdapterInterface');
        }
        return true;
    }
}
