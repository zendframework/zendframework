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
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 */
class ComplexEntity
{
    /**
     * @Annotation\ErrorMessage("Invalid or missing username")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"NotEmpty"})
     * @Annotation\Validator({"name":"StringLength","options":{"min":3,"max":25}})
     */
    public $username;

    /**
     * @Annotation\Attributes({"type":"password","label":"Enter your password"})
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength","options":{"min":3}})
     */
    public $password;

    /**
     * @Annotation\Flags({"priority":100})
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"EmailAddress","options":{"allow":15}})
     * @Annotation\Attributes({"type":"email","label":"What is the best email to reach you at?"})
     */
    public $email;

    /**
     * @Annotation\Name("user_image")
     * @Annotation\AllowEmpty()
     * @Annotation\Required(false)
     * @Annotation\Attributes({"type":"text","label":"Provide a URL for your avatar (optional):"})
     * @Annotation\Validator({"name":"ZendTest\Form\TestAsset\Annotation\UrlValidator"})
     */
    public $avatar;

    /**
     * @Annotation\Exclude()
     */
    protected $someComposedObject;
}
