<?php
namespace ZendTest\Form\TestAsset\Annotation;

use Zend\Form\Annotation;

/**
 * @Annotation\Name("some_name")
 * @Annotation\Attributes({"legend":"Some Fieldset"})
 * @Annotation\InputFilter("ZendTest\Form\TestAsset\Annotation\InputFilter")
 */
class ClassEntity
{
    /**
     * @Annotation\Exclude()
     */
    public $omit;

    /**
     * @Annotation\Name("keeper")
     * @Annotation\Attributes({"type":"text"})
     */
    public $keep;
}
