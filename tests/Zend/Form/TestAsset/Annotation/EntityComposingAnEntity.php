<?php
namespace ZendTest\Form\TestAsset\Annotation;

use Zend\Form\Annotation;

/**
 * @Annotation\Name("hierarchical")
 */
class EntityComposingAnEntity
{
    /**
     * @Annotation\Name("composed")
     * @Annotation\ComposedObject("ZendTest\Form\TestAsset\Annotation\Entity")
     */
    public $child;
}
