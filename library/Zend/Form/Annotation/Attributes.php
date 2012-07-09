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
 */

namespace Zend\Form\Annotation;

/**
 * Attributes annotation
 *
 * Expects an array of attributes. The value is used to set any attributes on 
 * the related form object (element, fieldset, or form).
 *
 * @Annotation
 * @package    Zend_Form
 * @subpackage Annotation
 */
class Attributes extends AbstractArrayAnnotation
{
    /**
     * Retrieve the attributes
     * 
     * @return null|array
     */
    public function getAttributes()
    {
        return $this->value;
    }
}
