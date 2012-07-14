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

use Zend\ServiceManager\AbstractPluginManager;

/**
 * Plugin manager implementation for pagination adapters
 *
 * Enforces that adapters retrieved are instances of
 * Adapter\AdapterInterface. Additionally, it registers a number
 * of default adapters available.
 *
 * @category   Zend
 * @package    Zend_Paginator
 */
class AdapterPluginManager extends AbstractPluginManager
{
    /**
     * Default set of adapters
     *
     * @var array
     */
    protected $invokableClasses = array(
        'array'         => 'Zend\Paginator\Adapter\ArrayAdapter',
        'dbselect'      => 'Zend\Paginator\Adapter\DbSelect',
        'dbtableselect' => 'Zend\Paginator\Adapter\DbTableSelect',
        'iterator'      => 'Zend\Paginator\Adapter\Iterator',
        'null'          => 'Zend\Paginator\Adapter\Null',
    );

    /**
     * @var bool Do not share by default
     */
    protected $shareByDefault = false;

    /**
     * Validate the plugin
     *
     * Checks that the adapter loaded is an instance of Adapter\AdapterInterface.
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\InvalidArgumentException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof Adapter\AdapterInterface) {
            // we're okay
            return;
        }

        throw new Exception\InvalidArgumentException(sprintf(
            'Plugin of type %s is invalid; must implement %s\Adapter\AdapterInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
