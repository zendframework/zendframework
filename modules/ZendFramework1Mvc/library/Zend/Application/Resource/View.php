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
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Application\Resource;

use Zend\Controller\Action\Helper\ViewRenderer;

/**
 * Resource for settings view options
 *
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class View extends AbstractResource
{
    /**
     * @var \Zend\View\Renderer
     */
    protected $_view;

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return \Zend\View\View
     */
    public function init()
    {
        $front     = false;
        $bootstrap = $this->getBootstrap();
        if ($bootstrap->getBroker()->hasPlugin('frontcontroller')) {
            $bootstrap->bootstrap('frontcontroller');
            $front = $bootstrap->getResource('frontcontroller');
        }
        $view = $this->getView();

        if ($front) {
            $viewRenderer = new ViewRenderer();
            $viewRenderer->setView($view);
            $front->getHelperBroker()->register('viewrenderer', $viewRenderer);
        }
        return $view;
    }

    /**
     * Retrieve view object
     *
     * @return \Zend\View\View
     */
    public function getView()
    {
        if (null === $this->_view) {
            $options = $this->getOptions();
            $this->_view = new \Zend\View\PhpRenderer($options);

            if(isset($options['doctype'])) {
                $this->_view->plugin('doctype')->setDoctype(strtoupper($options['doctype']));
            }
        }
        return $this->_view;
    }
}
