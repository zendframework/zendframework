<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace Zend\Form\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\Exception;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage View
 */
class FormFile extends FormInput
{
    /**
     * Attributes valid for the input tag type="file"
     *
     * @var array
     */
    protected $validTagAttributes = array(
        'name'           => true,
        'accept'         => true,
        'autofocus'      => true,
        'disabled'       => true,
        'form'           => true,
        'multiple'       => true,
        'required'       => true,
        'type'           => true,
        'value'          => true,
    );

    /**
     * Determine input type to use
     *
     * @param  ElementInterface $element
     * @return string
     */
    protected function getType(ElementInterface $element)
    {
        return 'file';
    }

    /**
     * Render a form <input> element from the provided $element
     *
     * @param  ElementInterface $element
     * @throws Exception\DomainException
     * @return string
     */
    public function render(ElementInterface $element)
    {
        $value = $element->getValue();
        if (is_array($value) && isset($value['name'])) {
            $element->setValue($value['name']);
        }
        return parent::render($element);
    }
}
