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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Form;

use ArrayAccess;
use Traversable;
use Zend\Stdlib\ArrayUtils;

/**
 * @category   Zend
 * @package    Zend_Form
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Factory
{
    /**
     * Create an element based on the provided specification
     *
     * Specification can contain any of the following:
     * - type: the Element class to use; defaults to \Zend\Form\Element
     * - name: what name to provide the element, if any
     * - attributes: an array, Traversable, or ArrayAccess object of element 
     *   attributes to assign
     * 
     * @param  array|Traversable|ArrayAccess $spec 
     * @return ElementInterface
     * @throws Exception\InvalidArgumentException for an invalid $spec
     * @throws Exception\DomainException for an invalid element type
     */
    public function createElement($spec)
    {
        if ($spec instanceof Traversable) {
            $spec = ArrayUtils::iteratorToArray($spec);
        }
        if (!is_array($spec) && !$spec instanceof ArrayAccess) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array, or object implementing Traversable or ArrayAccess; received "%s"',
                __METHOD__,
                (is_object($spec) ? get_class($spec) : gettype($spec))
            ));
        }

        $type       = isset($spec['type'])       ? $spec['type']       : 'Zend\Form\Element';
        $name       = isset($spec['name'])       ? $spec['name']       : null;
        $attributes = isset($spec['attributes']) ? $spec['attributes'] : null;

        $element = new $type();
        if (!$element instanceof ElementInterface) {
            throw new Exception\DomainException(sprintf(
                '%s expects an element type that implements Zend\Form\ElementInterface; received "%s"',
                __METHOD__,
                $type
            ));
        }

        if ($name) {
            $element->setName($name);
        }

        if (is_array($attributes) || $attributes instanceof Traversable || $attributes instanceof ArrayAccess) {
            $element->setAttributes($attributes);
        }

        return $element;
    }
}
