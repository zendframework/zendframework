<?php

namespace Zend\Code\Annotation;

use ArrayObject;

class GenericAnnotation extends ArrayObject implements Annotation
{

    /**
     * @var string
     */
    protected $name = null;

    /**
     * @return GenericAnnotation
     */
    public function __construct($name = null)
    {
        if ($name) {
            $this->setName($name);
        }
        parent::__construct(null, ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * @return null
     */
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function createAnnotation($annotationContent)
    {
        foreach (explode(',', $annotationContent) as $nvPair) {
            list($name, $value) = preg_split('=', $nvPair, 2);
            $this->offsetSet($name, trim($value, '"\' '));
        }
    }
    
}
 
