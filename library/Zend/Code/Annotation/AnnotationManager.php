<?php

namespace Zend\Code\Annotation;

use Zend\Code\Exception;

class AnnotationManager
{

    /**
     * @var string[]
     */
    protected $annotationNames = array();

    /**
     * @var Annotation[]
     */
    protected $annotations = array();

    public function __construct(array $annotations = array())
    {
        if ($annotations) {
            foreach ($annotations as $annotation) {
                $this->registerAnnotation($annotation);
            }
        }
    }

    public function registerAnnotation(Annotation $annotation)
    {
        $class = get_class($annotation);

        if (in_array($class, $this->annotationNames)) {
            throw new Exception\InvalidArgumentException('An annotation for this class ' . $class . ' already exists');
        }

        $this->annotations[] = $annotation;
        $this->annotationNames[] = $class;
    }

    public function hasAnnotation($class)
    {
        // otherwise, only if its name exists as a key
        return in_array($class, $this->annotationNames);
    }

    public function createAnnotation($class, $content = null)
    {
        if (!$this->hasAnnotation($class)) {
            throw new Exception\RuntimeException('This annotation class is not supported by this annotation manager');
        }

        $index = array_search($class, $this->annotationNames);
        $annotation = $this->annotations[$index];

        $newAnnotation = clone $annotation;
        if ($content) {
            $newAnnotation->initialize($content);
        }
        return $newAnnotation;
    }
}