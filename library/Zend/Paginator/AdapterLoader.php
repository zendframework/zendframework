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

use Zend\Loader\PluginClassLoader;

/**
 * Plugin Class Loader implementation for pagination adapters.
 *
 * @category   Zend
 * @package    Zend_Paginator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class AdapterLoader extends PluginClassLoader
{
    /**
     * @var array Pre-aliased adapters 
     */
    protected $plugins = array(
        'array'           => 'Zend\Paginator\Adapter\ArrayAdapter',
        'db_select'       => 'Zend\Paginator\Adapter\DbSelect',
        'db_table_select' => 'Zend\Paginator\Adapter\DbTableSelect',
        'iterator'        => 'Zend\Paginator\Adapter\Iterator',
        'null'            => 'Zend\Paginator\Adapter\Null',
    );
}
