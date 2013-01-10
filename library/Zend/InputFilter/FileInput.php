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

use Zend\Validator\File\Upload as UploadValidator;

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
     * @var boolean
     */
    protected $autoPrependUploadValidator = true;

    /**
     * @param  boolean $value Enable/Disable automatically prepending an Upload validator
     * @return FileInput
     */
    public function setAutoPrependUploadValidator($value)
    {
        $this->autoPrependUploadValidator = $value;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getAutoPrependUploadValidator()
    {
        return $this->autoPrependUploadValidator;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        $value = $this->value;
        if ($this->isValid && is_array($value)) {
            $filter = $this->getFilterChain();
            if (isset($value['tmp_name'])) {
                // Single file input
                $value = $filter->filter($value);
            } else {
                // Multi file input (multiple attribute set)
                $newValue = array();
                foreach ($value as $fileData) {
                    if (is_array($fileData) && isset($fileData['tmp_name'])) {
                        $newValue[] = $filter->filter($fileData);
                    }
                }
                $value = $newValue;
            }
        }

        return $value;
    }

    /**
     * @param  mixed $context Extra "context" to provide the validator
     * @return boolean
     */
    public function isValid($context = null)
    {
        $this->injectUploadValidator();
        $validator = $this->getValidatorChain();
        //$value   = $this->getValue(); // Do not run the filters yet for File uploads

        $rawValue = $this->getRawValue();
        if (!is_array($rawValue)) {
            // This can happen in an AJAX POST, where the input comes across as a string
            $rawValue = array(
                'tmp_name' => $rawValue,
                'name'     => $rawValue,
                'size'     => 0,
                'type'     => '',
                'error'    => UPLOAD_ERR_NO_FILE,
            );
        }
        if (is_array($rawValue) && isset($rawValue['tmp_name'])) {
            // Single file input
            $this->isValid = $validator->isValid($rawValue, $context);
        } elseif (is_array($rawValue) && !empty($rawValue) && isset($rawValue[0]['tmp_name'])) {
            // Multi file input (multiple attribute set)
            $this->isValid = true;
            foreach ($rawValue as $value) {
                if (!$validator->isValid($value, $context)) {
                    $this->isValid = false;
                    break; // Do not continue processing files if validation fails
                }
            }
        }

        return $this->isValid;
    }

    /**
     * @return void
     */
    protected function injectUploadValidator()
    {
        if (!$this->autoPrependUploadValidator) {
            return;
        }
        $chain = $this->getValidatorChain();

        // Check if Upload validator is already first in chain
        $validators = $chain->getValidators();
        if (isset($validators[0]['instance'])
            && $validators[0]['instance'] instanceof UploadValidator
        ) {
            $this->autoPrependUploadValidator = false;
            return;
        }

        $chain->prependByName('fileupload', array(), true);
        $this->autoPrependUploadValidator = false;
    }

    /**
     * No-op, NotEmpty validator does not apply for FileInputs.
     * See also: BaseInputFilter::isValid()
     *
     * @return void
     */
    protected function injectNotEmptyValidator()
    {
        $this->notEmptyValidator = true;
    }

    /**
     * @param  InputInterface $input
     * @return FileInput
     */
    public function merge(InputInterface $input)
    {
        parent::merge($input);
        if ($input instanceof FileInput) {
            $this->setAutoPrependUploadValidator($input->getAutoPrependUploadValidator());
        }
        return $this;
    }
}
