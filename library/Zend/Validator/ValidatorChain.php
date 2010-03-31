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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Validator;
use Zend;

/**
 * @uses       \Zend\Loader
 * @uses       \Zend\Validator\AbstractValidator
 * @uses       \Zend\Validator\Exception
 * @uses       \Zend\Validator\Validator
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ValidatorChain implements Validator
{
    /**
     * Validator chain
     *
     * @var array
     */
    protected $_validators = array();

    /**
     * Array of validation failure messages
     *
     * @var array
     */
    protected $_messages = array();

    /**
     * Default Namespaces
     *
     * @var array
     */
    protected static $_defaultNamespaces = array();

    /**
     * Array of validation failure message codes
     *
     * @var array
     * @deprecated Since 1.5.0
     */
    protected $_errors = array();

    /**
     * Adds a validator to the end of the chain
     *
     * If $breakChainOnFailure is true, then if the validator fails, the next validator in the chain,
     * if one exists, will not be executed.
     *
     * @param  \Zend\Validator\Validator $validator
     * @param  boolean                 $breakChainOnFailure
     * @return \Zend\Validator\ValidatorChain Provides a fluent interface
     */
    public function addValidator(Validator $validator, $breakChainOnFailure = false)
    {
        $this->_validators[] = array(
            'instance' => $validator,
            'breakChainOnFailure' => (boolean) $breakChainOnFailure
            );
        return $this;
    }

    /**
     * Returns true if and only if $value passes all validations in the chain
     *
     * Validators are run in the order in which they were added to the chain (FIFO).
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_messages = array();
        $this->_errors   = array();
        $result = true;
        foreach ($this->_validators as $element) {
            $validator = $element['instance'];
            if ($validator->isValid($value)) {
                continue;
            }
            $result = false;
            $messages = $validator->getMessages();
            $this->_messages = array_merge($this->_messages, $messages);
            $this->_errors   = array_merge($this->_errors,   array_keys($messages));
            if ($element['breakChainOnFailure']) {
                break;
            }
        }
        return $result;
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns array of validation failure messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns array of validation failure message codes
     *
     * @return array
     * @deprecated Since 1.5.0
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Returns the set default namespaces
     *
     * @return array
     */
    public static function getDefaultNamespaces()
    {
        return self::$_defaultNamespaces;
    }

    /**
     * Sets new default namespaces
     *
     * @param array|string $namespace
     * @return null
     */
    public static function setDefaultNamespaces($namespace)
    {
        if (!is_array($namespace)) {
            $namespace = array((string) $namespace);
        }

        self::$_defaultNamespaces = $namespace;
    }

    /**
     * Adds a new default namespace
     *
     * @param array|string $namespace
     * @return null
     */
    public static function addDefaultNamespaces($namespace)
    {
        if (!is_array($namespace)) {
            $namespace = array((string) $namespace);
        }

        self::$_defaultNamespaces = array_unique(array_merge(self::$_defaultNamespaces, $namespace));
    }

    /**
     * Returns true when defaultNamespaces are set
     *
     * @return boolean
     */
    public static function hasDefaultNamespaces()
    {
        return (!empty(self::$_defaultNamespaces));
    }

    /**
     * @param  mixed    $value
     * @param  string   $classBaseName
     * @param  array    $args          OPTIONAL
     * @param  mixed    $namespaces    OPTIONAL
     * @return boolean
     * @throws \Zend\Validator\Exception
     */
    public static function is($value, $classBaseName, array $args = array(), $namespaces = array())
    {
        $namespaces = array_merge((array) $namespaces, self::$_defaultNamespaces, array('Zend_Validate'));
        $className  = ucfirst($classBaseName);
        try {
            if (!class_exists($className, false)) {
                foreach($namespaces as $namespace) {
                    $class = $namespace . '_' . $className;
                    $file  = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
                    if (\Zend\Loader::isReadable($file)) {
                        \Zend\Loader::loadClass($class);
                        $className = $class;
                        break;
                    }
                }
            }

            $class = new \ReflectionClass($className);
            if ($class->implementsInterface('Zend\Validator\Validator')) {
                if ($class->hasMethod('__construct')) {
                    $keys    = array_keys($args);
                    $numeric = false;
                    foreach($keys as $key) {
                        if (is_numeric($key)) {
                            $numeric = true;
                            break;
                        }
                    }

                    if ($numeric) {
                        $object = $class->newInstanceArgs($args);
                    } else {
                        $object = $class->newInstance($args);
                    }
                } else {
                    $object = $class->newInstance();
                }

                return $object->isValid($value);
            }
        } catch (Exception $ze) {
            // if there is an exception while validating throw it
            throw $ze;
        } catch (\Exception $e) {
            // fallthrough and continue for missing validation classes
        }

        throw new Exception("Validate class not found from basename '$classBaseName'");
    }

    /**
     * Returns the maximum allowed message length
     *
     * @return integer
     */
    public static function getMessageLength()
    {
        return AbstractValidator::getMessageLength();
    }

    /**
     * Sets the maximum allowed message length
     *
     * @param integer $length
     */
    public static function setMessageLength($length = -1)
    {
        AbstractValidator::setMessageLength($length);
    }

    /**
     * Returns the default translation object
     *
     * @return \Zend\Translate\Adapter\Adapter|null
     */
    public static function getDefaultTranslator($translator = null)
    {
        require_once 'Zend/Validate/Abstract.php';
        return AbstractValidator::getDefaultTranslator();
    }

    /**
     * Sets a default translation object for all validation objects
     *
     * @param Zend_Translate|\Zend\Translate\Adapter\Adapter|null $translator
     */
    public static function setDefaultTranslator($translator = null)
    {
        require_once 'Zend/Validate/Abstract.php';
        AbstractValidator::setDefaultTranslator($translator);
    }
}
