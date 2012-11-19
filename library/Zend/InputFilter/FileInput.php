<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_InputFilter
 */

namespace Zend\InputFilter;

use Zend\Filter\FilterChain;
use Zend\Validator\ValidatorChain;
use Zend\Validator\NotEmpty;

/**
 * @category   Zend
 * @package    Zend_InputFilter
 */
class FileInput extends Input
{
    /**
     * @var boolean
     */
    protected $isValid = false;

    /**
     * @return mixed
     */
    public function getValue()
    {
        $filter = $this->getFilterChain();
        $value  = (is_array($this->value) && isset($this->value['tmp_name']))
                ? $this->value['tmp_name'] : $this->value;
        if (is_scalar($value) && $this->isValid) {
            // Single file input
            $value = $filter->filter($value);
        } elseif (is_array($value)) {
            // Multi file input (multiple attribute set)
            $newValue = array();
            foreach ($value as $multiFileData) {
                $fileName = (is_array($multiFileData) && isset($multiFileData['tmp_name']))
                            ? $multiFileData['tmp_name'] : $multiFileData;
                $newValue[] = ($this->isValid) ? $filter->filter($fileName) : $fileName;
            }
            $value = $newValue;
        }
        return $value;
    }

    /**
     * @param  mixed $context Extra "context" to provide the validator
     * @return boolean
     */
    public function isValid($context = null)
    {
        $this->injectNotEmptyValidator();
        $validator = $this->getValidatorChain();
        //$value     = $this->getValue(); // Do not run the filters yet for File uploads
        $this->isValid = $validator->isValid($this->getRawValue(), $context);
        return $this->isValid;
    }

    /**
     * @return void
     */
    protected function injectNotEmptyValidator()
    {
        $this->notEmptyValidator = true;
    }
}
