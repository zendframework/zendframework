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
 * Input annotation
 *
 * Use this annotation to specify a specific input class to use with an element.
 * The value should be a string indicating the fully qualified class name of the
 * input to use.
 *
 * @Annotation
 * @package    Zend_Form
 * @subpackage Annotation
 */
class Input extends AbstractStringAnnotation
{
    /**
     * Retrieve the input class
     * 
     * @return null|string
     */
    public function getInput()
    {
        return $this->value;
    }
}
