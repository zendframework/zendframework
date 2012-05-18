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

use Zend\Form\ElementInterface;
use Zend\Form\Exception;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FormInput extends AbstractHelper
{
    /**
     * Attributes valid for the input tag
     *
     * @var array
     */
    protected $validTagAttributes = array(
        'name'           => true,
        'accept'         => true,
        'alt'            => true,
        'autocomplete'   => true,
        'autofocus'      => true,
        'checked'        => true,
        'dirname'        => true,
        'disabled'       => true,
        'form'           => true,
        'formaction'     => true,
        'formenctype'    => true,
        'formmethod'     => true,
        'formnovalidate' => true,
        'formtarget'     => true,
        'height'         => true,
        'list'           => true,
        'max'            => true,
        'maxlength'      => true,
        'min'            => true,
        'multiple'       => true,
        'pattern'        => true,
        'placeholder'    => true,
        'readonly'       => true,
        'required'       => true,
        'size'           => true,
        'src'            => true,
        'step'           => true,
        'type'           => true,
        'value'          => true,
        'width'          => true,
    );

    /**
     * Valid values for the input type
     * 
     * @var array
     */
    protected $validTypes = array(
        'text'           => true,
        'button'         => true,
        'checkbox'       => true,
        'file'           => true,
        'hidden'         => true,
        'image'          => true,
        'password'       => true,
        'radio'          => true,
        'reset'          => true,
        'select'         => true,
        'submit'         => true,
        'color'          => true,
        'date'           => true,
        'datetime'       => true,
        'datetime-local' => true,
        'email'          => true,
        'month'          => true,
        'number'         => true,
        'range'          => true,
        'search'         => true,
        'tel'            => true,
        'time'           => true,
        'url'            => true,
        'week'           => true,
    );

    /**
     * Render a form <input> element from the provided $element
     * 
     * @param  ElementInterface $element 
     * @return string
     */
    public function render(ElementInterface $element)
    {
        $name   = $element->getName();
        if (empty($name)) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ));
        }

        $attributes         = $element->getAttributes();
        $attributes['name'] = $name;
        $attributes['type'] = $this->getType($element);

        return sprintf(
            '<input %s%s', 
            $this->createAttributesString($attributes), 
            $this->getInlineClosingBracket()
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
        return $this->render($element);
    }

    /**
     * Determine input type to use
     * 
     * @param  ElementInterface $element 
     * @return string
     */
    protected function getType(ElementInterface $element)
    {
        $type = $element->getAttribute('type');
        if (empty($type)) {
            return 'text';
        }

        $type = strtolower($type);
        if (!isset($this->validTypes[$type])) {
            return 'text';
        }

        return $type;
    }
}
