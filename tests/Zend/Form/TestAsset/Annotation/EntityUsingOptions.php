<?php
namespace ZendTest\Form\TestAsset\Annotation;

use Zend\Form\Annotation;

/**
 * @Annotation\Options({"use_as_base_fieldset":true})
 */
class EntityUsingOptions
{
    /**
      * @Annotation\Options({"label":"Username:", "label_attributes": {"class": "label"}})
      */
    public $username;
}
