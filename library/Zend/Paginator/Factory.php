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

use Zend\Paginator\Adapter\AdapterInterface;

/**
 * @category   Zend
 * @package    Zend_Paginator
 */
abstract class Factory
{
    protected static $adapters;
    
    public static function factory($items, $adapter)
    {   
        if(!$adapter instanceof AdapterInterface && !$adapter instanceof AdapterAggregateInterface) {
            $adapter = self::getAdapterPluginManager()->get($adapter, $items);
        }
        
        return new Paginator($adapter);
    }
    
    /**
     * Change the adapter plugin manager
     *
     * @param  AdapterPluginManager $adapters
     * @return void
     */
    public static function setAdapterPluginManager(AdapterPluginManager $adapters)
    {
        self::$adapters = $adapters;
    }

    /**
     * Get the adapter plugin manager
     *
     * @return AdapterPluginManager
     */
    public static function getAdapterPluginManager()
    {
        if (self::$adapters === null) {
            self::$adapters = new AdapterPluginManager();
        }
        return self::$adapters;
    }
}
