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
 * @package    Zend_Cache
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cache;

use Zend\Loader\PluginClassLoader;

/**
 * Plugin Class Loader implementation for cache patterns.
 *
 * @category   Zend
 * @package    Zend_Cache
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PatternLoader extends PluginClassLoader
{
    /**
     * @var array Pre-aliased adapters
     */
    protected $plugins = array(
        'callback' => 'Zend\Cache\Pattern\CallbackCache',
        'capture'  => 'Zend\Cache\Pattern\CaptureCache',
        'class'    => 'Zend\Cache\Pattern\ClassCache',
        'object'   => 'Zend\Cache\Pattern\ObjectCache',
        'output'   => 'Zend\Cache\Pattern\OutputCache',
        'page'     => 'Zend\Cache\Pattern\PageCache',
    );
}
