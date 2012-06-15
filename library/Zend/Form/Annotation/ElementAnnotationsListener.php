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
 * @package    Zend_Form
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Form\Annotation;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ElementAnnotationsListener implements ListenerAggregateInterface
{
    protected $listeners = array();

    /**
     * Attach listeners
     * 
     * @param  EventManagerInterface $events 
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('configureElement', array($this, 'handleAllowEmptyAnnotation'));
        $this->listeners[] = $events->attach('configureElement', array($this, 'handleAttributesAnnotation'));
        $this->listeners[] = $events->attach('configureElement', array($this, 'handleErrorMessageAnnotation'));
        $this->listeners[] = $events->attach('configureElement', array($this, 'handleFilterAnnotation'));
        $this->listeners[] = $events->attach('configureElement', array($this, 'handleFlagsAnnotation'));
        $this->listeners[] = $events->attach('configureElement', array($this, 'handleInputAnnotation'));
        $this->listeners[] = $events->attach('configureElement', array($this, 'handleRequiredAnnotation'));
        $this->listeners[] = $events->attach('configureElement', array($this, 'handleValidatorAnnotation'));
    }

    /**
     * Detach listeners
     * 
     * @param  EventManagerInterface $events 
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if (false !== $events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function handleAllowEmptyAnnotation($e)
    {
        $annotation = $e->getParam('annotation');
        if (!$annotation instanceof AllowEmpty) {
            return;
        }

        $inputSpec = $e->getParam('inputSpec');
        $inputSpec['allow_empty'] = true;
    }

    public function handleAttributesAnnotation($e)
    {
        $annotation = $e->getParam('annotation');
        if (!$annotation instanceof Attributes) {
            return;
        }

        $elementSpec = $e->getParam('elementSpec');
        $elementSpec['spec']['attributes'] = $annotation->getAttributes();
    }

    public function handleErrorMessageAnnotation($e)
    {
        $annotation = $e->getParam('annotation');
        if (!$annotation instanceof ErrorMessage) {
            return;
        }

        $inputSpec = $e->getParam('inputSpec');
        $inputSpec['error_message'] = $annotation->getMessage();
    }

    public function handleFilterAnnotation($e)
    {
        $annotation = $e->getParam('annotation');
        if (!$annotation instanceof Filter) {
            return;
        }

        $inputSpec = $e->getParam('inputSpec');
        if (!isset($inputSpec['filters'])) {
            $inputSpec['filters'] = array();
        }
        $inputSpec['filters'][] = $annotation->getFilter();
    }

    public function handleFlagsAnnotation($e)
    {
        $annotation = $e->getParam('annotation');
        if (!$annotation instanceof Flags) {
            return;
        }

        $elementSpec = $e->getParam('elementSpec');
        $elementSpec['flags'] = $annotation->getFlags();
    }

    public function handleInputAnnotation($e)
    {
        $annotation = $e->getParam('annotation');
        if (!$annotation instanceof Input) {
            return;
        }

        $name       = $e->getParam('name');
        $filterSpec = $e->getParam('filterSpec');
        $filterSpec[$name] = $annotation->getInput();
    }

    public function handleRequiredAnnotation($e)
    {
        $annotation = $e->getParam('annotation');
        if (!$annotation instanceof Required) {
            return;
        }

        $inputSpec = $e->getParam('inputSpec');
        $inputSpec['required'] = (bool) $annotation->getRequired();
    }

    public function handleValidatorAnnotation($e)
    {
        $annotation = $e->getParam('annotation');
        if (!$annotation instanceof Validator) {
            return;
        }

        $inputSpec = $e->getParam('inputSpec');
        if (!isset($inputSpec['validators'])) {
            $inputSpec['validators'] = array();
        }
        $inputSpec['validators'][] = $annotation->getValidator();
    }
}
