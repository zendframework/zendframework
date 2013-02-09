<?php
/**
* Zend Framework (http://framework.zend.com/)
*
* @link http://github.com/zendframework/zf2 for the canonical source repository
* @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
* @license http://framework.zend.com/license/new-bsd New BSD License
* @package Zend_Form
*/

namespace ZendTest\Form\TestAsset\Annotation;

use Zend\Form\Annotation;

/**
* @Annotation\Options({"use_as_base_fieldset":true})
*/
class EntityUsingObjectProperty
{
    /**
* @Annotation\Object("ZendTest\Form\TestAsset\Annotation\Entity")
* @Annotation\Type("Zend\Form\Fieldset")
* @Annotation\Hydrator({"type":"Zend\Stdlib\Hydrator\ClassMethods", "options": {"underscoreSeparatedKeys": false}})
*/
    public $object;
}
