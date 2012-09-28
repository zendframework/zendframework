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
use Zend\Http\Header\Accept\FieldValuePart\AbstractFieldValuePart;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Model\ModelInterface;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\Mvc\MvcEvent;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Controller
 */
class AcceptantViewModelSelector extends AbstractPlugin
{
    /**
     *
     * @var string the Key to inject the name of a viewmodel with in an Accept Header
     */
    const INJECT_VIEWMODEL_NAME = '_internalViewModel';

    /**
     *
     * @var Zend\Mvc\MvcEvent
     */
    protected $event;

    /**
     *
     * @var Zend\Http\Request
     */
    protected $request;

    /**
     * Default array to match against.
     *
     * @var Array
     */
    protected $defaultMatchAgainst;

    /**
     *
     * @var string Default ViewModel
     */
    protected $defaultViewModelName = 'Zend\View\Model\ViewModel';

    /**
     * Detects an appropriate viewmodel for request.
     *
     * Proxies to getViewModel()
     *
     * @return ModelInterface
     */
    public function __invoke(
            array $matchAgainst = null,
            $returnDefault = true,
            & $resultReference = null)
    {
        return $this->getViewModel($matchAgainst, $returnDefault, $resultReference);
    }

    /**
     * Detects an appropriate viewmodel for request.
     *
     * @param array (optional) $matchAgainst The Array to match against
     * @param bool (optional)$returnDefault If no match is availble. Return default instead
     * @param AbstractFieldValuePart|null (optional) $resultReference The object that was matched
     * @return ModelInterface|null
     */
    public function getViewModel(
            array $matchAgainst = null,
            $returnDefault = true,
            & $resultReference = null)
    {
        $name = $this->getViewModelName($matchAgainst, $returnDefault, $resultReference);

        if (!$name) {
            return;
        }

        return new $name();
    }


    /**
     * Detects an appropriate viewmodel name for request.
     *
     * @param array (optional) $matchAgainst The Array to match against
     * @param bool (optional)$returnDefault If no match is availble. Return default instead
     * @param AbstractFieldValuePart|null (optional) $resultReference The object that was matched.
     * @return ModelInterface|null Returns null if $returnDefault = false and no match could be made
     */
    public function getViewModelName(
            array $matchAgainst = null,
            $returnDefault = true,
            & $resultReference = null)
    {
        $res = $this->match($matchAgainst);
        if ($res) {
            $resultReference = $res;
            return $this->extractViewModelName($res);
        }

        if ($returnDefault) {
            return $this->defaultViewModelName;
        }
    }

    /**
     * Detects an appropriate viewmodel name for request.
     *
     * @param array (optional) $matchAgainst The Array to match against
     * @return AbstractFieldValuePart (optional) $resultReference The object that was matched
     */
    public function match(array $matchAgainst = null)
    {
        $request        = $this->getRequest();
        $headers        = $request->getHeaders();

        if ((!$matchAgainst && !$this->defaultMatchString) || !$headers->has('accept')) {
            return null;
        }

        if (!$matchAgainst) {
            $matchAgainst = $this->defaultMatchAgainst;
        }

        $matchAgainstString = '';
        foreach ($matchAgainst as $modelName => $modelStrings) {
            foreach ((array) $modelStrings as $modelString) {
                $matchAgainstString .= $this->injectViewModelName($modelString, $modelName);
            }
        }

        /** @var $accept \Zend\Http\Header\Accept */
        $accept = $headers->get('Accept');
        if (($res = $accept->match($matchAgainstString)) === false) {
            return null;
        }

        return $res;
    }

    /**
     * Inject the viewmodel name into the accept header string
     *
     * @param string $modelAcceptString
     * @param string $modelName
     * @return string
     */
    protected function injectViewModelName($modelAcceptString, $modelName)
    {
        $modelName = str_replace('\\', '|', $modelName);
        return $modelAcceptString . '; ' . self::INJECT_VIEWMODEL_NAME . '="' . $modelName . '", ';
    }

    /**
     * Extract the viewmodel name from a match
     * @param AbstractFieldValuePart $res
     * @return string
     */
    protected function extractViewModelName(AbstractFieldValuePart $res)
    {
        $modelName = $res->getMatchedAgainst()->params[self::INJECT_VIEWMODEL_NAME];
        return str_replace('|', '\\', $modelName);
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
            throw new Exception\DomainException(
                    'The event used does not contain a valid Request, but must.'
            );
        }

        return $this->request = $request;
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
            throw new Exception\DomainException(
                    'A controller that implements InjectApplicationEventInterface '
                  . 'is required to use ' . __CLASS__
            );
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
