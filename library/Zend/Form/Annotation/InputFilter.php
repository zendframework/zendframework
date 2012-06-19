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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Form\Annotation;

use Zend\Form\Exception;

/**
 * InputFilter annotation
 *
 * Use this annotation to specify a specific input filter class to use with the 
 * form. The value should be a bare string or a JSON-encoded string indicating 
 * the fully qualified class name of the input filter to use.
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class InputFilter extends AbstractAnnotation
{
    /**
     * @var string
     */
    protected $inputFilter;

    /**
     * Receive and process the contents of an annotation
     * 
     * @param  string $content 
     * @return void
     */
    public function initialize($content)
    {
        $inputFilter = $content;

        if ('"' === substr($inputFilter, 0, 1)) {
            // Look for unescaped NS, and escape them so the parser knows what to do
            $inputFilter = preg_replace('#(\\\\)(?!\\\\)#', '$1$1', $inputFilter);
            $inputFilter = $this->parseJsonContent($inputFilter, __METHOD__);
        }

        if (!is_string($inputFilter)) {
            throw new Exception\DomainException(sprintf(
                '%s expects the annotation to define a string or a JSON string; received "%s"',
                __METHOD__,
                gettype($inputFilter)
            ));
        }
        $this->inputFilter = $inputFilter;
    }

    /**
     * Retrieve the input filter class
     * 
     * @return null|string
     */
    public function getInputFilter()
    {
        return $this->inputFilter;
    }
}

