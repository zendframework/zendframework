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
 * @package    Zend_View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Application;

use Zend\Loader\PluginClassLoader;

/**
 * Plugin Class Loader implementation for bootstrap resources.
 *
 * @category   Zend
 * @package    Zend_View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ResourceLoader extends PluginClassLoader
{
    /**
     * @var array Pre-aliased bootstrap resources
     */
    protected $plugins = array(
        'cachemanager'    => 'Zend\Application\Resource\CacheManager',
        'db'              => 'Zend\Application\Resource\Db',
        'dojo'            => 'Zend\Application\Resource\Dojo',
        'frontcontroller' => 'Zend\Application\Resource\FrontController',
        'layout'          => 'Zend\Application\Resource\Layout',
        'locale'          => 'Zend\Application\Resource\Locale',
        'log'             => 'Zend\Application\Resource\Log',
        'mail'            => 'Zend\Application\Resource\Mail',
        'modules'         => 'Zend\Application\Resource\Modules',
        'multidb'         => 'Zend\Application\Resource\MultiDb',
        'navigation'      => 'Zend\Application\Resource\Navigation',
        'router'          => 'Zend\Application\Resource\Router',
        'session'         => 'Zend\Application\Resource\Session',
        'translator'      => 'Zend\Application\Resource\Translator',
        'view'            => 'Zend\Application\Resource\View',
    );
}
