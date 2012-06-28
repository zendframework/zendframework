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
 * @package    Zend_Code
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Code\Annotation;

use Doctrine\Common\Annotations\DocParser;
use Zend\EventManager\EventInterface;

/**
 * A parser for docblock annotations that utilizes the annotation parser from 
 * Doctrine\Common.
 *
 * Consumes Doctrine\Common\Annotations\DocParser, and responds to events from 
 * AnnotationManager. If the annotation examined is in the list of classes we
 * are interested in, the raw annotation is passed to the DocParser in order to
 * retrieve the annotation object instance. Otherwise, it is skipped.
 *
 * @package    Zend_Code
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DoctrineAnnotationParser
{
    /**
     * @var array Annotation classes we support on this iteration
     */
    protected $allowedAnnotations = array();

    /**
     * @var DocParser
     */
    protected $docParser;

    /**
     * Set the DocParser instance
     *
     * @param  DocParser $docParser
     * @return AnnotationParser
     */
    public function setDocParser(DocParser $docParser)
    {
        $this->docParser = $docParser;
        return $this;
    }

    /**
     * Retrieve the DocParser instance
     *
     * If none is registered, lazy-loads a new instance.
     * 
     * @return DocParser
     */
    public function getDocParser()
    {
        if (!$this->docParser instanceof DocParser) {
            $this->setDocParser(new DocParser());
        }
        return $this->docParser;
    }

    /**
     * Handle annotation creation
     * 
     * @param  EventInterface $e 
     * @return false|\stdClass
     */
    public function onCreateAnnotation(EventInterface $e)
    {
        $annotationClass = $e->getParam('class', false);
        if (!$annotationClass) {
            return false;
        }

        if (!isset($this->allowedAnnotations[$annotationClass])) {
            return false;
        }

        $annotationString = $e->getParam('raw', false);
        if (!$annotationString) {
            return false;
        }

        $parser      = $this->getDocParser();
        $annotations = $parser->parse($annotationString);
        if (empty($annotations)) {
            return false;
        }

        $annotation = array_shift($annotations);
        if (!is_object($annotation)) {
            return false;
        }

        return $annotation;
    }

    /**
     * Specify an allowed annotation class
     * 
     * @param  string $class 
     * @return AnnotationParser
     */
    public function allowAnnotation($class)
    {
        $this->allowedAnnotations[$class] = true;
        return $this;
    }

    /**
     * Set many allowed annotations at once
     * 
     * @param  array $classes Array of annotation class names
     * @return AnnotationParser
     */
    public function allowAnnotations(array $classes)
    {
        foreach ($classes as $class) {
            $this->allowedAnnotations[$class] = true;
        }
        return $this;
    }
}
