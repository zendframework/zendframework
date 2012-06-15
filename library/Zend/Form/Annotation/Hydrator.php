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

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Hydrator extends AbstractAnnotation
{
    protected $hydrator;

    /**
     * Receive and process the contents of an annotation
     * 
     * @param  string $content 
     * @return void
     */
    public function initialize($content)
    {
        $hydrator = $content;

        if ('"' === substr($hydrator, 0, 1)) {
            // Look for unescaped NS, and escape them so the parser knows what to do
            $hydrator = preg_replace('#(\\\\)(?!\\\\)#', '$1$1', $hydrator);
            $hydrator = $this->parseJsonContent($hydrator, __METHOD__);
        }

        if (!is_string($hydrator)) {
            throw new Exception\DomainException(sprintf(
                '%s expects the annotation to define a string or a JSON string; received "%s"',
                __METHOD__,
                gettype($hydrator)
            ));
        }
        $this->hydrator = $hydrator;
    }

    /**
     * Retrieve the hydrator class
     * 
     * @return null|string
     */
    public function getHydrator()
    {
        return $this->hydrator;
    }
}
