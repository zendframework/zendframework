<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Form\Element;

use Zend\Form\Element;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator\ValidatorInterface;
use Zend\Validator\Regex as RegexValidator;
use Zend\Validator\Explode as ExplodeValidator;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Email extends Element implements InputProviderInterface
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type' => 'email',
    );

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var ValidatorInterface
     */
    protected $emailValidator;

    /**
     * Get primary validator
     *
     * @return ValidatorInterface
     */
    public function getValidator()
    {
        if (null === $this->validator) {
            $emailValidator = $this->getEmailValidator();
            if (!empty($this->attributes['multiple'])) {
                $this->validator = new ExplodeValidator(array(
                    'validator' => $emailValidator,
                ));
            } else {
                $this->validator = $emailValidator;
            }
        }

        return $this->validator;
    }

    /**
     * Sets the primary validator to use for this element
     *
     * @param  ValidatorInterface $validator
     * @return Email
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
        return $this;
    }

    /**
     * Get the email validator to use for multiple or single
     * email addresses.
     *
     * Note from the HTML5 Specs regarding the regex:
     *
     * "This requirement is a *willful* violation of RFC 5322, which
     * defines a syntax for e-mail addresses that is simultaneously
     * too strict (before the "@" character), too vague
     * (after the "@" character), and too lax (allowing comments,
     * whitespace characters, and quoted strings in manners
     * unfamiliar to most users) to be of practical use here."
     *
     * The default Regex validator is in use to match that of the
     * browser validation, but you are free to set a different
     * (more strict) email validator such as Zend\Validator\Email
     * if you wish.
     *
     * @return ValidatorInterface
     */
    public function getEmailValidator()
    {
        if (null === $this->emailValidator) {
            $this->emailValidator = new RegexValidator(
                '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/'
            );
        }
        return $this->emailValidator;
    }

    /**
     * Sets the email validator to use for multiple or single
     * email addresses.
     *
     * @param  ValidatorInterface $validator
     * @return Email
     */
    public function setEmailValidator(ValidatorInterface $validator)
    {
        $this->emailValidator = $validator;
        return $this;
    }


    /**
     * Provide default input rules for this element
     *
     * Attaches an email validator.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        return array(
            'name' => $this->getName(),
            'required' => true,
            'filters' => array(
                array('name' => 'Zend\Filter\StringTrim'),
            ),
            'validators' => array(
                $this->getValidator(),
            ),
        );
    }
}
