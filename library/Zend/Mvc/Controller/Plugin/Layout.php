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

use Zend\Mvc\InjectApplicationEvent,
    Zend\Mvc\Exception,
    Zend\Mvc\MvcEvent,
    Zend\View\Model;

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
     * Set the layout
     * 
     * @param  string $template 
     * @return void
     */
    public function setLayout($template)
    {
        $event     = $this->getEvent();
        $viewModel = $event->getViewModel();
        if (!$viewModel instanceof Model) {
            throw new Exception\DomainException('Layout plugin requires that event view model is populated');
        }
        $viewModel->setTemplate((string) $template);
    }

    /**
     * Invoke as a functor
     *
     * Proxies to {setLayout()}.
     * 
     * @param  string $template 
     * @return void
     */
    public function __invoke($template)
    {
        return $this->setLayout($template);
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
        if (!$controller instanceof InjectApplicationEvent) {
            throw new Exception\DomainException('Layout plugin requires a controller that implements InjectApplicationEvent');
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
}
