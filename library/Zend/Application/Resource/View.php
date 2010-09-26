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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Application\Resource;

/**
 * Resource for settings view options
 *
 * @uses       \Zend\Application\Resource\AbstractResource
 * @uses       \Zend\Controller\Action\HelperBroker
 * @uses       \Zend\Controller\Action\Helper\ViewRenderer
 * @uses       \Zend\View\View
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class View extends AbstractResource
{
    /**
     * @var \Zend\View\ViewEngine
     */
    protected $_view;

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return \Zend\View\View
     */
    public function init()
    {
        $view = $this->getView();

        $viewRenderer = new \Zend\Controller\Action\Helper\ViewRenderer();
        $viewRenderer->setView($view);
        \Zend\Controller\Action\HelperBroker::addHelper($viewRenderer);
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
            $this->_view = new \Zend\View\View($options);

            if(isset($options['doctype'])) {
                $this->_view->doctype()->setDoctype(strtoupper($options['doctype']));
            }
        }
        return $this->_view;
    }
}
