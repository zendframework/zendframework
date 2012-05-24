<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Crypt
 */

namespace Zend\Crypt\Symmetric;

use Zend\Loader\PluginBroker;

/**
 * Broker for symmetric cipher adapter instances
 *
 * @category   Zend
 * @package    Zend_Crypt
 * @subpackage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PaddingBroker extends PluginBroker
{
    /**
     * @var string Default plugin loading strategy
     */
    protected $defaultClassLoader = 'Zend\Crypt\Symmetric\PaddingLoader';

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
        if (!$plugin instanceof Padding\PaddingInterface) {
            throw new Exception\InvalidArgumentException(
                'Padding adapter must implement Zend\Crypt\Symmetric\Padding\PaddingInterface');
        }
        return true;
    }
}