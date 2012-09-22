<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace Zend\Mvc\Controller\Plugin;

use Zend\Http\Request;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Model\JsonModel;
use Zend\View\Model\FeedModel;
use Zend\View\Model\ModelInterface;
use Zend\View\Model\ViewModel;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\Mvc\MvcEvent;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Controller
 */
class AcceptedModel extends AbstractPlugin
{
    protected $event;
    protected $request;

    /**
     * Detects an appropriate model for view.
     *
     * @return ModelInterface
     */
    public function __invoke()
    {
        $request = $this->getRequest();
        $headers = $request->getHeaders();

        if (!$headers->has('accept')) {
            return new ViewModel();
        }

        $accept = $headers->get('Accept');
        foreach ($accept->getPrioritized() as $acceptPart) {
            if (in_array($acceptPart->getTypeString(), array('application/xhtml+xml', 'text/html', 'application/xml'))) {
                return new ViewModel();
            }

            if (in_array($acceptPart->getTypeString(), array('application/json', 'application/javascript'))) {
                return new JsonModel();
            }

            if (in_array($acceptPart->getTypeString(), array('application/rss+xml', 'application/atom+xml'))) {
                return new FeedModel();
            }
        }

        return new ViewModel();
    }

    /**
     * Get the request
     *
     * @return Request
     * @throws Exception\DomainException if unable to find request
     */
    protected function getRequest()
    {
        if ($this->request) {
            return $this->request;
        }

        $event = $this->getEvent();
        $request = $event->getRequest();
        if (!$request instanceof Request) {
            throw new Exception\DomainException('AcceptedModel plugin requires event compose a request');
        }
        $this->request = $request;

        return $this->request;
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
            throw new Exception\DomainException('AcceptedModel plugin requires a controller that implements InjectApplicationEventInterface');
        }

        $event = $controller->getEvent();
        if (!$event instanceof MvcEvent) {
            $params = $event->getParams();
            $event = new MvcEvent();
            $event->setParams($params);
        }
        $this->event = $event;

        return $this->event;
    }

}
