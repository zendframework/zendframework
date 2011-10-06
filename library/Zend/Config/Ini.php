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
 * @package    Zend_Config
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Config;

/**
 * @uses       \Zend\Config\Config
 * @uses       \Zend\Config\Exception
 * @category   Zend
 * @package    Zend_Config
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Ini extends Config
{
    /**
     * String that separates nesting levels of configuration data identifiers
     *
     * @var string
     */
    protected $_nestSeparator = '.';

    /**
     * String that separates the parent section name
     *
     * @var string
     */
    protected $_sectionSeparator = ':';

    /**
     * Whether to skip extends or not
     *
     * @var boolean
     */
    protected $_skipExtends = false;

    /**
     * Loads the section $section from the config file $filename for
     * access facilitated by nested object properties.
     *
     * If the section name contains a ":" then the section name to the right
     * is loaded and included into the properties. Note that the keys in
     * this $section will override any keys of the same
     * name in the sections that have been included via ":".
     *
     * If the $section is null, then all sections in the ini file are loaded.
     *
     * If any key includes a ".", then this will act as a separator to
     * create a sub-property.
     *
     * example ini file:
     *      [all]
     *      db.connection = database
     *      hostname = live
     *
     *      [staging : all]
     *      hostname = staging
     *
     * after calling $data = new Zend_Config_Ini($file, 'staging'); then
     *      $data->hostname === "staging"
     *      $data->db->connection === "database"
     *
     * The $options parameter may be provided as either a boolean or an array.
     * If provided as a boolean, this sets the $allowModifications option of
     * Zend_Config. If provided as an array, there are two configuration
     * directives that may be set. For example:
     *
     * $options = array(
     *     'allowModifications' => false,
     *     'nestSeparator'      => '->'
     *      );
     *
     * @param  string        $filename
     * @param  string|null   $section
     * @param  boolean|array $options
     * @throws \Zend\Config\Exception
     * @return void
     */
    public function __construct($filename, $section = null, $options = false)
    {
        if (empty($filename)) {
            throw new Exception\InvalidArgumentException('Filename is not set');
        }

        $allowModifications = false;
        if (is_bool($options)) {
            $allowModifications = $options;
        } elseif (is_array($options)) {
            if (isset($options['allowModifications'])) {
                $allowModifications = (bool) $options['allowModifications'];
            }
            if (isset($options['nestSeparator'])) {
                $this->_nestSeparator = (string) $options['nestSeparator'];
            }
            if (isset($options['skipExtends'])) {
                $this->_skipExtends = (bool) $options['skipExtends'];
            }
        }

        $iniArray = $this->_loadIniFile($filename);

        if (null === $section) {
            // Load entire file
            $dataArray = array();
            foreach ($iniArray as $sectionName => $sectionData) {
                if(!is_array($sectionData)) {
                    $dataArray = $this->_arrayMergeRecursive($dataArray, $this->_processKey(array(), $sectionName, $sectionData));
                } else {
                    $dataArray[$sectionName] = $this->_processSection($iniArray, $sectionName);
                }
            }
            parent::__construct($dataArray, $allowModifications);
        } else {
            // Load one or more sections
            if (!is_array($section)) {
                $section = array($section);
            }
            $dataArray = array();
            foreach ($section as $sectionName) {
                if (!isset($iniArray[$sectionName])) {
                    throw new Exception\InvalidArgumentException("Section '$sectionName' cannot be found in $filename");
                }
                $dataArray = $this->_arrayMergeRecursive($this->_processSection($iniArray, $sectionName), $dataArray);

            }
            parent::__construct($dataArray, $allowModifications);
        }

        $this->_loadedSection = $section;
    }

    /**
     * Load the ini file and preprocess the section separator (':' in the
     * section name (that is used for section extension) so that the resultant
     * array has the correct section names and the extension information is
     * stored in a sub-key called ';extends'. We use ';extends' as this can
     * never be a valid key name in an INI file that has been loaded using
     * parse_ini_file().
     *
     * @param string $filename
     * @throws \Zend\Config\Exception
     * @return array
     */
    protected function _loadIniFile($filename)
    {
        $this->_setErrorHandler();
        $loaded = parse_ini_file($filename, true);
        $errorMessages = $this->_restoreErrorHandler();
        if ($loaded === false) {
            $e = null;
            foreach ($errorMessages as $errMsg) {
                $e = new Exception\RuntimeException($errMsg, 0, $e);
            }
            $e = new Exception\RuntimeException("Can't parse ini file '{$filename}'", 0, $e);
            throw $e;
        }

        $iniArray = array();
        foreach ($loaded as $key => $data)
        {
            $pieces = explode($this->_sectionSeparator, $key);
            $thisSection = trim($pieces[0]);
            switch (count($pieces)) {
                case 1:
                    $iniArray[$thisSection] = $data;
                    break;

                case 2:
                    $extendedSection = trim($pieces[1]);
                    $iniArray[$thisSection] = array_merge(array(';extends'=>$extendedSection), $data);
                    break;

                default:
                    throw new Exception\RuntimeException("Section '$thisSection' may not extend multiple sections in $filename");
            }
        }

        return $iniArray;
    }

    /**
     * Process each element in the section and handle the ";extends" inheritance
     * key. Passes control to _processKey() to handle the nest separator
     * sub-property syntax that may be used within the key name.
     *
     * @param  array  $iniArray
     * @param  string $section
     * @param  array  $config
     * @throws \Zend\Config\Exception
     * @return array
     */
    protected function _processSection($iniArray, $section, $config = array())
    {
        $thisSection = $iniArray[$section];

        foreach ($thisSection as $key => $value) {
            if (strtolower($key) == ';extends') {
                if (isset($iniArray[$value])) {
                    $this->_assertValidExtend($section, $value);

                    if (!$this->_skipExtends) {
                        $config = $this->_processSection($iniArray, $value, $config);
                    }
                } else {
                    throw new Exception\RuntimeException("Parent section '$section' cannot be found");
                }
            } else {
                $config = $this->_processKey($config, $key, $value);
            }
        }
        return $config;
    }

    /**
     * Assign the key's value to the property list. Handles the
     * nest separator for sub-properties.
     *
     * @param  array  $config
     * @param  string $key
     * @param  string $value
     * @throws \Zend\Config\Exception
     * @return array
     */
    protected function _processKey($config, $key, $value)
    {
        if (strpos($key, $this->_nestSeparator) !== false) {
            $pieces = explode($this->_nestSeparator, $key, 2);
            if (strlen($pieces[0]) && strlen($pieces[1])) {
                if (!isset($config[$pieces[0]])) {
                    if ($pieces[0] === '0' && !empty($config)) {
                        // convert the current values in $config into an array
                        $config = array($pieces[0] => $config);
                    } else {
                        $config[$pieces[0]] = array();
                    }
                } elseif (!is_array($config[$pieces[0]])) {
                    throw new Exception\RuntimeException("Cannot create sub-key for '{$pieces[0]}' as key already exists");
                }
                $config[$pieces[0]] = $this->_processKey($config[$pieces[0]], $pieces[1], $value);
            } else {
                throw new Exception\RuntimeException("Invalid key '$key'");
            }
        } else {
            if (is_string($value)) {
                if(strtolower(trim($value)) === 'true') {
                    $value = '1';
                } elseif(strtolower(trim($value)) === 'false') {
                    $value = '';
                }
            }
            $config[$key] = $value;
        }
        return $config;
    }
}
