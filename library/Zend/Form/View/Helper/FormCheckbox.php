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

        // default checked/unchecked values
        $attributes = array_merge(
            array(
                'options' => array(1, 0),
            ),
            $element->getAttributes()
        );

        if (!is_array($attributes['options']) && !$attributes['options'] instanceof Traversable) { 
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an array or Traversable "options" attribute.',
                __METHOD__
            )); 
        }

        if (count($attributes['options']) != 2) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has two options specified: the checked and unchecked values',
                __METHOD__
            )); 
        }

        $options = $attributes['options'];
        unset($attributes['options']);

        $attributes['name']    = $name;
        $attributes['checked'] = '';
        $attributes['type']    = $this->getInputType();
        $closingBracket        = $this->getInlineClosingBracket();

        list($checkedValue, $uncheckedValue) = $options;
        if (isset($attributes['value']) && $attributes['value'] == $checkedValue) {
            $attributes['checked'] = 'checked';
        }
        $attributes['value'] = $checkedValue;

        $hiddenAttributes = array(
            'name'  => $attributes['name'],
            'value' => $uncheckedValue,
        );

        return sprintf(
            '<input type="hidden" %s%s<input %s%s', 
            $this->createAttributesString($hiddenAttributes),
            $closingBracket,
            $this->createAttributesString($attributes), 
            $closingBracket
        );
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
