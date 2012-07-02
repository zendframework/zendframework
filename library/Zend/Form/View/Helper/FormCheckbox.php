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
class FormCheckbox extends FormInput
{
    /**
     * @var boolean
     */
    protected $useHiddenElement = true;

    /**
     * @var array
     */
    protected $defaultStateValues = array(
        'checkedValue'   => '1',
        'uncheckedValue' => '0',
    );

    /**
     * Returns the option for prefixing the element with a hidden element
     * for the unset value.
     *
     * @return boolean
     */
    public function getUseHiddenElement()
    {
        return $this->useHiddenElement;
    }

    /**
     * Sets the option for prefixing the element with a hidden element
     * for the unset value.
     *
     * @param  boolean $useHiddenElement
     * @return FormCheckbox
     */
    public function setUseHiddenElement($useHiddenElement)
    {
        $this->useHiddenElement = (bool) $useHiddenElement;
        return $this;
    }

    /**
     * Render a form <input> element from the provided $element
     *
     * @param  ElementInterface $element
     * @return string
     */
    public function render(ElementInterface $element)
    {
        $name = $element->getName();
        if (empty($name)) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ));
        }

        $attributes = $element->getAttributes();
        if (empty($attributes['options'])) {
            $attributes['options'] = array();
        }
        $options = $attributes['options'];
        unset($attributes['options']);

        // default checked/unchecked values
        foreach ($this->defaultStateValues as $key => $value) {
            if (empty($options[$key])) {
                $options[$key] = $value;
            }
        }

        if (!is_array($options) && !$options instanceof Traversable) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an array or Traversable "options" attribute.',
                __METHOD__
            ));
        }

        $attributes['name']    = $name;
        $attributes['checked'] = '';
        $attributes['type']    = $this->getInputType();
        $closingBracket        = $this->getInlineClosingBracket();

        if (isset($attributes['value']) && $attributes['value'] == $options['checkedValue']) {
            $attributes['checked'] = 'checked';
        }
        $attributes['value'] = $options['checkedValue'];

        $rendered = sprintf(
            '<input %s%s',
            $this->createAttributesString($attributes),
            $closingBracket
        );

        $useHiddenElement = isset($attributes['useHiddenElement'])
            ? (bool) $attributes['useHiddenElement']
            : $this->useHiddenElement;

        if ($useHiddenElement) {
            $hiddenAttributes = array(
                'name'  => $attributes['name'],
                'value' => $options['uncheckedValue'],
            );

            $rendered = sprintf(
                '<input type="hidden" %s%s',
                $this->createAttributesString($hiddenAttributes),
                $closingBracket
            ) . $rendered;
        }

        return $rendered;
    }

    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     *
     * @param  ElementInterface $element
     * @return string|FormCheckbox
     */
    public function __invoke(ElementInterface $element = null)
    {
        if (!$element) {
            return $this;
        }

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
}
