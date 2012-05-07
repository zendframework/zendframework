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

namespace Zend\View;

use Zend\Loader\PluginBroker,
    Zend\View\Renderer\RendererInterface as Renderer,
    Zend\View\Helper\HelperInterface as Helper;

/**
 * Helper Broker for view instances
 *
 * Used to retrieve helper instances. Injects the view instance registered into
 * returned helper instances. 
 *
 * @category   Zend
 * @package    Zend_View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class HelperBroker extends PluginBroker
{
    /**
     * @var string Default plugin loading strategy
     */
    protected $defaultClassLoader = 'Zend\View\HelperLoader';

    /**
     * @var Renderer
     */
    protected $view;

    /**
     * Set view object
     * 
     * @param  Renderer $view 
     * @return HelperBroker
     */
    public function setView(Renderer $view)
    {
        $this->view = $view;
        return $this;
    }

    /**
     * Retrieve view instance
     * 
     * @return null|Renderer
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Load a helper
     *
     * Injects the view object into the helper prior to returning it.
     * 
     * @param mixed $plugin 
     * @param array $options 
     * @return void
     */
    public function load($plugin, array $options = null)
    {
        $helper = parent::load($plugin, $options);
        if (null !== ($view = $this->getView())) {
            $helper->setView($view);
        }
        return $helper;
    }

    /**
     * Determine if we have a valid helper
     * 
     * @param  mixed $plugin 
     * @return true
     * @throws Exception\InvalidHelperException
     */
    protected function validatePlugin($plugin)
    {
        if (!$plugin instanceof Helper) {
            throw new Exception\InvalidHelperException('View helpers must implement Zend\View\Helper');
        }
        return true;
    }
}
