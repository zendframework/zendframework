<?php

namespace Zend\Code\Annotation;

interface Annotation
{
    public function getName();
    public function createAnnotation($annotationContent);
}