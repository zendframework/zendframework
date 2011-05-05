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
 * @package   Zend_Uri
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Uri;
use Zend\Validator\Hostname,
    Zend\Uri\Exception\InvalidArgumentException;

/**
 * URL handler
 *
 * @uses      \Zend\URI\URI
 * @uses      \Zend\URI\Exception\InvalidArgumentException
 * @uses      \Zend\Validator\Hostname\Hostname
 * @category  Zend
 * @package   Zend_Uri
 * @copyright Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Url implements Uri
{
    /**
     * Character classes for validation regular expressions
     * (null) values here will be initialized during calls to _getRegex
     */
    private static $_regex = array(
        'initialized'   => false,
        'charAlnum'     => 'A-Za-z0-9',
        'charMark'      => '-_.!~*\'()\[\]',
        'charReserved'  => ';\/?:@&=+$,',
        'charSegment'   => ':@&=+$,;',
        'escaped'       => '%[[:xdigit:]]{2}',
        'unreserved'    => null,
        'path'          => null,
        'segment'       => null,
        'uric'          => null,
        'unwise'        => '{}|\\\\^`'
        );

    /**
     * @var bool
     */
    protected $_allowUnwise = false;
    
    /**#@+
     * Part of a URL
     * 
     * @var string|array
     */
    protected $_scheme = null;
    protected $_username = null;
    protected $_password = null;
    protected $_host = null;
    protected $_port = null;
    protected $_path = null;
    protected $_query = null;
    protected $_fragment = null;
    /**#@-*/
    
    /**
     * _getRegex()
     * 
     * @param string $regexName
     * @param bool $allowUnwiseCharset
     * @throws \Zend\Uri\Exception\InvalidArgumentException
     */
    final protected static function _getRegex($regexName, $allowUnwiseCharset = false)
    {
        if (!self::$_regex['initialized']) {
            // Unreserved characters
            self::$_regex['unreserved'] = '[' . self::$_regex['charAlnum'] 
                . self::$_regex['charMark'] . ']';

            // Segment can use escaped, unreserved or a set of additional chars
            self::$_regex['segment']    = '(?:' . self::$_regex['escaped'] . '|['
                . self::$_regex['charAlnum'] . self::$_regex['charMark'] 
                . self::$_regex['charSegment'] . '])*';

            // Path can be a series of segmets char strings seperated by '/'
            self::$_regex['path'] = '(?:\/(?:' . self::$_regex['segment'] . ')?)+';

            // URI characters can be escaped, alphanumeric, mark or reserved chars
            self::$_regex['uric'] = '(?:' . self::$_regex['escaped'] . '|['
                . self::$_regex['charAlnum'] . self::$_regex['charMark']
                . self::$_regex['charReserved']
                . '])';
            
            self::$_regex['initialized'] = true;
        }
        
        if (!array_key_exists($regexName, self::$_regex)) {
            throw new InvalidArgumentException('Requested regex ' . $regexName . ' is not a valid regexName');
        }
        
        switch ($regexName) {
            case 'uric':
                $regex = self::$_regex['uric'];
                if ($allowUnwiseCharset) {
                    // alter the regex to include uwise charset
                    $regex = substr($regex, 0, -2) . self::$_regex['unwise'] . '])';
                }
                break;
            default:
                $regex = self::$_regex[$regexName];
                break;
        }
        

        return $regex;
    }

    public static function validate($url)
    {
        if (empty($url)) {
            return false;
        }
        try {
            $url = new self($url);
        } catch (InvalidArgumentException $exception) {
            return false;
        }

        if (!$url->getScheme()) {
            return false;
        }

        return $url->isValid();
    }
    
    /**
     * validateUsername()
     * 
     * @param string $username
     * @return bool
     */
    public static function validateUsername($username)
    {
        // If the username is empty, then it is considered valid
        if (strlen($username) === 0) {
            return true;
        }

        // get predefined regexs to build custom regex
        $escapedRegex   = self::_getRegex('escaped');
        $charAlnumRegex = self::_getRegex('charAlnum');
        $charMarkRegex  = self::_getRegex('charMark');
        
        // Check the username against the allowed values
        $status = preg_match(
            '/^(?:' . $escapedRegex . '|[' . $charAlnumRegex . $charMarkRegex . ';:&=+$,' . '])+$/',
            $username
            );

        return $status === 1;
    }
    
    /**
     * validatePassword()
     * 
     * @param string $password
     * @return boolean
     */
    public static function validatePassword($password)
    {
        // If the password is empty, then it is considered valid
        if (strlen($password) === 0) {
            return true;
        }

        // get predefined regexs to build custom regex
        $escapedRegex   = self::_getRegex('escaped');
        $charAlnumRegex = self::_getRegex('charAlnum');
        $charMarkRegex  = self::_getRegex('charMark');
        
        // Check the password against the allowed values
        $status = preg_match(
            '/^(?:' . $escapedRegex . '|[' . $charAlnumRegex . $charMarkRegex . ';:&=+$,' . '])+$/',
            $password
            );

        return $status == 1;
    }
    
    /**
     * validateHost()
     * 
     * @param $host
     * @return bool
     */
    public static function validateHost($host)
    {
        // If the host is empty, then it is considered invalid
        if (strlen($host) === 0) {
            return false;
        }

        // Check the host against the allowed values; delegated to Zend_Filter.
        $validate = new Hostname(Hostname::ALLOW_ALL);

        return $validate->isValid($host);
    }
    
    /**
     * validatePort()
     * 
     * @param string|int $port
     * @return bool
     */
    public static function validatePort($port)
    {
        // If the port is empty, then it is considered valid
        if (strlen($port) === 0) {
            return true;
        }

        // Check the port against the allowed values
        return (ctype_digit((string) $port) && 1 <= $port && $port <= 0xffff);
    }
    
    /**
     * validatePath()
     * 
     * @param string $path
     * @return bool
     */
    public static function validatePath($path)
    {
        // If the path is empty, then it is considered valid
        if (strlen($path) === 0) {
            return true;
        }

        // Determine whether the path is well-formed
        $pathRegex = self::_getRegex('path');
        $status  = preg_match('/^' . $pathRegex . '$/', $path);

        return (boolean) $status;
    }

    /**
     * validateQuery()
     * 
     * @param string $query
     * @return bool
     */
    public static function validateQuery($query, $allowUnwiseCharacters = false)
    {
        // If query is empty, it is considered to be valid
        if (strlen($query) === 0) {
            return true;
        }

        $uricRegex = self::_getRegex('uric', $allowUnwiseCharacters);
        
        // Determine whether the query is well-formed
        $status  = preg_match('/^' . $uricRegex . '*$/', $query, $matches);

        return ($status == 1);
    }
    
    /**
     * validateFragment()
     * 
     * @param string $fragment
     * @return bool
     */
    public static function validateFragment($fragment, $allowUnwiseCharacters = false)
    {
        // If fragment is empty, it is considered to be valid
        if (strlen($fragment) === 0) {
            return true;
        }

        $uricRegex = self::_getRegex('uric', $allowUnwiseCharacters);
        
        // Determine whether the fragment is well-formed
        $status = preg_match('/^' . $uricRegex . '*$/', $fragment);

        return (boolean) $status;
    }
    
    /**
     * Constructor
     * 
     * @param array|string $options
     */
    public function __construct($optionsOrURL = null)
    {
        if (is_string($optionsOrURL)) {
            $this->parse($optionsOrURL);
        } elseif (is_array($optionsOrURL) && $optionsOrURL) {
            $this->setOptions($optionsOrURL);
        }
    }
    
    /**
     * setOptions
     * 
     * @param $options
     * @return URL
     */
    public function setOptions(Array $options)
    {
        foreach ($options as $optionName => $optionValue) {
            $methodName = 'set' . $optionName;
            if (method_exists($this, $methodName)) {
                $this->{$methodName}($optionValue);
            }
        }
        return $this;
    }
    
    /**
     * setAllowUnwise()
     * 
     * @param bool $allowUnwise
     * @return \Zend\Uri\Url
     */
    public function setAllowUnwise($allowUnwise = false)
    {
        $this->_allowUnwise = (bool) $allowUnwise;
        return $this;
    }
    
    /**
     * parse()
     * @param unknown_type $url
     * @throws \Zend\Uri\Exception\InvalidArgumentException
     */
    public function parse($url)
    {
        // use PHP's parse_url
        $parts = parse_url($url);
        
        if ($parts === false) {
            throw new InvalidArgumentException('The url provided ' . $url . ' is not parsable.');
        }
        
        $options = array();
        foreach ($parts as $partName => $partValue) {
            switch (strtolower($partName)) {
                case 'url':
                case 'fromurl':
                    $this->parse($partValue);
                    break;
                case 'user':
                    $options['username'] = $partValue;
                    break;
                case 'pass':
                    $options['password'] = $partValue;
                    break;
                default:
                    $options[$partName] = $partValue;
                    break;
            }
        }
        if ($options) {
            $this->setOptions($options);
        }
        return $this;
    }
    
    /**
     * getScheme()
     * 
     * @return string
     */
    public function getScheme()
    {
        return $this->_scheme;
    }
    
    /**
     * setScheme()
     * 
     * @param string $scheme
     * @return URL
     */
    public function setScheme($scheme)
    {
        $this->_scheme = strtolower($scheme);
        return $this;
    }
    
    /**
     * getHost()
     * 
     * @return string
     */
    public function getHost()
    {
        return $this->_host;
    }
    
    /**
     * setHost()
     * 
     * @param $host
     * @return URL
     */
    public function setHost($host)
    {
        $this->_host = $host;
        return $this;
    }
    
    /**
     * isValidHost()
     * 
     * @return bool
     */
    public function isValidHost()
    {
        if ($this->_host) {
            return self::validateHost($this->_host);
        }
        return true;
    }
    
    /**
     * getPort()
     * 
     * @return string
     */
    public function getPort()
    {
        return $this->_port;
    }
    
    /**
     * setPort()
     * 
     * @param int|string $port
     * @return URL
     */
    public function setPort($port)
    {
        $this->_port = $port;
        return $this;
    }
    
    /**
     * isValidPort()
     * 
     */
    public function isValidPort()
    {
        if ($this->_port) {
            return self::validatePort($this->_port);
        }
        
        return true;
    }
    
    /**
     * getUsername()
     * 
     * @return string
     */
    public function getUsername()
    {
        return $this->_username;
    }
    
    /**
     * setUsername()
     * 
     * @param string $username
     * @return URL
     */
    public function setUsername($username)
    {
        $this->_username = $username;
        return $this;
    }
    
    /**
     * isValidUsername()
     * 
     */
    public function isValidUsername()
    {
        if ($this->_username) {
            return self::validateUsername($this->_username);
        }
        return true;
    }
    
    /**
     * getPassword()
     * 
     * @return string
     */
    public function getPassword()
    {
        return $this->_password;
    }
    
    /**
     * setPassword()
     * 
     * @param string $password
     * @return URL
     */
    public function setPassword($password)
    {
        $this->_password = $password;
        return $this;
    }
    
    /**
     * isValidPassword()
     * 
     * @return bool
     */
    public function isValidPassword()
    {
        // If the password is nonempty, but there is no username, then it is considered invalid
        if (strlen($this->_password) > 0 and strlen($this->_username) === 0) {
            return false;
        } elseif ($this->_password != '') {
            return self::validatePassword($this->_password);
        }
        
        return true;
    }
    
    
    /**
     * getPath()
     * 
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }
    
    /**
     * setPath()
     * 
     * @param $path
     * @return URL
     */
    public function setPath($path)
    {
        if (empty($path)) {
            $path = '/';
        }
        $this->_path = $path;
        return $this;
    }
    
    /**
     * isValidPath()
     * 
     * @return bool
     */
    public function isValidPath()
    {
        if ($this->_path) {
            return self::validatePath($this->_path);
        }
        
        return true;
    }
    
    /**
     * getQuery()
     * 
     * @return string
     */
    public function getQuery()
    {
        return $this->_query;
    }
    
    /**
     * setQuery()
     * 
     * @param string|array $query
     * @return URL
     */
    public function setQuery($query)
    {
        // If query is empty, set an empty string
        if (empty($query) === true) {
            $this->_query = '';
            return $this;
        }

        // If query is an array, make a string out of it
        if (is_array($query) === true) {
            $query = http_build_query($query, '', '&');
        } else {
            // If it is a string, make sure it is valid. If not parse and encode it
            $query = (string) $query;
            if (self::validateQuery($query, $this->_allowUnwise) === false) {
                parse_str($query, $queryArray);
                $query = http_build_query($queryArray, '', '&');
            }
        }

        $this->_query = $query;
        return $this;
    }
    
    /**
     * isValidQuery()
     * 
     * @return bool
     */
    public function isValidQuery()
    {
        if ($this->_query) {
            return self::validateQuery($this->_query, $this->_allowUnwise);
        }
        
        return true;
    }
    
    /**
     * Returns the query portion of the URL (after ?) as a
     * key-value-array. If the query is empty an empty array
     * is returned
     *
     * @return array
     */
    public function getQueryAsArray()
    {
        $query = $this->getQuery();
        $queryParts = array();
        if ($query !== false) {
            parse_str($query, $queryParts);
        }
        return $queryParts;
    }
    
    /**
     * Add or replace params in the query string for the current URI, and
     * return the old query.
     *
     * @param  array $queryParams
     * @return string Old query string
     */
    public function addReplaceQueryParameters(array $queryParams)
    {
        $queryParams = array_merge($this->getQueryAsArray(), $queryParams);
        return $this->setQuery($queryParams);
    }

    /**
     * Remove params in the query string for the current URI, and
     * return the old query.
     *
     * @param  array $queryParamKeys
     * @return string Old query string
     */
    public function removeQueryParameters(array $queryParamKeys)
    {
        $queryParams = array_diff_key($this->getQueryAsArray(), array_fill_keys($queryParamKeys, 0));
        return $this->setQuery($queryParams);
    }
    
    /**
     * getFragement()
     * 
     * @return true
     */
    public function getFragment()
    {
        return $this->_fragment;
    }
    
    /**
     * setFragment()
     * 
     * @param $fragment
     * @return URL
     */
    public function setFragment($fragment)
    {
        $this->_fragment = $fragment;
        return $this;
    }
    
    /**
     * isValidFragment()
     * 
     * @return bool
     */
    public function isValidFragment()
    {
        if ($this->_fragment) {
            return self::validateFragment($this->_fragment);
        }
        
        return true;
    }
    
    public function isValid()
    {
        // Return true if and only if all parts of the URI have passed validation
        if ($this->isValidUsername()
           && $this->isValidPassword()
           && $this->isValidHost()
           && $this->isValidPort()
           && $this->isValidPath()
           && $this->isValidQuery()
           && $this->isValidFragment()
        ) {
            if (!$this->getScheme()) {
                return false;
            }
            if (!$this->getHost()) {
                return false;
            }
            return true;
        }
        return false;
    }
    
    /**
     * generate()
     * 
     * @return string
     */
    public function generate()
    {
        $url = '';

        $password = strlen($this->_password) > 0 ? ":$this->_password" : '';
        $auth     = strlen($this->_username) > 0 ? "$this->_username$password@" : '';
        $port     = strlen($this->_port) > 0 ? ":$this->_port" : '';
        $query    = strlen($this->_query) > 0 ? "?$this->_query" : '';
        $fragment = strlen($this->_fragment) > 0 ? "#$this->_fragment" : '';

        $url = $this->_scheme
             . '://'
             . $auth
             . $this->_host
             . $port
             . $this->_path
             . $query
             . $fragment;
        
        return $url;
    }
    
    /**
     * __toString()
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->generate();
    }
    
}

