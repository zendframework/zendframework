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

use Zend\Code\Exception;

/**
 * @category   Zend
 * @package    Zend_Code
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class AnnotationManager
{
    /**
     * @var string[]
     */
    protected $annotationNames = array();

    /**
     * @var AnnotationInterface[]
     */
    protected $annotations = array();

    /**
     * Constructor
     *
     * @param array $annotations
     */
    public function __construct(array $annotations = array())
    {
        if ($annotations) {
            foreach ($annotations as $annotation) {
                $this->registerAnnotation($annotation);
            }
        }
    }

    /**
     * Register annotations
     *
     * @param  AnnotationInterface $annotation
     * @throws Exception\InvalidArgumentException
     */
    public function registerAnnotation(AnnotationInterface $annotation)
    {
        $class = get_class($annotation);

        if (in_array($class, $this->annotationNames)) {
            throw new Exception\InvalidArgumentException('An annotation for this class ' . $class . ' already exists');
        }

        $this->annotations[]     = $annotation;
        $this->annotationNames[] = $class;
    }

    /**
     * Checks if the manager has annotations for a class
     *
     * @param $class
     * @return bool
     */
    public function hasAnnotation($class)
    {
        // Only if its name exists as a key
        return in_array($class, $this->annotationNames);
    }

    /**
     * Create Annotation
     *
     * @param  string $class
     * @param  null|string $content
     * @throws Exception\RuntimeException
     * @return AnnotationInterface
     */
    public function createAnnotation($class, $content = null)
    {
        if (!$this->hasAnnotation($class)) {
            throw new Exception\RuntimeException('This annotation class is not supported by this annotation manager');
        }

        $index      = array_search($class, $this->annotationNames);
        $annotation = $this->annotations[$index];

        $newAnnotation = clone $annotation;
        if ($content) {
            $newAnnotation->initialize($content);
        }
        return $newAnnotation;
    }
}
