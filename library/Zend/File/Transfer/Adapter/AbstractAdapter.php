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
 * @category  Zend
 * @package   Zend_File_Transfer
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\File\Transfer\Adapter;

use Zend\File\Transfer,
    Zend\File\Transfer\Exception,
    Zend\Filter,
    Zend\Filter\Exception as FilterException,
    Zend\Loader,
    Zend\Translator,
    Zend\Translator\Adapter as AdapterTranslator,
    Zend\Validator;

/**
 * Abstract class for file transfers (Downloads and Uploads)
 *
 * This class needs a full rewrite. It re-implements functionality present in
 * Zend\Filter\Input and/or Zend\Form\Element, and in a way that's inconsistent
 * with either one. Additionally, plugin loader usage is now deprecated -- but
 * modifying that should be done in tandem with a rewrite to utilize validator
 * and filter chains instead.
 *
 * @todo      Rewrite
 * @category  Zend
 * @package   Zend_File_Transfer
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractAdapter
{
    /**@+
     * Plugin loader Constants
     */
    const FILTER    = 'FILTER';
    const VALIDATOR = 'VALIDATOR';
    /**@-*/

    /**
     * Internal list of breaks
     *
     * @var array
     */
    protected $break = array();

    /**
     * Internal list of filters
     *
     * @var array
     */
    protected $filters = array();

    /**
     * Plugin loaders for filter and validation chains
     *
     * @var array
     */
    protected $loaders = array();

    /**
     * Internal list of messages
     *
     * @var array
     */
    protected $messages = array();

    /**
     * @var AdapterTranslator\AbstractAdapter
     */
    protected $translator;

    /**
     * Is translation disabled?
     *
     * @var bool
     */
    protected $translatorDisabled = false;

    /**
     * Internal list of validators
     * @var array
     */
    protected $validators = array();

    /**
     * Internal list of files
     * This array looks like this:
     *     array(form => array( - Form is the name within the form or, if not set the filename
     *         name,            - Original name of this file
     *         type,            - Mime type of this file
     *         size,            - Filesize in bytes
     *         tmp_name,        - Internally temporary filename for uploaded files
     *         error,           - Error which has occured
     *         destination,     - New destination for this file
     *         validators,      - Set validator names for this file
     *         files            - Set file names for this file
     *     ))
     *
     * @var array
     */
    protected $files = array();

    /**
     * TMP directory
     * @var string
     */
    protected $tmpDir;

    /**
     * Available options for file transfers
     */
    protected $options = array(
        'ignoreNoFile'  => false,
        'useByteString' => true,
        'magicFile'     => null,
        'detectInfos'   => true,
    );

    /**
     * Send file
     *
     * @param  mixed $options
     * @return bool
     */
    abstract public function send($options = null);

    /**
     * Receive file
     *
     * @param  mixed $options
     * @return bool
     */
    abstract public function receive($options = null);

    /**
     * Is file sent?
     *
     * @param  array|string|null $files
     * @return bool
     */
    abstract public function isSent($files = null);

    /**
     * Is file received?
     *
     * @param  array|string|null $files
     * @return bool
     */
    abstract public function isReceived($files = null);

    /**
     * Has a file been uploaded ?
     *
     * @param  array|string|null $files
     * @return bool
     */
    abstract public function isUploaded($files = null);

    /**
     * Has the file been filtered ?
     *
     * @param array|string|null $files
     * @return bool
     */
    abstract public function isFiltered($files = null);

    /**
     * Adds one or more files
     *
     * @param  string|array $file      File to add
     * @param  string|array $validator Validators to use for this file, must be set before
     * @param  string|array $filter    Filters to use for this file, must be set before
     * @return AbstractAdapter
     * @throws Exception Not implemented
     */
    //abstract public function addFile($file, $validator = null, $filter = null);

    /**
     * Returns all set types
     *
     * @return array List of set types
     * @throws Exception Not implemented
     */
    //abstract public function getType();

    /**
     * Adds one or more type of files
     *
     * @param  string|array $type Type of files to add
     * @param  string|array $validator Validators to use for this file, must be set before
     * @param  string|array $filter    Filters to use for this file, must be set before
     * @return AbstractAdapter
     * @throws Exception Not implemented
     */
    //abstract public function addType($type, $validator = null, $filter = null);

    /**
     * Returns all set files
     *
     * @return array List of set files
     */
    //abstract public function getFile();

    /**
     * Set plugin loader to use for validator or filter chain
     *
     * @param  Loader\ShortNameLocator $loader
     * @param  string                  $type 'filter', or 'validator'
     * @return AbstractAdapter
     * @throws Exception\InvalidArgumentException on invalid type
     */
    public function setPluginLoader(Loader\ShortNameLocator $loader, $type)
    {
        $type = strtoupper($type);
        switch ($type) {
            case self::FILTER:
            case self::VALIDATOR:
                $this->loaders[$type] = $loader;
                return $this;
            default:
                throw new Exception\InvalidArgumentException(sprintf('Invalid type "%s" provided to setPluginLoader()', $type));
        }
    }

    /**
     * Retrieve plugin loader for validator or filter chain
     *
     * Instantiates with default rules if none available for that type. Use
     * 'filter' or 'validator' for $type.
     *
     * @param  string $type
     * @return Loader\ShortNameLocator
     * @throws Exception\InvalidArgumentException on invalid type.
     */
    public function getPluginLoader($type)
    {
        $type = strtoupper($type);
        switch ($type) {
            case self::FILTER:
            case self::VALIDATOR:
                $prefixSegment = ucfirst(strtolower($type));
                $pathSegment   = $prefixSegment;
                if (!isset($this->loaders[$type])) {
                    $paths         = array(
                        'Zend\\' . $prefixSegment . '\\'    => 'Zend/' . $pathSegment . '/',
                        'Zend\\' . $prefixSegment . '\File' => 'Zend/' . $pathSegment . '/File',
                    );

                    $this->loaders[$type] = new Loader\PrefixPathLoader($paths);
                } else {
                    $loader = $this->loaders[$type];
                    if ($loader instanceof Loader\PrefixPathMapper) {
                        $prefix = 'Zend\\' . $prefixSegment . '\File\\';
                        if (!$loader->getPaths($prefix)) {
                            $loader->addPrefixPath($prefix, str_replace('_', '/', $prefix));
                        }
                    }
                }

                return $this->loaders[$type];
            default:
                throw new Exception\InvalidArgumentException(sprintf('Invalid type "%s" provided to getPluginLoader()', $type));
        }
    }

    /**
     * Add prefix path for plugin loader
     *
     * If no $type specified, assumes it is a base path for both filters and
     * validators, and sets each according to the following rules:
     * - filters:    $prefix = $prefix . '_Filter'
     * - validators: $prefix = $prefix . '_Validator'
     *
     * Otherwise, the path prefix is set on the appropriate plugin loader.
     *
     * @param  string $prefix
     * @param  string $path
     * @param  string $type
     * @return AbstractAdapter
     * @throws Exception\InvalidArgumentException for invalid type
     */
    public function addPrefixPath($prefix, $path, $type = null)
    {
        $type = (null === $type) ? null : strtoupper($type);
        switch ($type) {
            case self::FILTER:
            case self::VALIDATOR:
                $loader = $this->getPluginLoader($type);
                if ($loader instanceof Loader\PrefixPathMapper) {
                    $loader->addPrefixPath($prefix, $path);
                }

                return $this;
            case null:
                $prefix = rtrim($prefix, '\\');
                $path   = rtrim($path, DIRECTORY_SEPARATOR);
                foreach (array(self::FILTER, self::VALIDATOR) as $type) {
                    $loader       = $this->getPluginLoader($type);
                    if ($loader instanceof Loader\PrefixPathMapper) {
                        $cType        = ucfirst(strtolower($type));
                        $pluginPath   = $path . DIRECTORY_SEPARATOR . $cType . DIRECTORY_SEPARATOR;
                        $pluginPrefix = $prefix . '\\' . $cType;
                        $loader->addPrefixPath($pluginPrefix, $pluginPath);
                    }
                }

                return $this;
            default:
                throw new Exception\InvalidArgumentException(sprintf('Invalid type "%s" provided to getPluginLoader()', $type));
        }
    }

    /**
     * Add many prefix paths at once
     *
     * @param  array $spec
     * @return AbstractAdapter
     */
    public function addPrefixPaths(array $spec)
    {
        if (isset($spec['prefix']) && isset($spec['path'])) {
            return $this->addPrefixPath($spec['prefix'], $spec['path']);
        }
        foreach ($spec as $type => $paths) {
            if (is_numeric($type) && is_array($paths)) {
                $type = null;
                if (isset($paths['prefix']) && isset($paths['path'])) {
                    if (isset($paths['type'])) {
                        $type = $paths['type'];
                    }
                    $this->addPrefixPath($paths['prefix'], $paths['path'], $type);
                }
            } elseif (!is_numeric($type)) {
                if (!isset($paths['prefix']) || !isset($paths['path'])) {
                    foreach ($paths as $prefix => $spec) {
                        if (is_array($spec)) {
                            foreach ($spec as $path) {
                                if (!is_string($path)) {
                                    continue;
                                }
                                $this->addPrefixPath($prefix, $path, $type);
                            }
                        } elseif (is_string($spec)) {
                            $this->addPrefixPath($prefix, $spec, $type);
                        }
                    }
                } else {
                    $this->addPrefixPath($paths['prefix'], $paths['path'], $type);
                }
            }
        }

        return $this;
    }

    /**
     * Adds a new validator for this class
     *
     * @param  string|Validator\ValidatorInterface $validator           Type of validator to add
     * @param  boolean                    $breakChainOnFailure If the validation chain should stop an failure
     * @param  string|array               $options             Options to set for the validator
     * @param  string|array               $files               Files to limit this validator to
     * @return AbstractAdapter
     * @throws Exception\InvalidArgumentException for invalid type
     */
    public function addValidator($validator, $breakChainOnFailure = false, $options = null, $files = null)
    {
        if ($validator instanceof Validator\ValidatorInterface) {
            $name = get_class($validator);
        } elseif (is_string($validator)) {
            $name      = $this->getPluginLoader(self::VALIDATOR)->load($validator);
            $validator = new $name($options);
            if (is_array($options) && isset($options['messages'])) {
                if (is_array($options['messages'])) {
                    $validator->setMessages($options['messages']);
                } elseif (is_string($options['messages'])) {
                    $validator->setMessage($options['messages']);
                }

                unset($options['messages']);
            }
        } else {
            throw new Exception\InvalidArgumentException('Invalid validator provided to addValidator; must be string or Zend\Validator\ValidatorInterface');
        }

        $this->validators[$name] = $validator;
        $this->break[$name]      = $breakChainOnFailure;
        $files                    = $this->getFiles($files, true, true);
        foreach ($files as $file) {
            if ($name == 'NotEmpty') {
                $temp = $this->files[$file]['validators'];
                $this->files[$file]['validators']  = array($name);
                $this->files[$file]['validators'] += $temp;
            } else {
                $this->files[$file]['validators'][] = $name;
            }

            $this->files[$file]['validated']    = false;
        }

        return $this;
    }

    /**
     * Add Multiple validators at once
     *
     * @param  array        $validators
     * @param  string|array $files
     * @return AbstractAdapter
     * @throws Exception\InvalidArgumentException for invalid type
     */
    public function addValidators(array $validators, $files = null)
    {
        foreach ($validators as $name => $validatorInfo) {
            if ($validatorInfo instanceof Validator\ValidatorInterface) {
                $this->addValidator($validatorInfo, null, null, $files);
            } else if (is_string($validatorInfo)) {
                if (!is_int($name)) {
                    $this->addValidator($name, null, $validatorInfo, $files);
                } else {
                    $this->addValidator($validatorInfo, null, null, $files);
                }
            } else if (is_array($validatorInfo)) {
                $argc                = count($validatorInfo);
                $breakChainOnFailure = false;
                $options             = array();
                if (isset($validatorInfo['validator'])) {
                    $validator = $validatorInfo['validator'];
                    if (isset($validatorInfo['breakChainOnFailure'])) {
                        $breakChainOnFailure = $validatorInfo['breakChainOnFailure'];
                    }

                    if (isset($validatorInfo['options'])) {
                        $options = $validatorInfo['options'];
                    }

                    $this->addValidator($validator, $breakChainOnFailure, $options, $files);
                } else {
                    if (is_string($name)) {
                        $validator = $name;
                        $options   = $validatorInfo;
                        $this->addValidator($validator, $breakChainOnFailure, $options, $files);
                    } else {
                        $file = $files;
                        switch (true) {
                            case (0 == $argc):
                                break;
                            case (1 <= $argc):
                                $validator  = array_shift($validatorInfo);
                            case (2 <= $argc):
                                $breakChainOnFailure = array_shift($validatorInfo);
                            case (3 <= $argc):
                                $options = array_shift($validatorInfo);
                            case (4 <= $argc):
                                if (!empty($validatorInfo)) {
                                    $file = array_shift($validatorInfo);
                                }
                            default:
                                $this->addValidator($validator, $breakChainOnFailure, $options, $file);
                                break;
                        }
                    }
                }
            } else {
                throw new Exception\InvalidArgumentException('Invalid validator passed to addValidators()');
            }
        }

        return $this;
    }

    /**
     * Sets a validator for the class, erasing all previous set
     *
     * @param  array        $validators Validators to set
     * @param  string|array $files      Files to limit this validator to
     * @return AbstractAdapter
     */
    public function setValidators(array $validators, $files = null)
    {
        $this->clearValidators();
        return $this->addValidators($validators, $files);
    }

    /**
     * Determine if a given validator has already been registered
     *
     * @param  string $name
     * @return bool
     */
    public function hasValidator($name)
    {
        return (false !== $this->getValidatorIdentifier($name));
    }

    /**
     * Retrieve individual validator
     *
     * @param  string $name
     * @return Validator\ValidatorInterface|null
     */
    public function getValidator($name)
    {
        if (false === ($identifier = $this->getValidatorIdentifier($name))) {
            return null;
        }
        return $this->validators[$identifier];
    }

    /**
     * Returns all set validators
     *
     * @param  string|array $files (Optional) Returns the validator for this files
     * @return null|array List of set validators
     */
    public function getValidators($files = null)
    {
        if ($files == null) {
            return $this->validators;
        }

        $files      = $this->getFiles($files, true, true);
        $validators = array();
        foreach ($files as $file) {
            if (!empty($this->files[$file]['validators'])) {
                $validators += $this->files[$file]['validators'];
            }
        }

        $validators = array_unique($validators);
        $result     = array();
        foreach ($validators as $validator) {
            $result[$validator] = $this->validators[$validator];
        }

        return $result;
    }

    /**
     * Remove an individual validator
     *
     * @param  string $name
     * @return AbstractAdapter
     */
    public function removeValidator($name)
    {
        if (false === ($key = $this->getValidatorIdentifier($name))) {
            return $this;
        }

        unset($this->validators[$key]);
        foreach (array_keys($this->files) as $file) {
            if (empty($this->files[$file]['validators'])) {
                continue;
            }

            $index = array_search($key, $this->files[$file]['validators']);
            if ($index === false) {
                continue;
            }

            unset($this->files[$file]['validators'][$index]);
            $this->files[$file]['validated'] = false;
        }

        return $this;
    }

    /**
     * Remove all validators
     *
     * @return AbstractAdapter
     */
    public function clearValidators()
    {
        $this->validators = array();
        foreach (array_keys($this->files) as $file) {
            $this->files[$file]['validators'] = array();
            $this->files[$file]['validated']  = false;
        }

        return $this;
    }

    /**
     * Sets Options for adapters
     *
     * @param array $options Options to set
     * @param array $files   (Optional) Files to set the options for
     */
    public function setOptions($options = array(), $files = null) {
        $file = $this->getFiles($files, false, true);

        if (is_array($options)) {
            if (empty($file)) {
                $this->options = array_merge($this->options, $options);
            }

            foreach ($options as $name => $value) {
                foreach ($file as $key => $content) {
                    switch ($name) {
                        case 'magicFile' :
                            $this->files[$key]['options'][$name] = (string) $value;
                            break;

                        case 'ignoreNoFile' :
                        case 'useByteString' :
                        case 'detectInfos' :
                            $this->files[$key]['options'][$name] = (boolean) $value;
                            break;

                        default:
                            continue;
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Returns set options for adapters or files
     *
     * @param  array $files (Optional) Files to return the options for
     * @return array Options for given files
     */
    public function getOptions($files = null) {
        $file = $this->getFiles($files, false, true);

        foreach ($file as $key => $content) {
            if (isset($this->files[$key]['options'])) {
                $options[$key] = $this->files[$key]['options'];
            } else {
                $options[$key] = array();
            }
        }

        return $options;
    }

    /**
     * Checks if the files are valid
     *
     * @param  string|array $files (Optional) Files to check
     * @return boolean True if all checks are valid
     */
    public function isValid($files = null)
    {
        $check = $this->getFiles($files, false, true);
        if (empty($check)) {
            return false;
        }

        $translator      = $this->getTranslator();
        $this->messages = array();
        $break           = false;
        foreach($check as $key => $content) {
            if (array_key_exists('validators', $content) &&
                in_array('Zend\Validator\File\Count', $content['validators'])) {
                $validator = $this->validators['Zend\Validator\File\Count'];
                $count     = $content;
                if (empty($content['tmp_name'])) {
                    continue;
                }

                if (array_key_exists('destination', $content)) {
                    $checkit = $content['destination'];
                } else {
                    $checkit = dirname($content['tmp_name']);
                }

                $checkit .= DIRECTORY_SEPARATOR . $content['name'];
                    $validator->addFile($checkit);
            }
        }

        if (isset($count)) {
            if (!$validator->isValid($count['tmp_name'], $count)) {
                $this->messages += $validator->getMessages();
            }
        }

        foreach ($check as $key => $content) {
            $fileerrors  = array();
            if (array_key_exists('validators', $content) && $content['validated']) {
                continue;
            }

            if (array_key_exists('validators', $content)) {
                foreach ($content['validators'] as $class) {
                    $validator = $this->validators[$class];
                    if (method_exists($validator, 'setTranslator')) {
                        $validator->setTranslator($translator);
                    }

                    if (($class === 'Zend\Validator\File\Upload') and (empty($content['tmp_name']))) {
                        $tocheck = $key;
                    } else {
                        $tocheck = $content['tmp_name'];
                    }

                    if (!$validator->isValid($tocheck, $content)) {
                        $fileerrors += $validator->getMessages();
                    }

                    if (!empty($content['options']['ignoreNoFile']) and (isset($fileerrors['fileUploadErrorNoFile']))) {
                        unset($fileerrors['fileUploadErrorNoFile']);
                        break;
                    }

                    if (($class === 'Zend\Validator\File\Upload') and (count($fileerrors) > 0)) {
                        break;
                    }

                    if (($this->break[$class]) and (count($fileerrors) > 0)) {
                        $break = true;
                        break;
                    }
                }
            }

            if (count($fileerrors) > 0) {
                $this->files[$key]['validated'] = false;
            } else {
                $this->files[$key]['validated'] = true;
            }

            $this->messages += $fileerrors;
            if ($break) {
                break;
            }
        }

        if (count($this->messages) > 0) {
            return false;
        }

        return true;
    }

    /**
     * Returns found validation messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Retrieve error codes
     *
     * @return array
     */
    public function getErrors()
    {
        return array_keys($this->messages);
    }

    /**
     * Are there errors registered?
     *
     * @return boolean
     */
    public function hasErrors()
    {
        return (!empty($this->messages));
    }

    /**
     * Adds a new filter for this class
     *
     * @param  string|Filter\FilterInterface $filter Type of filter to add
     * @param  string|array         $options   Options to set for the filter
     * @param  string|array         $files     Files to limit this filter to
     * @return AbstractAdapter
     * @throws Exception\InvalidArgumentException for invalid type
     */
    public function addFilter($filter, $options = null, $files = null)
    {
        if ($filter instanceof Filter\FilterInterface) {
            $class = get_class($filter);
        } elseif (is_string($filter)) {
            $class  = $this->getPluginLoader(self::FILTER)->load($filter);
            $filter = new $class($options);
        } else {
            throw new Exception\InvalidArgumentException('Invalid filter specified');
        }

        $this->filters[$class] = $filter;
        $files                  = $this->getFiles($files, true, true);
        foreach ($files as $file) {
            $this->files[$file]['filters'][] = $class;
        }

        return $this;
    }

    /**
     * Add Multiple filters at once
     *
     * @param  array $filters
     * @param  string|array $files
     * @return AbstractAdapter
     */
    public function addFilters(array $filters, $files = null)
    {
        foreach ($filters as $key => $spec) {
            if ($spec instanceof Filter\FilterInterface) {
                $this->addFilter($spec, null, $files);
                continue;
            }

            if (is_string($key)) {
                $this->addFilter($key, $spec, $files);
                continue;
            }

            if (is_int($key)) {
                if (is_string($spec)) {
                    $this->addFilter($spec, null, $files);
                    continue;
                }

                if (is_array($spec)) {
                    if (!array_key_exists('filter', $spec)) {
                        continue;
                    }

                    $filter = $spec['filter'];
                    unset($spec['filter']);
                    $this->addFilter($filter, $spec, $files);
                    continue;
                }

                continue;
            }
        }

        return $this;
    }

    /**
     * Sets a filter for the class, erasing all previous set
     *
     * @param  array        $filters Filter to set
     * @param  string|array $files   Files to limit this filter to
     * @return Filter\AbstractFilter
     */
    public function setFilters(array $filters, $files = null)
    {
        $this->clearFilters();
        return $this->addFilters($filters, $files);
    }

    /**
     * Determine if a given filter has already been registered
     *
     * @param  string $name
     * @return bool
     */
    public function hasFilter($name)
    {
        return (false !== $this->getFilterIdentifier($name));
    }

    /**
     * Retrieve individual filter
     *
     * @param  string $name
     * @return Filter\FilterInterface|null
     */
    public function getFilter($name)
    {
        if (false === ($identifier = $this->getFilterIdentifier($name))) {
            return null;
        }

        return $this->filters[$identifier];
    }

    /**
     * Returns all set filters
     *
     * @param  string|array $files (Optional) Returns the filter for this files
     * @return array List of set filters
     * @throws Exception\RuntimeException When file not found
     */
    public function getFilters($files = null)
    {
        if ($files === null) {
            return $this->filters;
        }

        $files   = $this->getFiles($files, true, true);
        $filters = array();
        foreach ($files as $file) {
            if (!empty($this->files[$file]['filters'])) {
                $filters += $this->files[$file]['filters'];
            }
        }

        $filters = array_unique($filters);
        $result  = array();
        foreach ($filters as $filter) {
            $result[] = $this->filters[$filter];
        }

        return $result;
    }

    /**
     * Remove an individual filter
     *
     * @param  string $name
     * @return AbstractAdapter
     */
    public function removeFilter($name)
    {
        if (false === ($key = $this->getFilterIdentifier($name))) {
            return $this;
        }

        unset($this->filters[$key]);
        foreach (array_keys($this->files) as $file) {
            if (empty($this->files[$file]['filters'])) {
                continue;
            }

            $index = array_search($key, $this->files[$file]['filters']);
            if ($index === false) {
                continue;
            }

            unset($this->files[$file]['filters'][$index]);
        }
        return $this;
    }

    /**
     * Remove all filters
     *
     * @return AbstractAdapter
     */
    public function clearFilters()
    {
        $this->filters = array();
        foreach (array_keys($this->files) as $file) {
            $this->files[$file]['filters'] = array();
        }

        return $this;
    }

    /**
     * Retrieves the filename of transferred files.
     *
     * @param  string  $fileelement (Optional) Element to return the filename for
     * @param  boolean $path        (Optional) Should the path also be returned ?
     * @return string|array
     */
    public function getFileName($file = null, $path = true)
    {
        $files     = $this->getFiles($file, true, true);
        $result    = array();
        $directory = "";
        foreach($files as $file) {
            if (empty($this->files[$file]['name'])) {
                continue;
            }

            if ($path === true) {
                $directory = $this->getDestination($file) . DIRECTORY_SEPARATOR;
            }

            $result[$file] = $directory . $this->files[$file]['name'];
        }

        if (count($result) == 1) {
            return current($result);
        }

        return $result;
    }

    /**
     * Retrieve additional internal file informations for files
     *
     * @param  string $file (Optional) File to get informations for
     * @return array
     */
    public function getFileInfo($file = null)
    {
        return $this->getFiles($file);
    }

    /**
     * Sets a new destination for the given files
     *
     * @deprecated Will be changed to be a filter!!!
     * @param  string       $destination New destination directory
     * @param  string|array $files       Files to set the new destination for
     * @return AbstractAdapter
     * @throws Exception\InvalidArgumentException when the given destination is not a directory or does not exist
     */
    public function setDestination($destination, $files = null)
    {
        $orig = $files;
        $destination = rtrim($destination, "/\\");
        if (!is_dir($destination)) {
            throw new Exception\InvalidArgumentException('The given destination is not a directory or does not exist');
        }

        if (!is_writable($destination)) {
            throw new Exception\InvalidArgumentException('The given destination is not writeable');
        }

        if ($files === null) {
            foreach ($this->files as $file => $content) {
                $this->files[$file]['destination'] = $destination;
            }
        } else {
            $files = $this->getFiles($files, true, true);
            if (empty($files) and is_string($orig)) {
                $this->files[$orig]['destination'] = $destination;
            }

            foreach ($files as $file) {
                $this->files[$file]['destination'] = $destination;
            }
        }

        return $this;
    }

    /**
     * Retrieve destination directory value
     *
     * @param  null|string|array $files
     * @return null|string|array
     */
    public function getDestination($files = null)
    {
        $orig  = $files;
        $files = $this->getFiles($files, false, true);
        $destinations = array();
        if (empty($files) and is_string($orig)) {
            if (isset($this->files[$orig]['destination'])) {
                $destinations[$orig] = $this->files[$orig]['destination'];
            } else {
                throw new Exception\InvalidArgumentException(sprintf('The file transfer adapter can not find "%s"', $orig));
            }
        }

        foreach ($files as $key => $content) {
            if (isset($this->files[$key]['destination'])) {
                $destinations[$key] = $this->files[$key]['destination'];
            } else {
                $tmpdir = $this->getTmpDir();
                $this->setDestination($tmpdir, $key);
                $destinations[$key] = $tmpdir;
            }
        }

        if (empty($destinations)) {
            $destinations = $this->getTmpDir();
        } else if (count($destinations) == 1) {
            $destinations = current($destinations);
        }

        return $destinations;
    }

    /**
     * Set translator object for localization
     *
     * @param  Translator\Translator|AdapterTranslator\AbstractAdapter|null $translator
     * @return AbstractAdapter
     */
    public function setTranslator($translator = null)
    {
        if (null === $translator) {
            $this->translator = null;
        } elseif ($translator instanceof AdapterTranslator\AbstractAdapter) {
            $this->translator = $translator;
        } elseif ($translator instanceof Translator\Translator) {
            $this->translator = $translator->getAdapter();
        } else {
            throw new Exception\InvalidArgumentException('Invalid translator specified');
        }

        return $this;
    }

    /**
     * Retrieve localization translator object
     *
     * @return AdapterTranslator\AbstractAdapter|null
     */
    public function getTranslator()
    {
        if ($this->translatorIsDisabled()) {
            return null;
        }

        return $this->translator;
    }

    /**
     * Indicate whether or not translation should be disabled
     *
     * @param  bool $flag
     * @return AbstractAdapter
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

    /**
     * Returns the hash for a given file
     *
     * @param  string       $hash  Hash algorithm to use
     * @param  string|array $files Files to return the hash for
     * @return string|array Hashstring
     * @throws Exception\InvalidArgumentException On unknown hash algorithm
     */
    public function getHash($hash = 'crc32', $files = null)
    {
        if (!in_array($hash, hash_algos())) {
            throw new Exception\InvalidArgumentException('Unknown hash algorithm');
        }

        $files  = $this->getFiles($files);
        $result = array();
        foreach($files as $key => $value) {
            if (file_exists($value['name'])) {
                $result[$key] = hash_file($hash, $value['name']);
            } else if (file_exists($value['tmp_name'])) {
                $result[$key] = hash_file($hash, $value['tmp_name']);
            } else if (empty($value['options']['ignoreNoFile'])) {
                throw new Exception\InvalidArgumentException("The file '{$value['name']}' does not exist");
            }
        }

        if (count($result) == 1) {
            return current($result);
        }

        return $result;
    }

    /**
     * Returns the real filesize of the file
     *
     * @param  string|array $files Files to get the filesize from
     * @return string|array Filesize
     * @throws Exception\InvalidArgumentException When the file does not exist
     */
    public function getFileSize($files = null)
    {
        $files  = $this->getFiles($files);
        $result = array();
        foreach($files as $key => $value) {
            if (file_exists($value['name']) || file_exists($value['tmp_name'])) {
                if ($value['options']['useByteString']) {
                    $result[$key] = self::toByteString($value['size']);
                } else {
                    $result[$key] = $value['size'];
                }
            } else if (empty($value['options']['ignoreNoFile'])) {
                throw new Exception\InvalidArgumentException("The file '{$value['name']}' does not exist");
            } else {
                continue;
            }
        }

        if (count($result) == 1) {
            return current($result);
        }

        return $result;
    }

    /**
     * Internal method to detect the size of a file
     *
     * @param  array $value File infos
     * @return string Filesize of given file
     */
    protected function detectFileSize($value)
    {
        if (file_exists($value['name'])) {
            $result = sprintf("%u", @filesize($value['name']));
        } else if (file_exists($value['tmp_name'])) {
            $result = sprintf("%u", @filesize($value['tmp_name']));
        } else {
            return null;
        }

        return $result;
    }

    /**
     * Returns the real mimetype of the file
     * Uses fileinfo, when not available mime_magic and as last fallback a manual given mimetype
     *
     * @param string|array $files Files to get the mimetype from
     * @return string|array MimeType
     * @throws Exception\InvalidArgumentException When the file does not exist
     */
    public function getMimeType($files = null)
    {
        $files  = $this->getFiles($files);
        $result = array();
        foreach($files as $key => $value) {
            if (file_exists($value['name']) || file_exists($value['tmp_name'])) {
                $result[$key] = $value['type'];
            } else if (empty($value['options']['ignoreNoFile'])) {
                throw new Exception\InvalidArgumentException("the file '{$value['name']}' does not exist");
            } else {
                continue;
            }
        }

        if (count($result) == 1) {
            return current($result);
        }

        return $result;
    }

    /**
     * Internal method to detect the mime type of a file
     *
     * @param  array $value File infos
     * @return string Mimetype of given file
     */
    protected function detectMimeType($value)
    {
        if (file_exists($value['name'])) {
            $file = $value['name'];
        } else if (file_exists($value['tmp_name'])) {
            $file = $value['tmp_name'];
        } else {
            return null;
        }

        if (class_exists('finfo', false)) {
            $const = defined('FILEINFO_MIME_TYPE') ? FILEINFO_MIME_TYPE : FILEINFO_MIME;
            if (!empty($value['options']['magicFile'])) {
                $mime = @finfo_open($const, $value['options']['magicFile']);
            }

            if (empty($mime)) {
                $mime = @finfo_open($const);
            }

            if (!empty($mime)) {
                $result = finfo_file($mime, $file);
            }

            unset($mime);
        }

        if (empty($result) && (function_exists('mime_content_type')
            && ini_get('mime_magic.magicfile'))) {
            $result = mime_content_type($file);
        }

        if (empty($result)) {
            $result = 'application/octet-stream';
        }

        return $result;
    }

    /**
     * Returns the formatted size
     *
     * @param  integer $size
     * @return string
     */
    protected static function toByteString($size)
    {
        $sizes = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        for ($i=0; $size >= 1024 && $i < 9; $i++) {
            $size /= 1024;
        }

        return round($size, 2) . $sizes[$i];
    }

    /**
     * Internal function to filter all given files
     *
     * @param  string|array $files (Optional) Files to check
     * @return boolean False on error
     */
    protected function filter($files = null)
    {
        $check           = $this->getFiles($files);
        foreach ($check as $name => $content) {
            if (array_key_exists('filters', $content)) {
                foreach ($content['filters'] as $class) {
                    $filter = $this->filters[$class];
                    try {
                        $result = $filter->filter($this->getFileName($name));

                        $this->files[$name]['destination'] = dirname($result);
                        $this->files[$name]['name']        = basename($result);
                    } catch (FilterException\ExceptionInterface $e) {
                        $this->messages += array($e->getMessage());
                    }
                }
            }
        }

        if (count($this->messages) > 0) {
            return false;
        }

        return true;
    }

    /**
     * Determine system TMP directory and detect if we have read access
     *
     * @return string
     * @throws Exception\RuntimeException if unable to determine directory
     */
    protected function getTmpDir()
    {
        if (null === $this->tmpDir) {
            $tmpdir = array();
            if (function_exists('sys_get_temp_dir')) {
                $tmpdir[] = sys_get_temp_dir();
            }

            if (!empty($_ENV['TMP'])) {
                $tmpdir[] = realpath($_ENV['TMP']);
            }

            if (!empty($_ENV['TMPDIR'])) {
                $tmpdir[] = realpath($_ENV['TMPDIR']);
            }

            if (!empty($_ENV['TEMP'])) {
                $tmpdir[] = realpath($_ENV['TEMP']);
            }

            $upload = ini_get('upload_tmp_dir');
            if ($upload) {
                $tmpdir[] = realpath($upload);
            }

            foreach($tmpdir as $directory) {
                if ($this->isPathWriteable($directory)) {
                    $this->tmpDir = $directory;
                }
            }

            if (empty($this->tmpDir)) {
                // Attemp to detect by creating a temporary file
                $tempFile = tempnam(md5(uniqid(rand(), TRUE)), '');
                if ($tempFile) {
                    $this->tmpDir = realpath(dirname($tempFile));
                    unlink($tempFile);
                } else {
                    throw new Exception\RuntimeException('Could not determine a temporary directory');
                }
            }

            $this->tmpDir = rtrim($this->tmpDir, "/\\");
        }
        return $this->tmpDir;
    }

    /**
     * Tries to detect if we can read and write to the given path
     *
     * @param string $path
     */
    protected function isPathWriteable($path)
    {
        $tempFile = rtrim($path, "/\\");
        $tempFile .= '/' . 'test.1';

        $result = @file_put_contents($tempFile, 'TEST');

        if ($result == false) {
            return false;
        }

        $result = @unlink($tempFile);

        if ($result == false) {
            return false;
        }

        return true;
    }

    /**
     * Returns found files based on internal file array and given files
     *
     * @param  string|array $files       (Optional) Files to return
     * @param  boolean      $names       (Optional) Returns only names on true, else complete info
     * @param  boolean      $noexception (Optional) Allows throwing an exception, otherwise returns an empty array
     * @return array Found files
     * @throws Exception\RuntimeException On false filename
     */
    protected function getFiles($files, $names = false, $noexception = false)
    {
        $check = array();

        if (is_string($files)) {
            $files = array($files);
        }

        if (is_array($files)) {
            foreach ($files as $find) {
                $found = array();
                foreach ($this->files as $file => $content) {
                    if (!isset($content['name'])) {
                        continue;
                    }

                    if (($content['name'] === $find) && isset($content['multifiles'])) {
                        foreach ($content['multifiles'] as $multifile) {
                            $found[] = $multifile;
                        }
                        break;
                    }

                    if ($file === $find) {
                        $found[] = $file;
                        break;
                    }

                    if ($content['name'] === $find) {
                        $found[] = $file;
                        break;
                    }
                }

                if (empty($found)) {
                    if ($noexception !== false) {
                        return array();
                    }

                    throw new Exception\RuntimeException(sprintf('The file transfer adapter can not find "%s"', $find));
                }

                foreach ($found as $checked) {
                    $check[$checked] = $this->files[$checked];
                }
            }
        }

        if ($files === null) {
            $check = $this->files;
            $keys  = array_keys($check);
            foreach ($keys as $key) {
                if (isset($check[$key]['multifiles'])) {
                    unset($check[$key]);
                }
            }
        }

        if ($names) {
            $check = array_keys($check);
        }

        return $check;
    }

    /**
     * Retrieve internal identifier for a named validator
     *
     * @param  string $name
     * @return string
     */
    protected function getValidatorIdentifier($name)
    {
        if (array_key_exists($name, $this->validators)) {
            return $name;
        }

        foreach (array_keys($this->validators) as $test) {
            if (preg_match('/' . preg_quote($name) . '$/i', $test)) {
                return $test;
            }
        }

        return false;
    }

    /**
     * Retrieve internal identifier for a named filter
     *
     * @param  string $name
     * @return string
     */
    protected function getFilterIdentifier($name)
    {
        if (array_key_exists($name, $this->filters)) {
            return $name;
        }

        foreach (array_keys($this->filters) as $test) {
            if (preg_match('/' . preg_quote($name) . '$/i', $test)) {
                return $test;
            }
        }

        return false;
    }
}
