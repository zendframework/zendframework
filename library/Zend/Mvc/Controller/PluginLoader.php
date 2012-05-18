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
 * @package    Zend_Mvc
 * @subpackage Controller
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Mvc\Controller;

use Zend\Loader\PluginClassLoader;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Controller
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PluginLoader extends PluginClassLoader
{
    /**
     * @var array Pre-aliased plugins
     */
    protected $plugins = array(
        'flash_messenger' => 'Zend\Mvc\Controller\Plugin\FlashMessenger',
        'flashmessenger'  => 'Zend\Mvc\Controller\Plugin\FlashMessenger',
        'forward'         => 'Zend\Mvc\Controller\Plugin\Forward',
        'layout'          => 'Zend\Mvc\Controller\Plugin\Layout',
        'redirect'        => 'Zend\Mvc\Controller\Plugin\Redirect',
        'url'             => 'Zend\Mvc\Controller\Plugin\Url',
    );
}
