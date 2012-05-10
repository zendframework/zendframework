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
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Filter;

use Zend\Loader\Broker,
    Zend\Registry,
    Zend\Translator\Adapter\AbstractAdapter as TranslationAdapter,
    Zend\Translator\Translator as Translator,
    Zend\Validator;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class InputFilter
{

    const ALLOW_EMPTY           = 'allowEmpty';
    const BREAK_CHAIN           = 'breakChainOnFailure';
    const DEFAULT_VALUE         = 'default';
    const MESSAGES              = 'messages';
    const ESCAPE_FILTER         = 'escapeFilter';
    const FIELDS                = 'fields';
    const FILTER                = 'filter';
    const FILTER_CHAIN          = 'filterChain';
    const FILTER_BROKER         = 'filterBroker';
    const MISSING_MESSAGE       = 'missingMessage';
    const NOT_EMPTY_MESSAGE     = 'notEmptyMessage';
    const PRESENCE              = 'presence';
    const PRESENCE_OPTIONAL     = 'optional';
    const PRESENCE_REQUIRED     = 'required';
    const RULE                  = 'rule';
    const RULE_WILDCARD         = '*';
    const VALIDATOR             = 'validator';
    const VALIDATOR_BROKER      = 'validatorBroker';
    const VALIDATOR_CHAIN       = 'validatorChain';
    const VALIDATOR_CHAIN_COUNT = 'validatorChainCount';

    /**
     * @var array Input data, before processing.
     */
    protected $data = array();

    /**
     * @var array Association of rules to filters.
     */
    protected $filterRules = array();

    /**
     * @var array Association of rules to validators.
     */
    protected $validatorRules = array();

    /**
     * @var array After processing data, this contains mapping of valid fields
     * to field values.
     */
    protected $validFields = array();

    /**
     * @var array After processing data, this contains mapping of validation
     * rules that did not pass validation to the array of messages returned
     * by the validator chain.
     */
    protected $invalidMessages = array();

    /**
     * @var array After processing data, this contains mapping of validation
     * rules that did not pass validation to the array of error identifiers
     * returned by the validator chain.
     */
    protected $invalidErrors = array();

    /**
     * @var array After processing data, this contains mapping of validation
     * rules in which some fields were missing to the array of messages
     * indicating which fields were missing.
     */
    protected $missingFields = array();

    /**
     * @var array After processing, this contains a copy of $_data elements
     * that were not mentioned in any validation rule.
     */
    protected $unknownFields = array();

    /**
     * @var Zend\Filter\FilterInterface The filter object that is run on values
     * returned by the getEscaped() method.
     */
    protected $defaultEscapeFilter = null;

    /**
     * Plugin brokers
     * @var array
     */
    protected $brokers = array();

    /**
     * @var array Default values to use when processing filters and validators.
     */
    protected $defaults = array(
        self::ALLOW_EMPTY         => false,
        self::BREAK_CHAIN         => false,
        self::ESCAPE_FILTER       => 'HtmlEntities',
        self::MISSING_MESSAGE     => "Field '%field%' is required by rule '%rule%', but the field is missing",
        self::NOT_EMPTY_MESSAGE   => "You must give a non-empty value for field '%field%'",
        self::PRESENCE            => self::PRESENCE_OPTIONAL
    );

    /**
     * @var boolean Set to False initially, this is set to True after the
     * input data have been processed.  Reset to False in setData() method.
     */
    protected $processed = false;

    /**
     * Translation object
     * @var Zend\Translator\Translator
     */
    protected $translator;

    /**
     * Is translation disabled?
     * @var Boolean
     */
    protected $translatorDisabled = false;

    /**
     * @param array $filterRules
     * @param array $validatorRules
     * @param array $data       OPTIONAL
     * @param array $options    OPTIONAL
     */
    public function __construct($filterRules, $validatorRules, array $data = null, array $options = null)
    {
        if ($options) {
            $this->setOptions($options);
        }

        $this->filterRules = (array) $filterRules;
        $this->validatorRules = (array) $validatorRules;

        if ($data) {
            $this->setData($data);
        }
    }

    /**
     * Set plugin brokers for use with validators and filters
     *
     * @param  Broker $broker
     * @param  string $type 'filter' or 'validator'
     * @return InputFilter
     * @throws Exception on invalid type
     */
    public function setPluginBroker($broker, $type)
    {
        $type = strtolower($type);
        switch ($type) {
            case self::FILTER:
            case self::VALIDATOR:
                if (is_string($broker)) {
                    if (!class_exists($broker)) {
                        throw new Exception\RuntimeException(sprintf('Broker class "%s" not found', $broker));
                    }
                    $broker = new $broker;
                }
                if (!$broker instanceof Broker) {
                    throw new Exception\RuntimeException(sprintf(
                        'setPluginBroker() expects a class or object of type Zend\Loader\Broker; received "%s"',
                        (is_object($broker) ? get_class($broker) : gettype($broker))
                    ));
                }
                $this->brokers[$type] = $broker;
                return $this;
            default:
                throw new Exception\InvalidArgumentException(sprintf('Invalid type "%s" provided to setPluginBroker()', $type));
        }

        return $this;
    }

    /**
     * Retrieve plugin broker for given type
     *
     * $type may be one of:
     * - filter
     * - validator
     *
     * If a plugin broker does not exist for the given type, defaults are
     * created.
     *
     * @param  string $type 'filter' or 'validator'
     * @return Broker
     * @throws Exception on invalid type
     */
    public function getPluginBroker($type)
    {
        $type = strtolower($type);
        if (!isset($this->brokers[$type])) {
            switch ($type) {
                case self::FILTER:
                    $this->setPluginBroker(new FilterBroker(), $type);
                    break;
                case self::VALIDATOR:
                    $this->setPluginBroker(new Validator\ValidatorBroker(), $type);
                    break;
                default:
                    throw new Exception\InvalidArgumentException(sprintf('Invalid type "%s" provided to getPluginBroker()', $type));
            }
        }

        return $this->brokers[$type];
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        $this->_process();
        return array_merge($this->invalidMessages, $this->missingFields);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        $this->_process();
        return $this->invalidErrors;
    }

    /**
     * @return array
     */
    public function getInvalid()
    {
        $this->_process();
        return $this->invalidMessages;
    }

    /**
     * @return array
     */
    public function getMissing()
    {
        $this->_process();
        return $this->missingFields;
    }

    /**
     * @return array
     */
    public function getUnknown()
    {
        $this->_process();
        return $this->unknownFields;
    }

    /**
     * @param string $fieldName OPTIONAL
     * @return mixed
     */
    public function getEscaped($fieldName = null)
    {
        $this->_process();
        $this->_getDefaultEscapeFilter();

        if ($fieldName === null) {
            return $this->_escapeRecursive($this->validFields);
        }
        if (array_key_exists($fieldName, $this->validFields)) {
            return $this->_escapeRecursive($this->validFields[$fieldName]);
        }
        return null;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    protected function _escapeRecursive($data)
    {
        if($data === null) {
            return $data;
        }

        if (!is_array($data)) {
            $filter = $this->_getDefaultEscapeFilter();
            return $filter->filter($data);
        }
        foreach ($data as &$element) {
            $element = $this->_escapeRecursive($element);
        }
        return $data;
    }

    /**
     * @param string $fieldName OPTIONAL
     * @return mixed
     */
    public function getUnescaped($fieldName = null)
    {
        $this->_process();
        if ($fieldName === null) {
            return $this->validFields;
        }
        if (array_key_exists($fieldName, $this->validFields)) {
            return $this->validFields[$fieldName];
        }
        return null;
    }

    /**
     * @param string $fieldName
     * @return mixed
     */
    public function __get($fieldName)
    {
        return $this->getEscaped($fieldName);
    }

    /**
     * @return boolean
     */
    public function hasInvalid()
    {
        $this->_process();
        return !(empty($this->invalidMessages));
    }

    /**
     * @return boolean
     */
    public function hasMissing()
    {
        $this->_process();
        return !(empty($this->missingFields));
    }

    /**
     * @return boolean
     */
    public function hasUnknown()
    {
        $this->_process();
        return !(empty($this->unknownFields));
    }

    /**
     * @return boolean
     */
    public function hasValid()
    {
        $this->_process();
        return !(empty($this->validFields));
    }

    /**
     * @param string $fieldName
     * @return boolean
     */
    public function isValid($fieldName = null)
    {
        $this->_process();
        if ($fieldName === null) {
            return !($this->hasMissing() || $this->hasInvalid());
        }
        return array_key_exists($fieldName, $this->validFields);
    }

    /**
     * @param string $fieldName
     * @return boolean
     */
    public function __isset($fieldName)
    {
        $this->_process();
        return isset($this->validFields[$fieldName]);
    }

    /**
     * @return InputFilter
     * @throws Exception\RuntimeException
     */
    public function process()
    {
        $this->_process();
        if ($this->hasInvalid()) {
            throw new Exception\RuntimeException("Input has invalid fields");
        }
        if ($this->hasMissing()) {
            throw new Exception\RuntimeException("Input has missing fields");
        }

        return $this;
    }

    /**
     * @param array $data
     * @return InputFilter
     */
    public function setData(array $data)
    {
        $this->data = $data;

        /**
         * Reset to initial state
         */
        $this->validFields = array();
        $this->invalidMessages = array();
        $this->invalidErrors = array();
        $this->missingFields = array();
        $this->unknownFields = array();

        $this->processed = false;

        return $this;
    }

    /**
     * @param mixed $escapeFilter
     * @return FilterInterface
     */
    public function setDefaultEscapeFilter($escapeFilter)
    {
        if (is_string($escapeFilter) || is_array($escapeFilter)) {
            $escapeFilter = $this->_getFilter($escapeFilter);
        }
        if (!$escapeFilter instanceof FilterInterface) {
            throw new Exception\InvalidArgumentException(
                'Escape filter specified does not implement Zend\Filter\FilterInterface'
            );
        }
        $this->defaultEscapeFilter = $escapeFilter;
        return $escapeFilter;
    }

    /**
     * @param array $options
     * @return InputFilter
     * @throws Exception\ExceptionInterface if an unknown option is given
     */
    public function setOptions(array $options)
    {
        foreach ($options as $option => $value) {
            switch ($option) {
                case self::FILTER_BROKER:
                    $this->setPluginBroker($value, self::FILTER);
                    break;
                case self::VALIDATOR_BROKER:
                    $this->setPluginBroker($value, self::VALIDATOR);
                    break;
                case self::ESCAPE_FILTER:
                    $this->setDefaultEscapeFilter($value);
                    break;
                case self::ALLOW_EMPTY:
                case self::BREAK_CHAIN:
                case self::MISSING_MESSAGE:
                case self::NOT_EMPTY_MESSAGE:
                case self::PRESENCE:
                    $this->defaults[$option] = $value;
                    break;
                default:
                    // ignore unknown options
                    break;
            }
        }

        return $this;
    }

    /**
     * Set translation object
     *
     * @param  Zend_Translator|Zend\Translator\Adapter\Adapter|null $translator
     * @return InputFilter
     */
    public function setTranslator($translator = null)
    {
        if ((null === $translator) || ($translator instanceof TranslationAdapter)) {
            $this->translator = $translator;
        } elseif ($translator instanceof Translator) {
            $this->translator = $translator->getAdapter();
        } else {
            throw new Validator\Exception\InvalidArgumentException('Invalid translator specified');
        }

        return $this;
    }

    /**
     * Return translation object
     *
     * @return Zend\Translator\Adapter\Adapter|null
     */
    public function getTranslator()
    {
        if ($this->translatorIsDisabled()) {
            return null;
        }

        if ($this->translator === null) {
            if (\Zend\Registry::isRegistered('Zend_Translator')) {
                $translator = \Zend\Registry::get('Zend_Translator');
                if ($translator instanceof TranslationAdapter) {
                    return $translator;
                } elseif ($translator instanceof Translator) {
                    return $translator->getAdapter();
                }
            }
        }

        return $this->translator;
    }

    /**
     * Indicate whether or not translation should be disabled
     *
     * @param  bool $flag
     * @return InputFilter
     */
    public function setDisableTranslator($flag)
    {
        $this->translatorDisabled = (bool) $flag;
        return $this;
    }

    /**
     * Is translation disabled?
     *
     * @return bool
     */
    public function translatorIsDisabled()
    {
        return $this->translatorDisabled;
    }

    /*
     * Protected methods
     */

    /**
     * @return void
     */
    protected function _filter()
    {
        foreach ($this->filterRules as $ruleName => &$filterRule) {
            /**
             * Make sure we have an array representing this filter chain.
             * Don't typecast to (array) because it might be a Zend\Filter\Filter object
             */
            if (!is_array($filterRule)) {
                $filterRule = array($filterRule);
            }

            /**
             * Filters are indexed by integer, metacommands are indexed by string.
             * Pick out the filters.
             */
            $filterList = array();
            foreach ($filterRule as $key => $value) {
                if (is_int($key)) {
                    $filterList[] = $value;
                }
            }

            /**
             * Use defaults for filter metacommands.
             */
            $filterRule[self::RULE] = $ruleName;
            if (!isset($filterRule[self::FIELDS])) {
                $filterRule[self::FIELDS] = $ruleName;
            }

            /**
             * Load all the filter classes and add them to the chain.
             */
            if (!isset($filterRule[self::FILTER_CHAIN])) {
                $filterRule[self::FILTER_CHAIN] = new FilterChain();
                foreach ($filterList as $filter) {
                    if (is_string($filter) || is_array($filter)) {
                        $filter = $this->_getFilter($filter);
                    }
                    $filterRule[self::FILTER_CHAIN]->attach($filter);
                }
            }

            /**
             * If the ruleName is the special wildcard rule,
             * then apply the filter chain to all input data.
             * Else just process the field named by the rule.
             */
            if ($ruleName == self::RULE_WILDCARD) {
                foreach (array_keys($this->data) as $field)  {
                    $this->_filterRule(array_merge($filterRule, array(self::FIELDS => $field)));
                }
            } else {
                $this->_filterRule($filterRule);
            }
        }
    }

    /**
     * @param array $filterRule
     * @return void
     */
    protected function _filterRule(array $filterRule)
    {
        $field = $filterRule[self::FIELDS];
        if (!array_key_exists($field, $this->data)) {
            return;
        }
        if (is_array($this->data[$field])) {
            foreach ($this->data[$field] as $key => $value) {
                $filterChain = $filterRule[self::FILTER_CHAIN];
                $this->data[$field][$key] = $filterChain($value);
            }
        } else {
            $filterChain = $filterRule[self::FILTER_CHAIN];
            $this->data[$field] = $filterChain($this->data[$field]);
        }
    }

    /**
     * @return Zend\Filter\FilterInterface
     */
    protected function _getDefaultEscapeFilter()
    {
        if ($this->defaultEscapeFilter !== null) {
            return $this->defaultEscapeFilter;
        }
        return $this->setDefaultEscapeFilter($this->defaults[self::ESCAPE_FILTER]);
    }

    /**
     * @param string $rule
     * @param string $field
     * @return string
     */
    protected function _getMissingMessage($rule, $field)
    {
        $message = $this->defaults[self::MISSING_MESSAGE];

        if (null !== ($translator = $this->getTranslator())) {
            if ($translator->isTranslated(self::MISSING_MESSAGE)) {
                $message = $translator->translate(self::MISSING_MESSAGE);
            } else {
                $message = $translator->translate($message);
            }
        }

        $message = str_replace('%rule%', $rule, $message);
        $message = str_replace('%field%', $field, $message);
        return $message;
    }

    /**
     * @return string
     */
    protected function _getNotEmptyMessage($rule, $field)
    {
        $message = $this->defaults[self::NOT_EMPTY_MESSAGE];

        if (null !== ($translator = $this->getTranslator())) {
            if ($translator->isTranslated(self::NOT_EMPTY_MESSAGE)) {
                $message = $translator->translate(self::NOT_EMPTY_MESSAGE);
            } else {
                $message = $translator->translate($message);
            }
        }

        $message = str_replace('%rule%', $rule, $message);
        $message = str_replace('%field%', $field, $message);
        return $message;
    }

    /**
     * @return void
     */
    protected function _process()
    {
        if ($this->processed === false) {
            $this->_filter();
            $this->_validate();
            $this->processed = true;
        }
    }

    /**
     * @return void
     */
    protected function _validate()
    {
        /**
         * Special case: if there are no validators, treat all fields as valid.
         */
        if (!$this->validatorRules) {
            $this->validFields = $this->data;
            $this->data = array();
            return;
        }

        // remember the default not empty message in case we want to temporarily change it
        $preserveDefaultNotEmptyMessage = $this->defaults[self::NOT_EMPTY_MESSAGE];

        foreach ($this->validatorRules as $ruleName => &$validatorRule) {
            /**
             * Make sure we have an array representing this validator chain.
             * Don't typecast to (array) because it might be a Zend_Validator object
             */
            if (!is_array($validatorRule)) {
                $validatorRule = array($validatorRule);
            }

            /**
             * Validators are indexed by integer, metacommands are indexed by string.
             * Pick out the validators.
             */
            $validatorList = array();
            foreach ($validatorRule as $key => $value) {
                if (is_int($key)) {
                    $validatorList[$key] = $value;
                }
            }

            /**
             * Use defaults for validation metacommands.
             */
            $validatorRule[self::RULE] = $ruleName;
            if (!isset($validatorRule[self::FIELDS])) {
                $validatorRule[self::FIELDS] = $ruleName;
            }
            if (!isset($validatorRule[self::BREAK_CHAIN])) {
                $validatorRule[self::BREAK_CHAIN] = $this->defaults[self::BREAK_CHAIN];
            }
            if (!isset($validatorRule[self::PRESENCE])) {
                $validatorRule[self::PRESENCE] = $this->defaults[self::PRESENCE];
            }
            if (!isset($validatorRule[self::ALLOW_EMPTY])) {
                $foundNotEmptyValidator = false;

                foreach ($validatorRule as $rule) {
                    if ($rule === 'NotEmpty') {
                        $foundNotEmptyValidator = true;
                        // field may not be empty, we are ready
                        break 1;
                    }

                    if (is_array($rule)) {
                        $keys = array_keys($rule);
                        $classKey = array_shift($keys);
                        if (isset($rule[$classKey])) {
                            $ruleClass = $rule[$classKey];
                            if ($ruleClass === 'NotEmpty') {
                                $foundNotEmptyValidator = true;
                                // field may not be empty, we are ready
                                break 1;
                            }
                        }
                    }

                    // we must check if it is an object before using instanceof
                    if (!is_object($rule)) {
                        // it cannot be a NotEmpty validator, skip this one
                        continue;
                    }

                    if($rule instanceof Validator\NotEmpty) {
                        $foundNotEmptyValidator = true;
                        // field may not be empty, we are ready
                        break 1;
                    }
                }

                if (!$foundNotEmptyValidator) {
                    $validatorRule[self::ALLOW_EMPTY] = $this->defaults[self::ALLOW_EMPTY];
                } else {
                    $validatorRule[self::ALLOW_EMPTY] = false;
                }
            }

            if (!isset($validatorRule[self::MESSAGES])) {
                $validatorRule[self::MESSAGES] = array();
            } else if (!is_array($validatorRule[self::MESSAGES])) {
                $validatorRule[self::MESSAGES] = array($validatorRule[self::MESSAGES]);
            } else if (array_intersect_key($validatorList, $validatorRule[self::MESSAGES])) {
                // This seems pointless... it just re-adds what it already has...
                // I can disable all this and not a single unit test fails...
                // There are now corresponding numeric keys in the validation rule messages array
                // Treat it as a named messages list for all rule validators
                $unifiedMessages = $validatorRule[self::MESSAGES];
                $validatorRule[self::MESSAGES] = array();

                foreach ($validatorList as $key => $validator) {
                    if (array_key_exists($key, $unifiedMessages)) {
                        $validatorRule[self::MESSAGES][$key] = $unifiedMessages[$key];
                    }
                }
            }

            /**
             * Load all the validator classes and add them to the chain.
             */
            if (!isset($validatorRule[self::VALIDATOR_CHAIN])) {
                $validatorRule[self::VALIDATOR_CHAIN] = new Validator\ValidatorChain();

                foreach ($validatorList as $key => $validator) {
                    if (is_string($validator) || is_array($validator)) {
                        $validator = $this->_getValidator($validator);
                    }

                    if (isset($validatorRule[self::MESSAGES][$key])) {
                        $value = $validatorRule[self::MESSAGES][$key];
                        if (is_array($value)) {
                            $validator->setMessages($value);
                        } else {
                            $validator->setMessage($value);
                        }

                        if ($validator instanceof Validator\NotEmpty) {
                            /**
                             * We are changing the defaults here, this is alright if all subsequent validators are also a not empty
                             * validator, but it goes wrong if one of them is not AND is required!!!
                             * that is why we restore the default value at the end of this loop
                             */
                            if (is_array($value)) {
                                $temp = $value; // keep the original value
                                $this->defaults[self::NOT_EMPTY_MESSAGE] = array_pop($temp);
                                unset($temp);
                            } else {
                                $this->defaults[self::NOT_EMPTY_MESSAGE] = $value;
                            }
                        }
                    }

                    $validatorRule[self::VALIDATOR_CHAIN]->addValidator($validator, $validatorRule[self::BREAK_CHAIN]);
                }
                $validatorRule[self::VALIDATOR_CHAIN_COUNT] = count($validatorList);
            }

            /**
             * If the ruleName is the special wildcard rule,
             * then apply the validator chain to all input data.
             * Else just process the field named by the rule.
             */
            if ($ruleName == self::RULE_WILDCARD) {
                foreach (array_keys($this->data) as $field)  {
                    $this->_validateRule(array_merge($validatorRule, array(self::FIELDS => $field)));
                }
            } else {
                $this->_validateRule($validatorRule);
            }

            // Reset the default not empty message
            $this->defaults[self::NOT_EMPTY_MESSAGE] = $preserveDefaultNotEmptyMessage;
        }

        /**
         * Unset fields in $_data that have been added to other arrays.
         * We have to wait until all rules have been processed because
         * a given field may be referenced by multiple rules.
         */
        foreach (array_merge(array_keys($this->missingFields), array_keys($this->invalidMessages)) as $rule) {
            foreach ((array) $this->validatorRules[$rule][self::FIELDS] as $field) {
                unset($this->data[$field]);
            }
        }
        foreach ($this->validFields as $field => $value) {
            unset($this->data[$field]);
        }

        /**
         * Anything left over in $_data is an unknown field.
         */
        $this->unknownFields = $this->data;
    }

    /**
     * @param array $validatorRule
     * @return void
     */
    protected function _validateRule(array $validatorRule)
    {
        /**
         * Get one or more data values from input, and check for missing fields.
         * Apply defaults if fields are missing.
         */
        $data = array();
        foreach ((array) $validatorRule[self::FIELDS] as $key => $field) {
            if (array_key_exists($field, $this->data)) {
                $data[$field] = $this->data[$field];
            } else if (isset($validatorRule[self::DEFAULT_VALUE])) {
                /** @todo according to this code default value can't be an array. It has to be reviewed */
                if (!is_array($validatorRule[self::DEFAULT_VALUE])) {
                    // Default value is a scalar
                    $data[$field] = $validatorRule[self::DEFAULT_VALUE];
                } else {
                    // Default value is an array. Search for corresponding key
                    if (isset($validatorRule[self::DEFAULT_VALUE][$key])) {
                        $data[$field] = $validatorRule[self::DEFAULT_VALUE][$key];
                    } else if ($validatorRule[self::PRESENCE] == self::PRESENCE_REQUIRED) {
                        // Default value array is provided, but it doesn't have an entry for current field
                        // and presence is required
                        $this->missingFields[$validatorRule[self::RULE]][] =
                           $this->_getMissingMessage($validatorRule[self::RULE], $field);
                    }
                }
            } else if ($validatorRule[self::PRESENCE] == self::PRESENCE_REQUIRED) {
                $this->missingFields[$validatorRule[self::RULE]][] =
                    $this->_getMissingMessage($validatorRule[self::RULE], $field);
            }
        }

        /**
         * If any required fields are missing, break the loop.
         */
        if (isset($this->missingFields[$validatorRule[self::RULE]]) && count($this->missingFields[$validatorRule[self::RULE]]) > 0) {
            return;
        }

        /**
         * Evaluate the inputs against the validator chain.
         */
        if (count((array) $validatorRule[self::FIELDS]) > 1) {
            if (!$validatorRule[self::ALLOW_EMPTY]) {
                $emptyFieldsFound = false;
                $errorsList       = array();
                $messages         = array();

                foreach ($data as $fieldKey => $field) {
                    // If there is no Validator\NotEmpty instance in the rules, we will use the default
                    if (!($notEmptyValidator = $this->_getNotEmptyValidatorInstance($validatorRule))) {
                        $notEmptyValidator = $this->_getValidator('NotEmpty');
                        $notEmptyValidator->setMessage($this->_getNotEmptyMessage($validatorRule[self::RULE], $fieldKey));
                    }

                    if (!$notEmptyValidator->isValid($field)) {
                        foreach ($notEmptyValidator->getMessages() as $messageKey => $message) {
                            if (!isset($messages[$messageKey])) {
                                $messages[$messageKey] = $message;
                            } else {
                                $messages[] = $message;
                            }
                        }
                        $errorsList[] = array_keys($notEmptyValidator->getMessages());
                        $emptyFieldsFound = true;
                    }
                }

                if ($emptyFieldsFound) {
                    $this->invalidMessages[$validatorRule[self::RULE]] = $messages;
                    $this->invalidErrors[$validatorRule[self::RULE]]   = array_unique(call_user_func_array('array_merge', $errorsList));
                    return;
                }
            }

            if (!$validatorRule[self::VALIDATOR_CHAIN]->isValid($data)) {
                $this->invalidMessages[$validatorRule[self::RULE]] = $validatorRule[self::VALIDATOR_CHAIN]->getMessages();
                $this->invalidErrors[$validatorRule[self::RULE]] = $validatorRule[self::VALIDATOR_CHAIN]->getErrors();
                return;
            }
        } else if (count($data) > 0) {
            // $data is actually a one element array
            $fieldNames = array_keys($data);
            $fieldName = reset($fieldNames);
            $field     = reset($data);

            $failed = false;
            if (!is_array($field)) {
                $field = array($field);
            }

            // If there is no \Zend\Validator\NotEmpty instance in the rules, we will use the default
            if (!($notEmptyValidator = $this->_getNotEmptyValidatorInstance($validatorRule))) {
                $notEmptyValidator = $this->_getValidator('NotEmpty');
                $notEmptyValidator->setMessage($this->_getNotEmptyMessage($validatorRule[self::RULE], $fieldName));
            }

            if ($validatorRule[self::ALLOW_EMPTY]) {
                $validatorChain = $validatorRule[self::VALIDATOR_CHAIN];
            } else {
                $validatorChain = new Validator\ValidatorChain();
                $validatorChain->addValidator($notEmptyValidator, true /* Always break on failure */);
                $validatorChain->addValidator($validatorRule[self::VALIDATOR_CHAIN]);
            }

            foreach ($field as $key => $value) {
                if ($validatorRule[self::ALLOW_EMPTY]  &&  !$notEmptyValidator->isValid($value)) {
                    // Field is empty AND it's allowed. Do nothing.
                    continue;
                }

                if (!$validatorChain->isValid($value)) {
                    if (isset($this->invalidMessages[$validatorRule[self::RULE]])) {
                        $collectedMessages = $this->invalidMessages[$validatorRule[self::RULE]];
                    } else {
                        $collectedMessages = array();
                    }

                    foreach ($validatorChain->getMessages() as $messageKey => $message) {
                        if (!isset($collectedMessages[$messageKey])) {
                            $collectedMessages[$messageKey] = $message;
                        } else {
                            $collectedMessages[] = $message;
                        }
                    }

                    $this->invalidMessages[$validatorRule[self::RULE]] = $collectedMessages;
                    if (isset($this->invalidErrors[$validatorRule[self::RULE]])) {
                        $this->invalidErrors[$validatorRule[self::RULE]] = array_merge($this->invalidErrors[$validatorRule[self::RULE]],
                                                                                        array_keys($validatorChain->getMessages()));
                    } else {
                        $this->invalidErrors[$validatorRule[self::RULE]] = array_keys($validatorChain->getMessages());
                    }
                    unset($this->validFields[$fieldName]);
                    $failed = true;
                    if ($validatorRule[self::BREAK_CHAIN]) {
                        return;
                    }
                }
            }
            if ($failed) {
                return;
            }
        }

        /**
         * If we got this far, the inputs for this rule pass validation.
         */
        foreach ((array) $validatorRule[self::FIELDS] as $field) {
            if (array_key_exists($field, $data)) {
                $this->validFields[$field] = $data[$field];
            }
        }
    }

    /**
     * Check a validatorRule for the presence of a NotEmpty validator instance.
     * The purpose is to preserve things like a custom message, that may have been
     * set on the validator outside InputFilter.
     * @param  array $validatorRule
     * @return mixed False if none is found, \Zend\Validator\NotEmpty instance if found
     */
    protected function _getNotEmptyValidatorInstance($validatorRule) {
        foreach ($validatorRule as $rule => $value) {
            if (is_object($value) and $value instanceof Validator\NotEmpty) {
                return $value;
            }
        }

        return false;
    }

    /**
     * @param mixed $classBaseName
     * @return \Zend\Filter\FilterInterface
     */
    protected function _getFilter($classBaseName)
    {
        return $this->_getFilterOrValidator(self::FILTER, $classBaseName);
    }

    /**
     * @param mixed $classBaseName
     * @return \Zend\Validator\ValidatorInterface
     */
    protected function _getValidator($classBaseName)
    {
        return $this->_getFilterOrValidator(self::VALIDATOR, $classBaseName);
    }

    /**
     * @param string $type
     * @param mixed $classBaseName
     * @return \Zend\Filter\FilterInterface|\Zend\Validator\ValidatorInterface
     * @throws Exception\ExceptionInterface
     */
    protected function _getFilterOrValidator($type, $classBaseName)
    {
        $args = array();

        if (is_array($classBaseName)) {
            $args = $classBaseName;
            $classBaseName = array_shift($args);
        }

        return $this->getPluginBroker($type)->load($classBaseName, $args);
    }
}
