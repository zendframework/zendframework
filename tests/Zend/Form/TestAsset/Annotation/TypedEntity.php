<?php
namespace ZendTest\Form\TestAsset\Annotation;

use Zend\Form\Annotation;

/**
 * @Annotation\Type("ZendTest\Form\TestAsset\Annotation\Form")
 */
class TypedEntity
{
    /**
     * @Annotation\Type("ZendTest\Form\TestAsset\Annotation\Element")
     * @Annotation\Name("typed_element")
     */
    public $typedElement;
}
