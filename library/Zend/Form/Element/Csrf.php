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
     * @var CsrfValidator
     */
    protected $validator;

    /**
     * Get CSRF validator
     *
     * @return CsrfValidator
     */
    protected function getValidator()
    {
        if (null === $this->validator) {
            $this->validator = new CsrfValidator(array('name' => $this->getName()));
        }
        return $this->validator;
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
        $validator = $this->getValidator();
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
        $validator  = $this->getValidator();
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
                $this->getValidator(),
            ),
        );
    }

    /**
     * Prepare the form element
     */
    public function prepareElement(Form $form)
    {
        $this->getValidator()->getHash(true);
    }
}
