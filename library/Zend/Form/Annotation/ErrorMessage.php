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
 * ErrorMessage annotation
 *
 * Allows providing an error message to seed the Input specification for a 
 * given element. The content should be a string.
 *
 * @Annotation
 * @package    Zend_Form
 * @subpackage Annotation
 */
class ErrorMessage extends AbstractStringAnnotation
{
    /**
     * Retrieve the message
     * 
     * @return null|string
     */
    public function getMessage()
    {
        return $this->value;
    }
}
