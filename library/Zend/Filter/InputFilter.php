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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Filter;

use Zend\Loader\PluginLoader,
    Zend\Loader\PrefixPathMapper,
    Zend\Loader\ShortNameLocater,
    Zend\Registry,
    Zend\Translator\Adapter as TranslationAdapter,
    Zend\Translator\Translator as Translator,
    Zend\Validator;

/**
 * @uses       ReflectionClass
 * @uses       Zend\Filter\Filter
 * @uses       Zend\Filter\Exception
 * @uses       Zend\Loader\PluginLoader
 * @uses       Zend\Registry
 * @uses       Zend\Validator\Validator
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
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
    const MISSING_MESSAGE       = 'missingMessage';
    const INPUT_NAMESPACE       = 'inputNamespace';
    const VALIDATOR_NAMESPACE   = 'validatorNamespace';
    const FILTER_NAMESPACE      = 'filterNamespace';
    const NOT_EMPTY_MESSAGE     = 'notEmptyMessage';
    const PRESENCE              = 'presence';
    const PRESENCE_OPTIONAL     = 'optional';
    const PRESENCE_REQUIRED     = 'required';
    const RULE                  = 'rule';
    const RULE_WILDCARD         = '*';
    const VALIDATOR             = 'validator';
    const VALIDATOR_CHAIN       = 'validatorChain';
    const VALIDATOR_CHAIN_COUNT = 'validatorChainCount';

    /**
     * @var array Input data, before processing.
     */
    protected $_data = array();

    /**
     * @var array Association of rules to filters.
     */
    protected $_filterRules = array();

    /**
     * @var array Association of rules to validators.
     */
    protected $_validatorRules = array();

    /**
     * @var array After processing data, this contains mapping of valid fields
     * to field values.
     */
    protected $_validFields = array();

    /**
     * @var array After processing data, this contains mapping of validation
     * rules that did not pass validation to the array of messages returned
     * by the validator chain.
     */
    protected $_invalidMessages = array();

    /**
     * @var array After processing data, this contains mapping of validation
     * rules that did not pass validation to the array of error identifiers
     * returned by the validator chain.
     */
    protected $_invalidErrors = array();

    /**
     * @var array After processing data, this contains mapping of validation
     * rules in which some fields were missing to the array of messages
     * indicating which fields were missing.
     */
    protected $_missingFields = array();

    /**
     * @var array After processing, this contains a copy of $_data elements
     * that were not mentioned in any validation rule.
     */
    protected $_unknownFields = array();

    /**
     * @var Zend\Filter\Filter The filter object that is run on values
     * returned by the getEscaped() method.
     */
    protected $_defaultEscapeFilter = null;

    /**
     * Plugin loaders
     * @var array
     */
    protected $_loaders = array();

    /**
     * @var array Default values to use when processing filters and validators.
     */
    protected $_defaults = array(
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
    protected $_processed = false;

    /**
     * Translation object
     * @var Zend\Translate\Translate
     */
    protected $_translator;

    /**
     * Is translation disabled?
     * @var Boolean
     */
    protected $_translatorDisabled = false;

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

        $this->_filterRules = (array) $filterRules;
        $this->_validatorRules = (array) $validatorRules;

        if ($data) {
            $this->setData($data);
        }
    }

    /**
     * @param mixed $namespaces
     * @return Zend\Filter\InputFilter
     * @deprecated since 1.5.0RC1 - use addFilterPrefixPath() or addValidatorPrefixPath instead.
     */
    public function addNamespace($namespaces)
    {
        if (!is_array($namespaces)) {
            $namespaces = array($namespaces);
        }

        foreach ($namespaces as $namespace) {
            $prefix = $namespace;
            $path = str_replace('\\', DIRECTORY_SEPARATOR, $prefix);
            $this->addFilterPrefixPath($prefix, $path);
            $this->addValidatorPrefixPath($prefix, $path);
        }

        return $this;
    }

    /**
     * Add prefix path for all elements
     *
     * @param  string $prefix
     * @param  string $path
     * @return Zend\Filter\InputFilter
     */
    public function addFilterPrefixPath($prefix, $path)
    {
        $pluginLoader = $this->getPluginLoader(self::FILTER);
        if ($pluginLoader instanceof PrefixPathMapper) {
            $pluginLoader->addPrefixPath($prefix, $path);
        }
        return $this;
    }

    /**
     * Add prefix path for all elements
     *
     * @param  string $prefix
     * @param  string $path
     * @return Zend\Filter\InputFilter
     */
    public function addValidatorPrefixPath($prefix, $path)
    {
        $pluginLoader = $this->getPluginLoader(self::VALIDATOR);
        if ($pluginLoader instanceof PrefixPathMapper) {
            $pluginLoader->addPrefixPath($prefix, $path);
        }
        return $this;
    }

    /**
     * Set plugin loaders for use with decorators and elements
     *
     * @param  Zend\Loader\ShortNameLocater $loader
     * @param  string $type 'filter' or 'validate'
     * @return Zend\Filter\InputFilter
     * @throws Zend\Filter\Exception on invalid type
     */
    public function setPluginLoader(ShortNameLocater $loader, $type)
    {
        $type = strtolower($type);
        switch ($type) {
            case self::FILTER:
            case self::VALIDATOR:
                $this->_loaders[$type] = $loader;
                return $this;
            default:
                throw new Exception(sprintf('Invalid type "%s" provided to setPluginLoader()', $type));
        }

        return $this;
    }

    /**
     * Retrieve plugin loader for given type
     *
     * $type may be one of:
     * - filter
     * - validator
     *
     * If a plugin loader does not exist for the given type, defaults are
     * created.
     *
     * @param  string $type 'filter' or 'validate'
     * @return Zend\Loader\ShortNameLocater
     * @throws Zend\Filter\Exception on invalid type
     */
    public function getPluginLoader($type)
    {
        $type = strtolower($type);
        if (!isset($this->_loaders[$type])) {
            switch ($type) {
                case self::FILTER:
                    $prefixSegment = 'Zend\\Filter\\';
                    $pathSegment   = 'Zend/Filter/';
                    break;
                case self::VALIDATOR:
                    $prefixSegment = 'Zend\\Validator\\';
                    $pathSegment   = 'Zend/Validator/';
                    break;
                default:
                    throw new Exception(sprintf('Invalid type "%s" provided to getPluginLoader()', $type));
            }

            $this->_loaders[$type] = new PluginLoader(
                array($prefixSegment => $pathSegment)
            );
        }

        return $this->_loaders[$type];
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        $this->_process();
        return array_merge($this->_invalidMessages, $this->_missingFields);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        $this->_process();
        return $this->_invalidErrors;
    }

    /**
     * @return array
     */
    public function getInvalid()
    {
        $this->_process();
        return $this->_invalidMessages;
    }

    /**
     * @return array
     */
    public function getMissing()
    {
        $this->_process();
        return $this->_missingFields;
    }

    /**
     * @return array
     */
    public function getUnknown()
    {
        $this->_process();
        return $this->_unknownFields;
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
            return $this->_escapeRecursive($this->_validFields);
        }
        if (array_key_exists($fieldName, $this->_validFields)) {
            return $this->_escapeRecursive($this->_validFields[$fieldName]);
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
            return $this->_validFields;
        }
        if (array_key_exists($fieldName, $this->_validFields)) {
            return $this->_validFields[$fieldName];
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
        return !(empty($this->_invalidMessages));
    }

    /**
     * @return boolean
     */
    public function hasMissing()
    {
        $this->_process();
        return !(empty($this->_missingFields));
    }

    /**
     * @return boolean
     */
    public function hasUnknown()
    {
        $this->_process();
        return !(empty($this->_unknownFields));
    }

    /**
     * @return boolean
     */
    public function hasValid()
    {
        $this->_process();
        return !(empty($this->_validFields));
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
        return array_key_exists($fieldName, $this->_validFields);
    }

    /**
     * @param string $fieldName
     * @return boolean
     */
    public function __isset($fieldName)
    {
        $this->_process();
        return isset($this->_validFields[$fieldName]);
    }

    /**
     * @return Zend\Filter\InputFilter
     * @throws Zend\Filter\Exception
     */
    public function process()
    {
        $this->_process();
        if ($this->hasInvalid()) {
            throw new Exception("Input has invalid fields");
        }
        if ($this->hasMissing()) {
            throw new Exception("Input has missing fields");
        }

        return $this;
    }

    /**
     * @param array $data
     * @return Zend\Filter\InputFilter
     */
    public function setData(array $data)
    {
        $this->_data = $data;

        /**
         * Reset to initial state
         */
        $this->_validFields = array();
        $this->_invalidMessages = array();
        $this->_invalidErrors = array();
        $this->_missingFields = array();
        $this->_unknownFields = array();

        $this->_processed = false;

        return $this;
    }

    /**
     * @param mixed $escapeFilter
     * @return Zend\Filter\Filter
     */
    public function setDefaultEscapeFilter($escapeFilter)
    {
        if (is_string($escapeFilter) || is_array($escapeFilter)) {
            $escapeFilter = $this->_getFilter($escapeFilter);
        }
        if (!$escapeFilter instanceof Filter) {
            throw new Exception('Escape filter specified does not implement Zend\\Filter\\Filter');
        }
        $this->_defaultEscapeFilter = $escapeFilter;
        return $escapeFilter;
    }

    /**
     * @param array $options
     * @return Zend\Filter\InputFilter
     * @throws Zend\Filter\Exception if an unknown option is given
     */
    public function setOptions(array $options)
    {
        foreach ($options as $option => $value) {
            switch ($option) {
                case self::ESCAPE_FILTER:
                    $this->setDefaultEscapeFilter($value);
                    break;
                case self::INPUT_NAMESPACE:
                    $this->addNamespace($value);
                    break;
                case self::VALIDATOR_NAMESPACE:
                    if(is_string($value)) {
                        $value = array($value);
                    }

                    foreach($value AS $prefix) {
                        $this->addValidatorPrefixPath(
                                $prefix,
                                str_replace('\\', DIRECTORY_SEPARATOR, $prefix)
                        );
                    }
                    break;
                case self::FILTER_NAMESPACE:
                    if(is_string($value)) {
                        $value = array($value);
                    }

                    foreach($value AS $prefix) {
                        $this->addFilterPrefixPath(
                                $prefix,
                                str_replace('\\', DIRECTORY_SEPARATOR, $prefix)
                        );
                    }
                    break;
                case self::ALLOW_EMPTY:
                case self::BREAK_CHAIN:
                case self::MISSING_MESSAGE:
                case self::NOT_EMPTY_MESSAGE:
                case self::PRESENCE:
                    $this->_defaults[$option] = $value;
                    break;
                default:
                    throw new Exception("Unknown option '$option'");
                    break;
            }
        }

        return $this;
    }

    /**
     * Set translation object
     *
     * @param  Zend_Translate|Zend\Translate\Adapter\Adapter|null $translator
     * @return Zend\Filter\InputFilter
     */
    public function setTranslator($translator = null)
    {
        if ((null === $translator) || ($translator instanceof TranslationAdapter)) {
            $this->_translator = $translator;
        } elseif ($translator instanceof Translator) {
            $this->_translator = $translator->getAdapter();
        } else {
            throw new Validator\Exception('Invalid translator specified');
        }

        return $this;
    }

    /**
     * Return translation object
     *
     * @return Zend\Translate\Adapter\Adapter|null
     */
    public function getTranslator()
    {
        if ($this->translatorIsDisabled()) {
            return null;
        }

        if ($this->_translator === null) {
            if (\Zend\Registry::isRegistered('Zend_Translate')) {
                $translator = \Zend\Registry::get('Zend_Translate');
                if ($translator instanceof TranslationAdapter) {
                    return $translator;
                } elseif ($translator instanceof Translator) {
                    return $translator->getAdapter();
                }
            }
        }

        return $this->_translator;
    }

    /**
     * Indicate whether or not translation should be disabled
     *
     * @param  bool $flag
     * @return Zend\Filter\InputFilter
     */
    public function setDisableTranslator($flag)
    {
        $this->_translatorDisabled = (bool) $flag;
        return $this;
    }

    /**
     * Is translation disabled?
     *
     * @return bool
     */
    public function translatorIsDisabled()
    {
        return $this->_translatorDisabled;
    }

    /*
     * Protected methods
     */

    /**
     * @return void
     */
    protected function _filter()
    {
        foreach ($this->_filterRules as $ruleName => &$filterRule) {
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
                    $filterRule[self::FILTER_CHAIN]->addFilter($filter);
                }
            }

            /**
             * If the ruleName is the special wildcard rule,
             * then apply the filter chain to all input data.
             * Else just process the field named by the rule.
             */
            if ($ruleName == self::RULE_WILDCARD) {
                foreach (array_keys($this->_data) as $field)  {
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
        if (!array_key_exists($field, $this->_data)) {
            return;
        }
        if (is_array($this->_data[$field])) {
            foreach ($this->_data[$field] as $key => $value) {
                $filterChain = $filterRule[self::FILTER_CHAIN];
                $this->_data[$field][$key] = $filterChain($value);
            }
        } else {
            $filterChain = $filterRule[self::FILTER_CHAIN];
            $this->_data[$field] = $filterChain($this->_data[$field]);
        }
    }

    /**
     * @return Zend\Filter\Filter
     */
    protected function _getDefaultEscapeFilter()
    {
        if ($this->_defaultEscapeFilter !== null) {
            return $this->_defaultEscapeFilter;
        }
        return $this->setDefaultEscapeFilter($this->_defaults[self::ESCAPE_FILTER]);
    }

    /**
     * @param string $rule
     * @param string $field
     * @return string
     */
    protected function _getMissingMessage($rule, $field)
    {
        $message = $this->_defaults[self::MISSING_MESSAGE];

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
        $message = $this->_defaults[self::NOT_EMPTY_MESSAGE];

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
        if ($this->_processed === false) {
            $this->_filter();
            $this->_validate();
            $this->_processed = true;
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
        if (!$this->_validatorRules) {
            $this->_validFields = $this->_data;
            $this->_data = array();
            return;
        }

        foreach ($this->_validatorRules as $ruleName => &$validatorRule) {
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
                $validatorRule[self::BREAK_CHAIN] = $this->_defaults[self::BREAK_CHAIN];
            }
            if (!isset($validatorRule[self::PRESENCE])) {
                $validatorRule[self::PRESENCE] = $this->_defaults[self::PRESENCE];
            }
            if (!isset($validatorRule[self::ALLOW_EMPTY])) {
                $validatorRule[self::ALLOW_EMPTY] = $this->_defaults[self::ALLOW_EMPTY];
            }

            if (!isset($validatorRule[self::MESSAGES])) {
                $validatorRule[self::MESSAGES] = array();
            } else if (!is_array($validatorRule[self::MESSAGES])) {
                $validatorRule[self::MESSAGES] = array($validatorRule[self::MESSAGES]);
            } else if (array_intersect_key($validatorList, $validatorRule[self::MESSAGES])) {
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
                            $this->_defaults[self::NOT_EMPTY_MESSAGE] = $value;
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
                foreach (array_keys($this->_data) as $field)  {
                    $this->_validateRule(array_merge($validatorRule, array(self::FIELDS => $field)));
                }
            } else {
                $this->_validateRule($validatorRule);
            }
        }

        /**
         * Unset fields in $_data that have been added to other arrays.
         * We have to wait until all rules have been processed because
         * a given field may be referenced by multiple rules.
         */
        foreach (array_merge(array_keys($this->_missingFields), array_keys($this->_invalidMessages)) as $rule) {
            foreach ((array) $this->_validatorRules[$rule][self::FIELDS] as $field) {
                unset($this->_data[$field]);
            }
        }
        foreach ($this->_validFields as $field => $value) {
            unset($this->_data[$field]);
        }

        /**
         * Anything left over in $_data is an unknown field.
         */
        $this->_unknownFields = $this->_data;
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
            if (array_key_exists($field, $this->_data)) {
                $data[$field] = $this->_data[$field];
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
                        $this->_missingFields[$validatorRule[self::RULE]][] =
                           $this->_getMissingMessage($validatorRule[self::RULE], $field);
                    }
                }
            } else if ($validatorRule[self::PRESENCE] == self::PRESENCE_REQUIRED) {
                $this->_missingFields[$validatorRule[self::RULE]][] =
                    $this->_getMissingMessage($validatorRule[self::RULE], $field);
            }
        }

        /**
         * If any required fields are missing, break the loop.
         */
        if (isset($this->_missingFields[$validatorRule[self::RULE]]) && count($this->_missingFields[$validatorRule[self::RULE]]) > 0) {
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
                    $notEmptyValidator = $this->_getValidator('NotEmpty');
                    $notEmptyValidator->setMessage($this->_getNotEmptyMessage($validatorRule[self::RULE], $fieldKey));

                    if (!$notEmptyValidator->isValid($field)) {
                        foreach ($notEmptyValidator->getMessages() as $messageKey => $message) {
                            if (!isset($messages[$messageKey])) {
                                $messages[$messageKey] = $message;
                            } else {
                                $messages[] = $message;
                            }
                        }
                        $errorsList[] = $notEmptyValidator->getErrors();
                        $emptyFieldsFound = true;
                    }
                }

                if ($emptyFieldsFound) {
                    $this->_invalidMessages[$validatorRule[self::RULE]] = $messages;
                    $this->_invalidErrors[$validatorRule[self::RULE]]   = array_unique(call_user_func_array('array_merge', $errorsList));
                    return;
                }
            }

            if (!$validatorRule[self::VALIDATOR_CHAIN]->isValid($data)) {
                $this->_invalidMessages[$validatorRule[self::RULE]] = $validatorRule[self::VALIDATOR_CHAIN]->getMessages();
                $this->_invalidErrors[$validatorRule[self::RULE]] = $validatorRule[self::VALIDATOR_CHAIN]->getErrors();
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

            $notEmptyValidator = $this->_getValidator('NotEmpty');
            $notEmptyValidator->setMessage($this->_getNotEmptyMessage($validatorRule[self::RULE], $fieldName));
            if ($validatorRule[self::ALLOW_EMPTY]) {
                $validatorChain = $validatorRule[self::VALIDATOR_CHAIN];
            } else {
                $validatorChain = new Validator\ValidatorChain();
                $validatorChain->addValidator($notEmptyValidator, true /* Always break on failure */);
                $validatorChain->addValidator($validatorRule[self::VALIDATOR_CHAIN]);
            }

            foreach ($field as $value) {
                if ($validatorRule[self::ALLOW_EMPTY]  &&  !$notEmptyValidator->isValid($value)) {
                    // Field is empty AND it's allowed. Do nothing.
                    continue;
                }

                if (!$validatorChain->isValid($value)) {
                    if (isset($this->_invalidMessages[$validatorRule[self::RULE]])) {
                        $collectedMessages = $this->_invalidMessages[$validatorRule[self::RULE]];
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

                    $this->_invalidMessages[$validatorRule[self::RULE]] = $collectedMessages;
                    if (isset($this->_invalidErrors[$validatorRule[self::RULE]])) {
                        $this->_invalidErrors[$validatorRule[self::RULE]] = array_merge($this->_invalidErrors[$validatorRule[self::RULE]],
                                                                                        $validatorChain->getErrors());
                    } else {
                        $this->_invalidErrors[$validatorRule[self::RULE]] = $validatorChain->getErrors();
                    }
                    unset($this->_validFields[$fieldName]);
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
                $this->_validFields[$field] = $data[$field];
            }
        }
    }

    /**
     * @param mixed $classBaseName
     * @return Zend\Filter\Filter
     */
    protected function _getFilter($classBaseName)
    {
        return $this->_getFilterOrValidator(self::FILTER, $classBaseName);
    }

    /**
     * @param mixed $classBaseName
     * @return Zend\Validator\Validator
     */
    protected function _getValidator($classBaseName)
    {
        return $this->_getFilterOrValidator(self::VALIDATOR, $classBaseName);
    }

    /**
     * @param string $type
     * @param mixed $classBaseName
     * @return Zend\Filter\Filter|Zend\Validator\Validator
     * @throws Zend\Filter\Exception
     */
    protected function _getFilterOrValidator($type, $classBaseName)
    {
        $args = array();

        if (is_array($classBaseName)) {
            $args = $classBaseName;
            $classBaseName = array_shift($args);
        }

        $interfaceType = ucfirst($type);
        $interfaceName = 'Zend\\' . $interfaceType . '\\' . $interfaceType;
        $className = $this->getPluginLoader($type)->load(ucfirst($classBaseName));

        $class = new \ReflectionClass($className);

        if (!$class->implementsInterface($interfaceName)) {
            throw new Exception("Class '$className' based on basename '$classBaseName' must implement the '$interfaceName' interface");
        }

        if ($class->hasMethod('__construct')) {
            $object = $class->newInstanceArgs($args);
        } else {
            $object = $class->newInstance();
        }

        return $object;
    }

}
