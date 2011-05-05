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
 * @package    Zend_Paginator
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Paginator;

use Zend\Loader\PluginBroker;

/**
 * Broker for scrolling-style adapter instances
 *
 * @category   Zend
 * @package    Zend_Paginator
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
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
     * @return true
     * @throws Exception
     */
    protected function validatePlugin($plugin)
    {
        if (!$plugin instanceof ScrollingStyle) {
            throw new Exception('ScrollingStyle adapters must implement Zend\Paginator\ScrollingStyle');
        }
        return true;
    }
}
