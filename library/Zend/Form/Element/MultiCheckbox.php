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

use Zend\Validator\Explode as ExplodeValidator;
use Zend\Validator\InArray as InArrayValidator;
use Zend\Validator\ValidatorInterface;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 */
class MultiCheckbox extends Checkbox
{
    /**
     * @var bool
     */
    protected $useHiddenElement = false;

    /**
     * @var string
     */
    protected $uncheckedValue = '';

    /**
     * Get validator
     *
     * @return ValidatorInterface
     */
    protected function getValidator()
    {
        if (null === $this->validator) {
            $inArrayValidator = new InArrayValidator(array(
                'haystack'  => $this->getOptionAttributeValues(),
                'strict'    => false,
            ));
            $this->validator = new ExplodeValidator(array(
                'validator'      => $inArrayValidator,
                'valueDelimiter' => null, // skip explode if only one value
            ));
        }
        return $this->validator;
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
            $value = (is_array($optionSpec)) ? $optionSpec['value'] : $optionSpec;
            $values[] = $value;
        }
        if ($this->useHiddenElement()) {
            $values[] = $this->getUncheckedValue();
        }
        return $values;
    }
}
