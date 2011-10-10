<?php

namespace Zend\Di\Definition;

use Zend\Code\Annotation\AnnotationManager;

class IntrospectionStrategy
{
    /**
     * @var bool
     */
    protected $useAnnotations = false;

    /**
     * @var string[]
     */
    protected $methodNameInclusionPatterns = array('/^set[A-Z]{1}\w*/');

    /**
     * @var string[]
     */
    protected $interfaceInjectionInclusionPatterns = array('/\w*Aware\w*/');

    /**
     * @var AnnotationManager
     */
    protected $annotationManager = null;

    public function __construct(AnnotationManager $annotationManager = null)
    {
        $this->annotationManager = ($annotationManager) ?: $this->createDefaultAnnotationManager();
    }

    public function getAnnotationManager()
    {
        return $this->annotationManager;
    }

    public function createDefaultAnnotationManager()
    {
        $annotationManager = new AnnotationManager;
        $annotationManager->registerAnnotation(new Annotation\Inject());
        return $annotationManager;
    }


    
    public function setUseAnnotations($useAnnotations)
    {
        $this->useAnnotations = $useAnnotations;
    }

    public function getUseAnnotations()
    {
        return $this->useAnnotations;
    }


    public function setMethodNameInclusionPatterns($methodNameInclusionPatterns)
    {
        $this->methodNameInclusionPatterns = $methodNameInclusionPatterns;
    }

    public function getMethodNameInclusionPatterns()
    {
        return $this->methodNameInclusionPatterns;
    }


    public function setInterfaceInjectionInclusionPatterns($interfaceInjectionInclusionPatterns)
    {
        $this->interfaceInjectionInclusionPatterns = $interfaceInjectionInclusionPatterns;
    }

    public function getInterfaceInjectionInclusionPatterns()
    {
        return $this->interfaceInjectionInclusionPatterns;
    }

}
