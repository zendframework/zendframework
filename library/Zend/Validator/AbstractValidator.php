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
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Validator;

use Traversable,
    Zend\Stdlib\IteratorToArray,
    Zend\Translator,
    Zend\Validator\Exception\InvalidArgumentException;

/**
 * @uses       \Zend\Registry
 * @uses       \Zend\Validator\Exception
 * @uses       \Zend\Validator\Validator
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractValidator implements Validator
{
    /**
     * The value to be validated
     *
     * @var mixed
     */
    protected $value;

    /**
     * Default translation object for all validate objects
     * @var \Zend\Translator\Translator
     */
    protected static $_defaultTranslator;

    /**
     * Limits the maximum returned length of a error message
     *
     * @var Integer
     */
    protected static $_messageLength = -1;

    protected $abstractOptions = array(
        'messages'           => array(),  // Array of validation failure messages
        'messageTemplates'   => array(),  // Array of validation failure message templates
        'messageVariables'   => array(),  // Array of additional variables available for validation failure messages
        'translator'         => null,     // Translation object to used -> \Zend\Translator\Translator
        'translatorDisabled' => false,    // Is translation disabled?
        'valueObscured'      => false,    // Flag indidcating whether or not value should be obfuscated in error messages
    );

    /**
     * Abstract constructor for all validators
     * A validator should accept following parameters:
     *  - nothing f.e. Validator()
     *  - one or multiple scalar values f.e. Validator($first, $second, $third)
     *  - an array f.e. Validator(array($first => 'first', $second => 'second', $third => 'third'))
     *  - an instance of Zend_Config f.e. Validator($config_instance)
     *
     * @param mixed $options
     */
    public function __construct($options = null)
    {
        // The abstract constructor allows no scalar values
        if ($options instanceof Traversable) {
            $options = IteratorToArray::convert($options);
        }

        if (isset($this->_messageTemplates)) {
            $this->abstractOptions['messageTemplates'] = $this->_messageTemplates;
        }

        if (isset($this->_messageVariables)) {
            $this->abstractOptions['messageVariables'] = $this->_messageVariables;
        }

        if (is_array($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * Returns an option
     *
     * @param string $option Option to be returned
     * @throws \Zend\Validator\Exception\InvalidArgumentException
     * @return mixed Returned option
     */
    public function getOption($option)
    {
        if (array_key_exists($option, $this->abstractOptions)) {
            return $this->abstractOptions[$option];
        }

        if (isset($this->options) && array_key_exists($option, $this->options)) {
            return $this->options[$option];
        }

        throw new InvalidArgumentException("Invalid option '$option'");
    }

    /**
     * Returns all available options
     *
     * @return array Array with all available options
     */
    public function getOptions()
    {
        $result = $this->abstractOptions;
        if (isset($this->options)) {
            $result += $this->options;
        }
        return $result;
    }

    /**
     * Sets one or multiple options
     *
     * @param  array|Traversable $options Options to set
     * @return \Zend\Validator\AbstractValidator Provides fluid interface
     */
    public function setOptions($options = array())
    {
        if (!is_array($options) && !$options instanceof Traversable) {
            throw new Exception\InvalidArgumentException(__METHOD__ . ' expects an array or Traversable');
        }

        foreach ($options as $name => $option) {
            $fname = 'set' . ucfirst($name);
            $fname2 = 'is' . ucfirst($name);
            if (($name != 'setOptions') && method_exists($this, $name)) {
                $this->{$name}($option);
            } else if (($fname != 'setOptions') && method_exists($this, $fname)) {
                $this->{$fname}($option);
            } else if (($fname2 != 'setOptions') && method_exists($this, $fname2)) {
                $this->{$fname2}($option);
            } else if (isset($this->options)) {
                $this->options[$name] = $options;
            } else {
                $this->abstractOptions[$name] = $options;
            }
        }

        return $this;
    }

    /**
     * Returns array of validation failure messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->abstractOptions['messages'];
    }

    /**
     * Invoke as command
     *
     * @param  mixed $value
     * @return boolean
     */
    public function __invoke($value)
    {
        return $this->isValid($value);
    }

    /**
     * Returns an array of the names of variables that are used in constructing validation failure messages
     *
     * @return array
     */
    public function getMessageVariables()
    {
        return array_keys($this->abstractOptions['messageVariables']);
    }

    /**
     * Returns the message templates from the validator
     *
     * @return array
     */
    public function getMessageTemplates()
    {
        return $this->abstractOptions['messageTemplates'];
    }

    /**
     * Sets the validation failure message template for a particular key
     *
     * @param  string $messageString
     * @param  string $messageKey     OPTIONAL
     * @return \Zend\Validator\AbstractValidator Provides a fluent interface
     * @throws \Zend\Validator\Exception
     */
    public function setMessage($messageString, $messageKey = null)
    {
        if ($messageKey === null) {
            $keys = array_keys($this->abstractOptions['messageTemplates']);
            foreach($keys as $key) {
                $this->setMessage($messageString, $key);
            }
            return $this;
        }

        if (!isset($this->abstractOptions['messageTemplates'][$messageKey])) {
            throw new InvalidArgumentException("No message template exists for key '$messageKey'");
        }

        $this->abstractOptions['messageTemplates'][$messageKey] = $messageString;
        return $this;
    }

    /**
     * Sets validation failure message templates given as an array, where the array keys are the message keys,
     * and the array values are the message template strings.
     *
     * @param  array $messages
     * @return \Zend\Validator\AbstractValidator
     */
    public function setMessages(array $messages)
    {
        foreach ($messages as $key => $message) {
            $this->setMessage($message, $key);
        }
        return $this;
    }

    /**
     * Magic function returns the value of the requested property, if and only if it is the value or a
     * message variable.
     *
     * @param  string $property
     * @return mixed
     * @throws \Zend\Validator\Exception
     */
    public function __get($property)
    {
        if ($property == 'value') {
            return $this->value;
        }

        if (array_key_exists($property, $this->abstractOptions['messageVariables'])) {
            $result = $this->abstractOptions['messageVariables'][$property];
            if (is_array($result)) {
                $result = $this->{key($result)}[current($result)];
            } else {
                $result = $this->{$result};
            }
            return $result;
        }

        if (isset($this->_messageVariables) && array_key_exists($property, $this->_messageVariables)) {
            $result = $this->{$this->_messageVariables[$property]};
            if (is_array($result)) {
                $result = $this->{key($result)}[current($result)];
            } else {
                $result = $this->{$result};
            }
            return $result;
        }

        throw new InvalidArgumentException("No property exists by the name '$property'");
    }

    /**
     * Constructs and returns a validation failure message with the given message key and value.
     *
     * Returns null if and only if $messageKey does not correspond to an existing template.
     *
     * If a translator is available and a translation exists for $messageKey,
     * the translation will be used.
     *
     * @param  string $messageKey
     * @param  string $value
     * @return string
     */
    protected function createMessage($messageKey, $value)
    {
        if (!isset($this->abstractOptions['messageTemplates'][$messageKey])) {
            return null;
        }

        $message = $this->abstractOptions['messageTemplates'][$messageKey];

        if (null !== ($translator = $this->getTranslator())) {
            if ($translator->isTranslated($messageKey)) {
                $message = $translator->translate($messageKey);
            } else {
                $message = $translator->translate($message);
            }
        }

        if (is_object($value)) {
            if (!in_array('__toString', get_class_methods($value))) {
                $value = get_class($value) . ' object';
            } else {
                $value = $value->__toString();
            }
        } else {
            $value = (string)$value;
        }

        if ($this->isValueObscured()) {
            $value = str_repeat('*', strlen($value));
        }

        $message = str_replace('%value%', (string) $value, $message);
        foreach ($this->abstractOptions['messageVariables'] as $ident => $property) {
            if (is_array($property)) {
                $message = str_replace("%$ident%", (string) $this->{key($property)}[current($property)], $message);
            } else {
                $message = str_replace("%$ident%", (string) $this->$property, $message);
            }
        }

        $length = self::getMessageLength();
        if (($length > -1) && (strlen($message) > $length)) {
            $message = substr($message, 0, ($length - 3)) . '...';
        }

        return $message;
    }

    /**
     * @param  string $messageKey
     * @param  string $value      OPTIONAL
     * @return void
     */
    protected function error($messageKey, $value = null)
    {
        if ($messageKey === null) {
            $keys = array_keys($this->abstractOptions['messageTemplates']);
            $messageKey = current($keys);
        }

        if ($value === null) {
            $value = $this->value;
        }

        $this->abstractOptions['messages'][$messageKey] = $this->createMessage($messageKey, $value);
    }

    /**
     * Returns the validation value
     *
     * @return mixed Value to be validated
     */
    protected function getValue()
    {
        return $this->value;
    }

    /**
     * Sets the value to be validated and clears the messages and errors arrays
     *
     * @param  mixed $value
     * @return void
     */
    protected function setValue($value)
    {
        $this->value               = $value;
        $this->abstractOptions['messages'] = array();
    }

    /**
     * Set flag indicating whether or not value should be obfuscated in messages
     *
     * @param  bool $flag
     * @return \Zend\Validator\AbstractValidator
     */
    public function setValueObscured($flag)
    {
        $this->abstractOptions['valueObscured'] = (bool) $flag;
        return $this;
    }

    /**
     * Retrieve flag indicating whether or not value should be obfuscated in
     * messages
     *
     * @return bool
     */
    public function isValueObscured()
    {
        return $this->abstractOptions['valueObscured'];
    }

    /**
     * Set translation object
     *
     * @param  Zend_Translator|\Zend\Translator\Adapter\AbstractAdapter|null $translator
     * @return \Zend\Validator\AbstractValidator
     */
    public function setTranslator($translator = null)
    {
        if ((null === $translator) || ($translator instanceof Translator\Adapter\AbstractAdapter)) {
            $this->abstractOptions['translator'] = $translator;
        } elseif ($translator instanceof Translator\Translator) {
            $this->abstractOptions['translator'] = $translator->getAdapter();
        } else {
            throw new InvalidArgumentException('Invalid translator specified');
        }

        return $this;
    }

    /**
     * Return translation object
     *
     * @return \Zend\Translator\Adapter|null
     */
    public function getTranslator()
    {
        if ($this->isTranslatorDisabled()) {
            return null;
        }

        if (null === $this->abstractOptions['translator']) {
            return self::getDefaultTranslator();
        }

        return $this->abstractOptions['translator'];
    }

    /**
     * Does this validator have its own specific translator?
     *
     * @return bool
     */
    public function hasTranslator()
    {
        return (bool)$this->abstractOptions['translator'];
    }

    /**
     * Set default translation object for all validate objects
     *
     * @param  Zend_Translator|\Zend\Translator\Adapter|null $translator
     * @return void
     */
    public static function setDefaultTranslator($translator = null)
    {
        if ((null === $translator) || ($translator instanceof Translator\Adapter\AbstractAdapter)) {
            self::$_defaultTranslator = $translator;
        } elseif ($translator instanceof Translator\Translator) {
            self::$_defaultTranslator = $translator->getAdapter();
        } else {
            throw new InvalidArgumentException('Invalid translator specified');
        }
    }

    /**
     * Get default translation object for all validate objects
     *
     * @return \Zend\Translator\Adapter|null
     */
    public static function getDefaultTranslator()
    {
        if (null === self::$_defaultTranslator) {
            if (\Zend\Registry::isRegistered('Zend_Translator')) {
                $translator = \Zend\Registry::get('Zend_Translator');
                if ($translator instanceof Translator\Adapter\AbstractAdapter) {
                    return $translator;
                } elseif ($translator instanceof Translator\Translator) {
                    return $translator->getAdapter();
                }
            }
        }

        return self::$_defaultTranslator;
    }

    /**
     * Is there a default translation object set?
     *
     * @return boolean
     */
    public static function hasDefaultTranslator()
    {
        return (bool)self::$_defaultTranslator;
    }

    /**
     * Indicate whether or not translation should be disabled
     *
     * @param  bool $flag
     * @return \Zend\Validator\AbstractValidator
     */
    public function setTranslatorDisabled($flag)
    {
        $this->abstractOptions['translatorDisabled'] = (bool) $flag;
        return $this;
    }

    /**
     * Is translation disabled?
     *
     * @return bool
     */
    public function isTranslatorDisabled()
    {
        return $this->abstractOptions['translatorDisabled'];
    }

    /**
     * Returns the maximum allowed message length
     *
     * @return integer
     */
    public static function getMessageLength()
    {
        return self::$_messageLength;
    }

    /**
     * Sets the maximum allowed message length
     *
     * @param integer $length
     */
    public static function setMessageLength($length = -1)
    {
        self::$_messageLength = $length;
    }
}
