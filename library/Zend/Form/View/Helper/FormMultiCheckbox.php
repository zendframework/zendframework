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
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Form\View\Helper;

use Traversable;
use Zend\Form\ElementInterface;
use Zend\Form\Exception;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FormMultiCheckbox extends FormInput
{
    const LABEL_APPEND  = 'append';
    const LABEL_PREPEND = 'prepend';

    protected $inputHelper;
    protected $labelHelper;
    protected $labelPosition = self::LABEL_APPEND;
    protected $separator = '';

    /**
     * Set value for labelPosition
     *
     * @param  mixed labelPosition
     * @return $this
     */
    public function setLabelPosition($labelPosition)
    {
        $labelPosition = strtolower($labelPosition);
        if (!in_array($labelPosition, array(self::LABEL_APPEND, self::LABEL_PREPEND))) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects either %s::LABEL_APPEND or %s::LABEL_PREPEND; received "%s"',
                __METHOD__,
                __CLASS__,
                __CLASS__,
                (string) $labelPosition
            ));
        }
        $this->labelPosition = $labelPosition;
        return $this;
    }
    
    /**
     * Get position of label
     *
     * @return string
     */
    public function getLabelPosition()
    {
        return $this->labelPosition;
    }

    /**
     * Set separator string for checkbox elements
     *
     * @param  string $separator
     * @return FormMultiCheckbox
     */
    public function setSeparator($separator)
    {
        $this->separator = (string) $separator;
        return $this;
    }
    
    /**
     * Get separator for checkbox elements
     *
     * @return string
     */
    public function getSeparator()
    {
        return $this->separator;
    }

    /**
     * Render a form <input> element from the provided $element
     * 
     * @param  ElementInterface $element 
     * @return string
     */
    public function render(ElementInterface $element)
    {
        $name   = static::getName($element);
        if (empty($name)) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ));
        }

        $attributes         = $element->getAttributes();

        if (!isset($attributes['options']) 
            || (!is_array($attributes['options']) && !$attributes['options'] instanceof Traversable)
        ) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an array or Traversable "options" attribute; none found',
                __METHOD__
            ));
        }

        $options = $attributes['options'];
        unset($attributes['options']);

        $attributes['name'] = $name;
        $attributes['type'] = $this->getInputType();

        $values = array();
        if (isset($attributes['value'])) {
            $values = (array) $attributes['value'];
            unset($attributes['value']);
        }

        $inputHelper    = $this->getInputHelper();
        $escapeHelper   = $this->getEscapeHelper();
        $labelHelper    = $this->getLabelHelper();
        $labelOpen      = $labelHelper->openTag();
        $labelClose     = $labelHelper->closeTag();
        $labelPosition  = $this->getLabelPosition();
        $closingBracket = $this->getInlineClosingBracket();
        $template       = $labelOpen . '%s%s' . $labelClose;
        $combinedMarkup = array();
        $count          = 0;

        foreach ($options as $label => $value) {
            $count++;
            if ($count > 1 && array_key_exists('id', $attributes)) {
                unset($attributes['id']);
            }
            $attributes['value']   = $value;
            $attributes['checked'] = '';
            if (in_array($value, $values, true)) {
                $attributes['checked'] = 'checked';
            }

            $label = $escapeHelper($label);
            $input = sprintf(
                '<input %s%s', 
                $this->createAttributesString($attributes), 
                $closingBracket
            );

            switch ($labelPosition) {
                case self::LABEL_PREPEND:
                    $markup = sprintf($template, $label, $input);
                    break;
                case self::LABEL_APPEND:
                default:
                    $markup = sprintf($template, $input, $label);
                    break;
            }

            $combinedMarkup[] = $markup;
        }

        return implode($this->getSeparator(), $combinedMarkup);
    }

    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     * 
     * @param  ElementInterface $element 
     * @return string
     */
    public function __invoke(ElementInterface $element)
    {
        return $this->render($element);
    }

    /**
     * Return input type
     * 
     * @return string
     */
    protected function getInputType()
    {
        return 'checkbox';
    }

    /**
     * Retrieve the FormInput helper
     * 
     * @return FormInput
     */
    protected function getInputHelper()
    {
        if ($this->inputHelper) {
            return $this->inputHelper;
        }

        if ($this->view instanceof Pluggable) {
            $this->inputHelper = $this->view->plugin('form_input');
        }

        if (!$this->inputHelper instanceof FormInput) {
            $this->inputHelper = new FormInput();
        }

        return $this->inputHelper;
    }

    /**
     * Retrieve the FormLabel helper
     * 
     * @return FormLabel
     */
    protected function getLabelHelper()
    {
        if ($this->labelHelper) {
            return $this->labelHelper;
        }

        if ($this->view instanceof Pluggable) {
            $this->labelHelper = $this->view->plugin('form_label');
        }

        if (!$this->labelHelper instanceof FormLabel) {
            $this->labelHelper = new FormLabel();
        }

        return $this->labelHelper;
    }

    /**
     * Get element name
     * 
     * @param  ElementInterface $element 
     * @return string
     */
    protected static function getName(ElementInterface $element)
    {
        return $element->getName() . '[]';
    }
}
