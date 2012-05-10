<?php

namespace Zend\Code\Annotation;

use ArrayObject;

class AnnotationCollection extends ArrayObject
{
    /**
     * Checks if the collection has annotations for a class
     *
     * @param $class
     * @return bool
     */
    public function hasAnnotation($class)
    {
        foreach ($this as $annotation) {
            if (get_class($annotation) == $class) {
                return true;
            }
        }
        return false;
    }
}
