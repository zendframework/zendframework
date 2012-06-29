<?php
namespace ZendTest\Form\TestAsset\Annotation;

use Zend\Form\Annotation;

/**
 * @Annotation\Name("extended")
 */
class ExtendedEntity extends Entity
{
    /**
      * @Annotation\Filter({"name":"StringTrim"})
      * @Annotation\Validator({"name":"EmailAddress"})
      * @Annotation\Attributes({"type":"password","label":"Enter your email"})
      * @Annotation\Flags({"priority":-1})
      */
    public $email;
}
