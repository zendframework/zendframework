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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id$
 */

/**
 * @namespace
 */
namespace Zend\Uri;

use Zend\Validator;

/**
 * Generic URI handler
 *
 * @uses      \Zend\Uri\Exception
 * @uses      \Zend\Validator\Hostname
 * @uses      \Zend\Validator\Ip
 * @category  Zend
 * @package   Zend_Uri
 * @copyright Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Uri
{
    /**
     * Character classes defined in RFC-3986
     */
    const CHAR_UNRESERVED = '\w\-\.~';
    const CHAR_GEN_DELIMS = ':\/\?#\[\]@';
    const CHAR_SUB_DELIMS = '!\$&\'\(\)\*\+,;=';
    const CHAR_RESERVED   = ':\/\?#\[\]@!\$&\'\(\)\*\+,;=';
    
    /**
     * URI scheme 
     * 
     * @var string
     */
    protected $_scheme;
    
    /**
     * URI userInfo part (usually user:password in HTTP URLs)
     * 
     * @var string
     */
    protected $_userInfo;
    
    /**
     * URI hostname
     *  
     * @var string
     */
    protected $_host;
    
    /**
     * URI port
     * 
     * @var integer
     */
    protected $_port;
    
    /**
     * URI path
     * 
     * @var string
     */
    protected $_path;
    
    /**
     * URI query string
     * 
     * @var string
     */
    protected $_query;
    
    /**
     * URI fragment
     * 
     * @var string
     */
    protected $_fragment;

    /**
     * Array of valid schemes.
     * 
     * Subclasses of this class that only accept specific schemes may set the
     * list of accepted schemes here. If not empty, when setScheme() is called
     * it will only accept the schemes listed here.  
     *
     * @var array
     */
    static protected $_validSchemes = array();
    
    /**
     * Create a new URI object
     * 
     * @param \Zend\URI\URI|string|null $uri
     * @throws \InvalidArgumentException
     */
    public function __construct($uri = null) 
    {
        if (is_string($uri)) {
            $this->parse($uri);
            
        } elseif ($uri instanceof URI) {
            // Copy constructor
            $this->setScheme($uri->getScheme());
            $this->setUserInfo($uri->getUserInfo());
            $this->setHost($uri->getHost());
            $this->setPort($uri->getPort());
            $this->setPath($uri->getPath());
            $this->setQuery($uri->getQuery());
            $this->setFragment($uri->getFragment());
            
        } elseif ($uri !== null) {
            /**
             * @todo use a proper Exception class for Zend\URI
             */
            throw new \InvalidArgumentException('expecting a string or a URI object, got ' . gettype($uri));
        }
    }

    /**
     * Check if the URI is valid
     * 
     * Note that a relative URI may still be valid
     * 
     * @return boolean
     */
    public function isValid()
    {
        if ($this->_host) {
            if (strlen($this->_path) > 0 && substr($this->_path, 0, 1) != '/') return false; 
        } else {
            if ($this->_userInfo || $this->_port) return false;
            if (! ($this->_path || $this->_query || $this->_fragment)) return false;
        }
        
        return true;
    }
    
    /**
     * Check if the URI is an absolute or relative URI
     * 
     * @return boolean
     */
    public function isAbsolute()
    {
        return ($this->_scheme != null);
    }
    
    /**
     * Parse a URI string
     * 
     * @return \Zend\URI\URI
     */
    public function parse($uri)
    {
        // Capture scheme 
        if (preg_match('/^([A-Za-z][A-Za-z0-9\.\+\-]*):/', $uri, $match)) {
            $this->setScheme($match[1]);
            $uri = substr($uri, strlen($match[0]));
        }
        
        // Capture authority part
        if (preg_match('|^//([^/\?#]*)|', $uri, $match)) {
            $authority = $match[1];
            $uri = substr($uri, strlen($match[0]));
            
            // Split authority into userInfo and host
            if (strpos($authority, '@') !== false) {
                list($userInfo, $authority) = explode('@', $authority, 2);
                $this->setUserInfo($userInfo);
            }
            
            $colonPos = strrpos($authority, ':');
            if ($colonPos !== false) {
                $port = substr($authority, $colonPos + 1);
                if ($port) $this->setPort((int) $port);
                $authority = substr($authority, 0, $colonPos);
            }
            
            if ($authority) {
                $this->setHost($authority);
            }
        }
        if (! $uri) return $this;
        
        // Capture the path
        if (preg_match('|^[^\?#]*|', $uri, $match)) {
            $this->setPath($match[0]);
            $uri = substr($uri, strlen($match[0]));
        }
        if (! $uri) return $this;
        
        // Capture the query
        if (preg_match('|^\?([^#]*)|', $uri, $match)) {
            $this->setQuery($match[1]);
            $uri = substr($uri, strlen($match[0]));
        }
        if (! $uri) return $this;
        
        // All that's left is the fragment
        if ($uri && substr($uri, 0, 1) == '#') {
            $this->setFragment(substr($uri, 1));
        }
                
        return $this;
    }

    /**
     * Compose the URI into a string
     * 
     * @return string
     */
    public function generate()
    {
        if (! $this->isValid()) {
            throw new InvalidURIException("URI is not valid and cannot be converted into a string");
        }
        
        $uri = '';

        if ($this->_scheme) $uri = "$this->_scheme:"; 
        
        if ($this->_host !== null) {
            $uri .= '//';
            if ($this->_userInfo) $uri .= $this->_userInfo . '@';
            $uri .= $this->_host;
            if ($this->_port) $uri .= ":$this->_port";
        }

        if ($this->_path) {
            $uri .= $this->_path;
        } elseif ($this->_host && ($this->_query || $this->_fragment)) {
            $uri .= '/';
        }
        
        if ($this->_query) $uri .= "?" . self::encodeQueryFragment($this->_query);
        if ($this->_fragment) $uri .= "#" . self::encodeQueryFragment($this->_fragment);

        return $uri;
    }
    
    /**
     * Normalize the URI
     * 
     * Normalizing a URI includes removing any redundant parent directory or 
     * current directory references from the path (e.g. foo/bar/../baz becomes
     * foo/baz), normalizing the scheme case, decoding any over-encoded 
     * characters etc. 
     *  
     * Eventually, two normalized URLs pointing to the same resource should be 
     * equal even if they were originally represented by two different strings 
     * 
     * @return \Zend\URI\URI
     */
    public function normalize()
    {
        return $this;
    }
    
    /**
     * Convert a relative URI into an absolute URI using a base absolute URI as 
     * a reference.    
     * 
     * @return \Zend\URI\URI
     */
    public function resolve($baseUrl)
    {
        if (! $this->isAbsolute()) {
            
        }
        
        return $this;
    } 

    /**
     * Convert the link to a relative link by substracting a base URI
     * 
     *  This is the opposite of 'resolving' a relative link - creating a 
     *  relative reference link from an original URI and a base URI. 
     *  
     *  If the two URIs do not intersect (e.g. the original URI is not in any
     *  way related to the base URI) the URI will not be modified. 
     * 
     * @return \Zend\URI\URI
     */
    public function makeRelative($baseUrl)
    {
        return $this;
    }
    
    /**
     * @return the $_scheme
     */
    public function getScheme()
    {
        return $this->_scheme;
    }

	/**
     * @return the $_userInfo
     */
    public function getUserInfo()
    {
        return $this->_userInfo;
    }

	/**
     * @return the $_host
     */
    public function getHost()
    {
        return $this->_host;
    }

	/**
     * @return the $_port
     */
    public function getPort()
    {
        return $this->_port;
    }

	/**
     * @return the $_path
     */
    public function getPath()
    {
        return $this->_path;
    }

	/**
     * @return the $_query
     */
    public function getQuery()
    {
        return $this->_query;
    }
    
    /**
     * Return the query string as an associative array of key => value pairs
     *
     * This is an extension to RFC-3986 but is quite useful when working with
     * most common URI types
     * 
     * @return array
     */
    public function getQueryAsArray()
    {
        $query = array();
        if ($this->_query) {
            parse_str($this->_query, $query);
        }
        
        return $query;
    }

	/**
     * @return the $_fragment
     */
    public function getFragment()
    {
        return $this->_fragment;
    }

	/**
	 * Set the URI scheme
	 * 
	 * If the scheme is not valid according to the generic scheme syntax or 
	 * is not acceptable by the specific URI class (e.g. 'http' or 'https' are 
	 * the only acceptable schemes for the Zend\URI\HTTTP class) an exception
	 * will be thrown. 
	 * 
	 * You can check if a scheme is valid before setting it using the 
	 * validateScheme() method. 
	 * 
     * @param string $scheme
     * @throws \Zend\URI\InvalidSchemeException
     * @return \Zend\URI\URI
     */
    public function setScheme($scheme)
    {
        if ($scheme !== null && (! self::validateScheme($scheme))) {
            throw new InvalidSchemeException("Scheme '$scheme' is not a valid URI scheme or is not accepted by " . get_class($this));
        }
        
        $this->_scheme = $scheme;
        return $this;
    }

	/**
     * @param string $userInfo
     * @return \Zend\URI\URI
     */
    public function setUserInfo($userInfo)
    {
        $this->_userInfo = $userInfo;
        return $this;
    }

	/**
     * @param string $host
     * @return \Zend\URI\URI
     */
    public function setHost($host)
    {
        $this->_host = $host;
        return $this;
    }

	/**
     * @param integer $port
     * @return \Zend\URI\URI
     */
    public function setPort($port)
    {
        $this->_port = $port;
        return $this;
    }

	/**
     * @param string $path
     * @return \Zend\URI\URI
     */
    public function setPath($path)
    {
        $this->_path = $path;
        return $this;
    }

	/**
	 * Set the query string, encoding any characters which should be encoded.
	 * 
     * @param string $query
     * @return \Zend\URI\URI
     */
    public function setQuery($query)
    {
        $this->_query = $query;
        return $this;
    }

	/**
     * @param string $fragment
     * @return \Zend\URI\URI
     */
    public function setFragment($fragment)
    {
        $this->_fragment = $fragment;
        return $this;
    }

    /**
     * Magic method to convert the URI to a string
     * 
     * @return string
     */
	public function __toString()
    {
        return $this->generate();
    }
    
    /**
     * Encoding and Validation Methods
     */

    /**
     * Check if a scheme is valid or not
     * 
     * Will check $scheme to be valid against the generic scheme syntax defined
     * in RFC-3986. If the class also defines specific acceptable schemes, will
     * also check that $scheme is one of them.
     * 
     * @param  string $scheme
     * @return boolean
     */
    static public function validateScheme($scheme)
    {
        if (! empty(static::$_validSchemes) &&
            ! in_array(strtolower($scheme), static::$_validSchemes)) {
            
            return false;
        }
        
        return (bool) preg_match('/^[A-Za-z][A-Za-z0-9\-\.+]*$/', $scheme);
    }
    
    /**
     * Check that the userInfo part of a URI is valid
     * 
     * @param string $userInfo
     * @return boolean
     */
    static public function validateUserInfo($userInfo)
    {
        $regex = '/^(?:[' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . ':]+|%[A-Fa-f0-9]{2})*$/';
        return (boolean) preg_match($regex, $userInfo);
    }
    
    /**
     * Validate the host part
     * 
     * This allows different host representations, including IPv4 addresses, 
     * IPv6 addresses enclosed in square brackets, and registered names which
     * may be DNS names or even more complex names. This is different (and is
     * more loose) from what is commonly accepted as valid HTTP URLs for 
     * example.
     *   
     * @todo  Users should be able to control which host types are allowed 
     * @param string $host
     * @return boolean
     */
    static public function validateHost($host)
    {
        if (preg_match('/^\[(.+)\]$/', $host, $match)) {
            // Expect an IPv6 address
            $validator = new Validator\Ip(array('allowipv4' => false));
            $host = $match[1];
             
        } else {
            // Expect an IPv4 address or a hostname
            $validator = new Validator\Hostname(array(
                'allow' => Validator\Hostname::ALLOW_ALL,
                'ip'    => new Validator\Ip(array('allowipv6' => false))
            ));
        }
        
        if ($validator->isValid($host)) { 
            return true;
        } else {
            // Fallback: validate using reg-name regex
            $regex = '/^(?:[' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . ':@\/\?]+|%[A-Fa-f0-9]{2})+$/';
            return (bool) preg_match($regex, $host);
        }
    }

    /**
     * Validate the port 
     * 
     * Valid values include numbers between 1 and 65535, and empty values
     * 
     * @param  integer $port
     * @return boolean
     */
    static public function validatePort($port)
    {
        if ($port === 0) {
            return false; 
        }
        
        if ($port) {
            $port = (int) $port;
            if ($port < 1 || $port > 0xffff) return false;
        }
        
        return true;
    }
    
    static public function validatePath($path)
    {
        throw new Exception("Implelemt me!");
    }

    /**
     * Check if a URI query or fragment part is valid or not
     * 
     * Query and Fragment parts are both restricted by the same syntax rules, 
     * so the same validation method can be used for both. 
     * 
     * You can encode a query or fragment part to ensure it is valid by passing
     * it through the encodeQueryFragment() method.
     *  
     * @param  string $input
     * @return boolean 
     */
    static public function validateQueryFragment($input)
    {
        $regex = '/^(?:[' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . ':@\/\?]+|%[A-Fa-f0-9]{2})*$/';
        return (boolean) preg_match($regex, $input);
    }
    
    /**
     * URL-encode the user info part of a URI 
     * 
     * @param  string $userInfo
     * @return string
     * @throws \InvalidArgumentException
     */
    static public function encodeUserInfo($userInfo)
    {
        if (! is_string($userInfo)) {
            throw new \InvalidArgumentException("Expecting a string, got " . gettype($userInfo));
        }
        
        $regex = '/(?:[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '%:]|%(?![A-Fa-f0-9]{2}))/'; 
        $replace = function($match) {
            return rawurlencode($match[0]);
        };
        
        return preg_replace_callback($regex, $replace, $userInfo);
    }
    
    static public function encodePath($path)
    {
        throw new Exception("Implelemt me!");
    }
    
    /**
     * URL-encode a query string or fragment based on RFC-3986 guidelines. 
     * 
     * Note that query and fragment encoding allows more unencoded characters 
     * than the usual rawurlencode() function would usually return - for example 
     * '/' and ':' are allowed as literals.
     *  
     * @param  string $input
     * @return string
     */
    static public function encodeQueryFragment($input)
    {
        if (! is_string($input)) {
            throw new \InvalidArgumentException("Expecting a string, got " . gettype($input));
        }   
        
        $regex = '/(?:[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '%:@\/\?]+|%(?![A-Fa-f0-9]{2}))/'; 
        $replace = function($match) {
            return rawurlencode($match[0]);
        };
        
        return preg_replace_callback($regex, $replace, $input);
    }
}
