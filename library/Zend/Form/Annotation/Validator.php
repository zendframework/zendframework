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
 * Validator annotation
 *
 * Expects a JSON-encoded object/associative array defining the validator. 
 * Typically, this includes the "name" with an associated string value
 * indicating the validator name or class, and optionally an "options" key
 * with an object/associative array value of options to pass to the
 * validator constructor.
 *
 * This annotation may be specified multiple times; validators will be added
 * to the validator chain in the order specified.
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Validator extends AbstractAnnotation
{
    /**
     * @var array
     */
    protected $validator;

    /**
     * Receive and process the contents of an annotation
     * 
     * @param  string $content 
     * @return void
     */
    public function initialize($content)
    {
        $validator = $this->parseJsonContent($content, __METHOD__);
        if (!is_array($validator)) {
            throw new Exception\DomainException(sprintf(
                '%s expects the annotation to define a JSON object or array; received "%s"',
                __METHOD__,
                gettype($validator)
            ));
        }
        $this->validator = $validator;
    }

    /**
     * Retrieve the validator specification
     * 
     * @return null|array
     */
    public function getValidator()
    {
        return $this->validator;
    }
}
