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

class Entity
{
    /**
      * @Annotation\ErrorMessage("Invalid or missing username")
      * @Annotation\Required(true)
      * @Annotation\Filter({"name":"StringTrim"})
      * @Annotation\Validator({"name":"NotEmpty"})
      * @Annotation\Validator({"name":"StringLength","options":{"min":3,"max":25}})
      */
    public $username;

    /**
      * @Annotation\Filter({"name":"StringTrim"})
      * @Annotation\Validator({"name":"EmailAddress"})
      * @Annotation\Attributes({"type":"password","label":"Enter your password"})
      */
    public $password;
}
