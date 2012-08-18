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

use Traversable;
use Zend\Form\Element;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator\Explode as ExplodeValidator;
use Zend\Validator\InArray as InArrayValidator;
use Zend\Validator\ValidatorInterface;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Select extends Element implements InputProviderInterface
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type'    => 'select',
        'options' => array(),
    );

    /**
    * @var ValidatorInterface
    */
    protected $validator;

    /**
    * Get validator
    *
    * @return ValidatorInterface
    */
    protected function getValidator()
    {
        if (null === $this->validator) {
            $validator = new InArrayValidator(array(
                'haystack' => $this->getOptionAttributeValues(),
                'strict'   => false
            ));

            $multiple = (isset($this->attributes['multiple']))
                      ? $this->attributes['multiple'] : null;

            if (true === $multiple || 'multiple' === $multiple) {
                $validator = new ExplodeValidator(array(
                    'validator'      => $validator,
                    'valueDelimiter' => null, // skip explode if only one value
                ));
            }

            $this->validator = $validator;
        }
        return $this->validator;
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
        $spec = array(
            'name' => $this->getName(),
            'required' => true,
            'validators' => array(
                $this->getValidator()
            )
        );

        return $spec;
    }

    /**
     * Get only the values from the options attribute
     *
     * @return array
     */
    protected function getOptionAttributeValues()
    {
        $values = array();
        $options = $this->getAttribute('options');
        foreach ($options as $key => $optionSpec) {
            $value = (is_array($optionSpec)) ? $optionSpec['value'] : $key;
            $values[] = $value;
        }
        return $values;
    }
}
