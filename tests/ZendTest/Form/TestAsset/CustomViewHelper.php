<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace ZendTest\Form\TestAsset;

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\AbstractHelper;
use Zend\Form\View\Helper\FormElement;

class CustomViewHelper extends AbstractHelper
{
    /**
     * @var FormElement
     */
    protected $elementHelper;

    public function __invoke(ElementInterface $element)
    {
        $elementHelper = $this->getElementHelper();

        $name = preg_replace('/[^a-z0-9_-]+/', '', $element->getName());

        $result = '<div id="custom' . $name . '">' . $elementHelper($element) . '</div>';

        return $result;
    }

    /**
     * Retrieve the FormElement helper
     *
     * @return FormElement
     */
    protected function getElementHelper()
    {
        if ($this->elementHelper) {
            return $this->elementHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->elementHelper = $this->view->plugin('form_element');
        }

        if (!$this->elementHelper instanceof FormElement) {
            $this->elementHelper = new FormElement();
        }

        return $this->elementHelper;
    }
}
