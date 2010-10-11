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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Application;

use Zend\Loader\PluginClassLoader;

/**
 * Plugin Class Loader implementation for bootstrap resources.
 *
 * @category   Zend
 * @package    Zend_View
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ResourceLoader extends PluginClassLoader
{
    /**
     * @var array Pre-aliased bootstrap resources
     */
    protected $plugins = array(
        'cachemanager'    => __DIR__ . '/Resources/CacheManager.php',
        'db'              => __DIR__ . '/Resources/Db.php',
        'dojo'            => __DIR__ . '/Resources/Dojo.php',
        'frontcontroller' => __DIR__ . '/Resources/FrontController.php',
        'layout'          => __DIR__ . '/Resources/Layout.php',
        'locale'          => __DIR__ . '/Resources/Locale.php',
        'log'             => __DIR__ . '/Resources/Log.php',
        'mail'            => __DIR__ . '/Resources/Mail.php',
        'modules'         => __DIR__ . '/Resources/Modules.php',
        'multidb'         => __DIR__ . '/Resources/MultiDb.php',
        'navigation'      => __DIR__ . '/Resources/Navigation.php',
        'router'          => __DIR__ . '/Resources/Router.php',
        'session'         => __DIR__ . '/Resources/Session.php',
        'translate'       => __DIR__ . '/Resources/Translate.php',
        'view'            => __DIR__ . '/Resources/View.php',
    );
}
