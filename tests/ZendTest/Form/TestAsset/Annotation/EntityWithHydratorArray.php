<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace ZendTest\Form\TestAsset\Annotation;

use Zend\Form\Annotation;

/**
 * @Annotation\Name("user")
 * @Annotation\Attributes({"legend":"Register"})
 * @Annotation\Hydrator({"type":"Zend\Stdlib\Hydrator\ClassMethods", "options": {"underscoreSeparatedKeys": false}})
 */
class EntityWithHydratorArray
{
    /**
     * @Annotation\Options({"label":"Username:", "label_attributes": {"class": "label"}})
     */
    public $username;
}
