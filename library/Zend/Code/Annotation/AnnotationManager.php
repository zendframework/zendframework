<?php

namespace Zend\Code\Annotation;

class AnnotationManager
{
    /**
     * @var bool
     */
    protected $useGenericAnnotation = false;

    /**
     * @var array
     */
    protected $annotationNames = array();

    /**
     * @var Annotation[]
     */
    protected $annotations = array();

    public function __construct($useGenericAnnotation = false)
    {
        $this->useGenericAnnotation = (bool) $useGenericAnnotation;
    }

    public function registerAnnotation(Annotation $annotation)
    {
        $name  = strtolower($annotation->getName());
        $class = get_class($annotation);

        if (array_key_exists($name, $this->annotationNames)) {
            throw new InvalidArgumentException('An annotation for this name ' . $name . ' already exists');
        }

        if (array_key_exists($class, $this->annotationNames)) {
            throw new InvalidArgumentException('An annotation for this class ' . $class . ' already exists');
        }

        $this->annotations[] = $annotation;
        end($this->annotations);
        $key = key($this->annotations);

        $this->annotationNames[$name] = $key;
        $this->annotationNames[$class] = $key;
    }

    public function hasAnnotationName($name)
    {
        if (strpos($name, '\\') !== false) {
            // check for FQ class name first
            if (array_key_exists($name, $this->annotationNames)) {
                return true;
            }
            return (class_exists($name));
        }

        // if generic and its a short name, true
        if ($this->useGenericAnnotation) {
            return true;
        }

        // otherwise, only if its name exists as a key
        return array_key_exists($name, $this->annotationNames);
    }

    public function createAnnotation($name, $content)
    {
        if (!$this->hasAnnotationName($name) && $this->useGenericAnnotation === false) {
            throw new RuntimeException('This annotation name is not supported by this manager');
        }

        if (strpos($name, '\\') !== false && !array_key_exists($name, $this->annotationNames)) {
            $annotation = new $name;
            if (!$annotation instanceof Annotation) {
                throw new RuntimeException('The dynamically loaded annotation ' . $name . ' does not implement the annotation interface');
            }
        } else {
            $key = $this->annotationNames[$name];
            $annotation = $this->annotations[$key];
        }

        $newAnnotation = clone $annotation;
        $newAnnotation->createAnnotation($content);
        return $newAnnotation;
    }
}