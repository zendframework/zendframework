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
 * Flags annotation
 *
 * Allows passing flags to the form factory. These flags are used to indicate 
 * metadata, and typically the priority (order) in which an element will be 
 * included.
 *
 * The value should be an associative array.
 *
 * @Annotation
 * @package    Zend_Form
 * @subpackage Annotation
 */
class Flags extends AbstractArrayAnnotation
{
    /**
     * Retrieve the flags
     * 
     * @return null|array
     */
    public function getFlags()
    {
        return $this->value;
    }
}
