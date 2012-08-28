<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace Zend\Form\Element;

use Zend\Form\Element;
use Zend\Form\ElementPrepareAwareInterface;
use Zend\Form\Form;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator\Csrf as CsrfValidator;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 */
class Csrf extends Element implements InputProviderInterface, ElementPrepareAwareInterface
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type' => 'hidden',
    );

    /**
     * @var array
     */
    protected $csrfValidatorOptions = array();

    /**
     * @var CsrfValidator
     */
    protected $csrfValidator;

    /**
     * @return array
     */
    public function getCsrfValidatorOptions()
    {
        return $this->csrfValidatorOptions;
    }

    /**
     * @param  array $options
     * @return Csrf
     */
    public function setCsrfValidatorOptions(array $options)
    {
        $this->csrfValidatorOptions = $options;
        return $this;
    }

    /**
     * Get CSRF validator
     *
     * @return CsrfValidator
     */
    public function getCsrfValidator()
    {
        if (null === $this->csrfValidator) {
            $csrfOptions = $this->getCsrfValidatorOptions();
            $csrfOptions = array_merge($csrfOptions, array('name' => $this->getName()));
            $this->csrfValidator = new CsrfValidator($csrfOptions);
        }
        return $this->csrfValidator;
    }

    /**
     * @param  \Zend\Validator\Csrf $validator
     * @return Csrf
     */
    public function setCsrfValidator(CsrfValidator $validator)
    {
        $this->csrfValidator = $validator;
        return $this;
    }

    /**
     * Retrieve value
     *
     * Retrieves the hash from the validator
     *
     * @return void
     */
    public function getValue()
    {
        $validator = $this->getCsrfValidator();
        return $validator->getHash();
    }

    /**
     * Override: get attributes
     *
     * Seeds 'value' attribute with validator hash
     *
     * @return array
     */
    public function getAttributes()
    {
        $attributes = parent::getAttributes();
        $validator  = $this->getCsrfValidator();
        $attributes['value'] = $validator->getHash();
        return $attributes;
    }

    /**
     * Provide default input rules for this element
     *
     * Attaches the captcha as a validator.
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
                $this->getCsrfValidator(),
            ),
        );
    }

    /**
     * Prepare the form element
     */
    public function prepareElement(Form $form)
    {
        $this->getCsrfValidator()->getHash(true);
    }
}
