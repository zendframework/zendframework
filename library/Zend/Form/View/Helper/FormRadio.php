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
 */

namespace Zend\Form\View\Helper;

use Zend\Form\ElementInterface;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage View
 */
class FormRadio extends FormMultiCheckbox
{
    /**
     * Return input type
     * 
     * @return string
     */
    protected function getInputType()
    {
        return 'radio';
    }

    /**
     * Get element name
     * 
     * @param  ElementInterface $element 
     * @return string
     */
    protected static function getName(ElementInterface $element)
    {
        return $element->getName();
    }
}
