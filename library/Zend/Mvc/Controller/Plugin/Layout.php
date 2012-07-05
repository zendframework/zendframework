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

namespace Zend\Mvc\Controller\Plugin;

use Zend\Mvc\InjectApplicationEventInterface;
use Zend\Mvc\Exception;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ModelInterface as Model;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Controller
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Layout extends AbstractPlugin
{
    /**
     * @var MvcEvent
     */
    protected $event;

    /**
     * Set the layout template
     * 
     * @param  string $template 
     * @return Layout
     */
    public function setTemplate($template)
    {
        $viewModel = $this->getViewModel();
        $viewModel->setTemplate((string) $template);
        return $this;
    }

    /**
     * Invoke as a functor
     *
     * If no arguments are given, grabs the "root" or "layout" view model.
     * Otherwise, attempts to set the template for that view model.
     * 
     * @param  null|string $template 
     * @return Model|Layout
     */
    public function __invoke($template = null)
    {
        if (null === $template) {
            return $this->getViewModel();
        }
        return $this->setTemplate($template);
    }

    /**
     * Get the event
     *
     * @return MvcEvent
     * @throws Exception\DomainException if unable to find event
     */
    protected function getEvent()
    {
        if ($this->event) {
            return $this->event;
        }

        $controller = $this->getController();
        if (!$controller instanceof InjectApplicationEventInterface) {
            throw new Exception\DomainException('Layout plugin requires a controller that implements InjectApplicationEventInterface');
        }

        $event = $controller->getEvent();
        if (!$event instanceof MvcEvent) {
            $params = $event->getParams();
            $event  = new MvcEvent();
            $event->setParams($params);
        }
        $this->event = $event;

        return $this->event;
    }

    /**
     * Retrieve the root view model from the event
     * 
     * @return Model
     * @throws Exception\DomainException
     */
    protected function getViewModel()
    {
        $event     = $this->getEvent();
        $viewModel = $event->getViewModel();
        if (!$viewModel instanceof Model) {
            throw new Exception\DomainException('Layout plugin requires that event view model is populated');
        }
        return $viewModel;
    }
}
