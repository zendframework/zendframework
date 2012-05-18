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
 * Plugin Class Loader implementation for scrolling style adapters.
 *
 * @category   Zend
 * @package    Zend_Paginator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ScrollingStyleLoader extends PluginClassLoader
{
    /**
     * @var array Pre-aliased adapters 
     */
    protected $plugins = array(
        'all'     => 'Zend\Paginator\ScrollingStyle\All',
        'elastic' => 'Zend\Paginator\ScrollingStyle\Elastic',
        'jumping' => 'Zend\Paginator\ScrollingStyle\Jumping',
        'sliding' => 'Zend\Paginator\ScrollingStyle\Sliding',
    );
}
