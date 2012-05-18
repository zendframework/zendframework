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

namespace Zend\Validator;

use Countable;
use Zend\Loader\Broker;
use Zend\Loader\Pluggable;

/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ValidatorChain implements 
    Countable, 
    Pluggable,
    ValidatorInterface
{
    /**
     * @var Broker
     */
    protected $broker;

    /**
     * Validator chain
     *
     * @var array
     */
    protected $validators = array();

    /**
     * Array of validation failure messages
     *
     * @var array
     */
    protected $messages = array();

    /**
     * Array of validation failure message codes
     *
     * @var array
     * @deprecated Since 1.5.0
     */
    protected $errors = array();

    /**
     * Return the count of attached valicators
     * 
     * @return int
     */
    public function count()
    {
        return count($this->validators);
    }

    /**
     * Get plugin broker instance
     * 
     * @return Zend\Loader\Broker
     */
    public function getBroker()
    {
        if (!$this->broker) {
            $this->setBroker(new ValidatorBroker());
        }
        return $this->broker;
    }

    /**
     * Set plugin broker instance
     * 
     * @param  string|Broker $broker Plugin broker to load plugins
     * @return ValidatorChain
     */
    public function setBroker($broker)
    {
        if (!$broker instanceof Broker) {
            throw new Exception\RuntimeException(sprintf(
                '%s expects an argument of type Zend\Loader\Broker; received "%s"',
                __METHOD__,
                (is_object($broker) ? get_class($broker) : gettype($broker))
            ));
        }
        $this->broker = $broker;
        return $this;
    }

    /**
     * Retrieve a validator by name
     * 
     * @param  string     $plugin  Name of validator to return
     * @param  null|array $options Options to pass to validator constructor (if not already instantiated)
     * @return ValidatorInterface
     */
    public function plugin($name, array $options = null)
    {
        $broker = $this->getBroker();
        return $broker->load($name, $options);
    }

    /**
     * Adds a validator to the end of the chain
     *
     * If $breakChainOnFailure is true, then if the validator fails, the next validator in the chain,
     * if one exists, will not be executed.
     *
     * @param  ValidatorInterface $validator
     * @param  boolean                 $breakChainOnFailure
     * @return ValidatorChain Provides a fluent interface
     */
    public function addValidator(ValidatorInterface $validator, $breakChainOnFailure = false)
    {
        $this->validators[] = array(
            'instance'            => $validator,
            'breakChainOnFailure' => (boolean) $breakChainOnFailure
        );
        return $this;
    }

    /**
     * Adds a validator to the beginning of the chain
     *
     * If $breakChainOnFailure is true, then if the validator fails, the next validator in the chain,
     * if one exists, will not be executed.
     *
     * @param  ValidatorInterface $validator
     * @param  boolean                 $breakChainOnFailure
     * @return ValidatorChain Provides a fluent interface
     */
    public function prependValidator(ValidatorInterface $validator, $breakChainOnFailure = false)
    {
        array_unshift($this->validators, array(
            'instance'            => $validator,
            'breakChainOnFailure' => (boolean) $breakChainOnFailure
        ));
        return $this;
    }

    /**
     * Use the plugin broker to add a validator by name
     * 
     * @param  string $name 
     * @param  array $options 
     * @param  bool $breakChainOnFailure 
     * @return ValidatorChain
     */
    public function addByName($name, $options = array(), $breakChainOnFailure = false)
    {
        $validator = $this->plugin($name, $options);
        $this->addValidator($validator, $breakChainOnFailure);
        return $this;
    }

    /**
     * Use the plugin broker to prepend a validator by name
     * 
     * @param  string $name 
     * @param  array $options 
     * @param  bool $breakChainOnFailure 
     * @return ValidatorChain
     */
    public function prependByName($name, $options = array(), $breakChainOnFailure = false)
    {
        $validator = $this->plugin($name, $options);
        $this->prependValidator($validator, $breakChainOnFailure);
        return $this;
    }

    /**
     * Returns true if and only if $value passes all validations in the chain
     *
     * Validators are run in the order in which they were added to the chain (FIFO).
     *
     * @param  mixed $value
     * @param  mixed $context Extra "context" to provide the validator
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->messages = array();
        $this->errors   = array();
        $result = true;
        foreach ($this->validators as $element) {
            $validator = $element['instance'];
            if ($validator->isValid($value, $context)) {
                continue;
            }
            $result = false;
            $messages = $validator->getMessages();
            $this->messages = array_merge($this->messages, $messages);
            $this->errors   = array_merge($this->errors,   array_keys($messages));
            if ($element['breakChainOnFailure']) {
                break;
            }
        }
        return $result;
    }

    /**
     * Returns array of validation failure messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Returns array of validation failure message codes
     *
     * @return array
     * @deprecated Since 1.5.0
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Invoke chain as command
     * 
     * @param  mixed $value 
     * @return boolean
     */
    public function __invoke($value)
    {
        return $this->isValid($value);
    }
}
