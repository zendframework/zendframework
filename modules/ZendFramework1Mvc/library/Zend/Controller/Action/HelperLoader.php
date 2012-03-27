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
 * @package    Zend_Controller
 * @subpackage Action
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Controller\Action;

use Zend\Loader\PluginClassLoader;

/**
 * Plugin Class Loader implementation for action helpers.
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Action
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class HelperLoader extends PluginClassLoader
{
    /**
     * @var array Pre-aliased view helpers
     */
    protected $plugins = array(
        'action_stack'       => 'Zend\Controller\Action\Helper\ActionStack',
        'actionstack'        => 'Zend\Controller\Action\Helper\ActionStack',
        'ajax_context'       => 'Zend\Controller\Action\Helper\AjaxContext',
        'ajaxcontext'        => 'Zend\Controller\Action\Helper\AjaxContext',
        'auto_complete_dojo' => 'Zend\Controller\Action\Helper\AutoCompleteDojo',
        'autocompletedojo'   => 'Zend\Controller\Action\Helper\AutoCompleteDojo',
        'cache'              => 'Zend\Controller\Action\Helper\Cache',
        'context_switch'     => 'Zend\Controller\Action\Helper\ContextSwitch',
        'contextswitch'      => 'Zend\Controller\Action\Helper\ContextSwitch',
        'flash_messenger'    => 'Zend\Controller\Action\Helper\FlashMessenger',
        'flashmessenger'     => 'Zend\Controller\Action\Helper\FlashMessenger',
        'json'               => 'Zend\Controller\Action\Helper\Json',
        'redirector'         => 'Zend\Controller\Action\Helper\Redirector',
        'url'                => 'Zend\Controller\Action\Helper\Url',
        'view_renderer'      => 'Zend\Controller\Action\Helper\ViewRenderer',
        'viewrenderer'       => 'Zend\Controller\Action\Helper\ViewRenderer',
    );
}
