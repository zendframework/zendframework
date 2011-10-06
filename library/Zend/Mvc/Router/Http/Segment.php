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
 * @package    Zend_Router
 * @subpackage Route
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Mvc\Router\Http;

use Traversable,
    Zend\Config\Config,
    Zend\Stdlib\RequestDescription as Request,
    Zend\Mvc\Router\Exception,
    Zend\Mvc\Router\Route;

/**
 * Segment route.
 *
 * @package    Zend_Router
 * @subpackage Route
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
class Segment implements Route
{
    /**
     * Parts of the route.
     * 
     * @var array
     */
    protected $parts;
    
    /**
     * Regex used for matching the route.
     * 
     * @var string
     */
    protected $string;

    /**
     * Default values.
     *
     * @var array
     */
    protected $defaults;

    /**
     * __construct(): defined by Route interface.
     *
     * @see    Route::__construct()
     * @param  mixed $options
     * @return void
     */
    public function __construct($options = null)
    {
        if ($options instanceof Config) {
            $options = $options->toArray();
        } elseif ($options instanceof Traversable) {
            $options = iterator_to_array($options);
        }

        if (!is_array($options)) {
            throw new Exception\InvalidArgumentException('Options must either be an array or a Traversable object');
        }

        if (!isset($options['route']) || !is_string($options['route'])) {
            throw new Exception\InvalidArgumentException('Route not defined nor not a string');
        }
        
        if (!isset($options['defaults']) || !is_array($options['defaults'])) {
            throw new Exception\InvalidArgumentException('Defaults not defined nor not an array');
        }

        $this->defaults = $options['defaults'];        
        $this->parts    = $this->parseRouteDefinition($options['route']);
        $this->regex    = $this->buildRegex($this->parts);
    }
    
    /**
     * Parse a route definition.
     * 
     * @param  string $def
     * @return array
     */
    protected function parseRouteDefinition($def)
    {
        $currentPos = 0;
        $length     = strlen($def);
        $parts      = array();
        $levelParts = array(&$parts);
        $level      = 0;
        
        while ($currentPos < $length) {
            preg_match('(\G(?<literal>[^:{\[\]]*)(?<token>[:{\[\]]|$))', $def, $matches, 0, $currentPos);
            
            $currentPos += strlen($matches[0]);
            
            if (!empty($matches['literal'])) {
                $levelParts[$level][] = array('literal', $matches['literal']);
            }
            
            if ($matches['token'] === ':') {                
                if ($def[$currentPos] === '{') {
                    if (!preg_match('(\G\{(?<name>[^}]+)\}:?)', $def, $matches, 0, $currentPos)) {
                        throw new Exception\RuntimeException('Translated parameter missing closing bracket');
                    }
                    
                    $levelParts[$level][] = array('translated-parameter', $matches['name']);
                } else {
                    if (!preg_match('(\G(?<name>[^:/\[\]]+):?)', $def, $matches, 0, $currentPos)) {
                        throw new Exception\RuntimeException('Found empty parameter name');
                    }
                    
                    $levelParts[$level][] = array('parameter', $matches['name']);                                    
                }
                
                $currentPos += strlen($matches[0]);
            } elseif ($matches['token'] === '{') {
                if (!preg_match('(\G(?<literal>[^}]+)\})', $def, $matches, 0, $currentPos)) {
                    throw new Exception\RuntimeException('Translated literal missing closing bracket');
                }
                
                $currentPos += strlen($matches[0]);
                
                $levelParts[$level][] = array('translated-literal', $matches['literal']); 
            } elseif ($matches['token'] === '[') {
                $levelParts[$level][] = array('optional', array());
                $levelParts[$level + 1] = &$levelParts[$level][count($levelParts[$level]) - 1][1];
                
                $level++;
            } elseif ($matches['token'] === ']') {
                unset($levelParts[$level]);
                $level--;
                
                if ($level < 0) {
                    throw new Exception\RuntimeException('Found closing bracket without matching opening bracket');
                }
            } else {
                break;
            }
        }
        
        if ($level > 0) {
            throw new Exception\RuntimeException('Found unbalanced brackets');
        }
        
        return $parts;
    }
    
    /**
     * Build the matching regex from parsed parts.
     * 
     * @param  array $parts
     * @return string
     */
    protected function buildRegex(array $parts)
    {
        $regex = '';
        
        foreach ($parts as $part) {
            switch ($part[0]) {
                case 'literal':
                case 'translated-literal':
                    $regex .= preg_quote($part[1]);
                    break;
                
                case 'parameter':
                case 'translated-parameter':
                    $regex .= '(?<' . $part[1] . '>.+?)';
                    break;
                
                case 'optional':
                    $regex .= '(?:' . $this->buildRegex($part[1]) . ')?';
                    break;
            }
        }
        
        return $regex;
    }
    
    /**
     * Build a path.
     * 
     * @return string
     */
    protected function buildPath(array $parts, array $params)
    {
        $path = '';
        
        foreach ($parts as $part) {
            switch ($part[0]) {
                case 'literal':
                case 'translated-literal':
                    $path .= $part[1];
                    break;
                
                case 'parameter':
                case 'translated-parameter':
                    if (!isset($params[$part[1]])) {
                        return null;
                    }
                    
                    $path .= $params[$part[1]];
                    break;
                
                case 'optional':
                    $path .= $this->buildPath($part[1], $params);
                    break;
            }
        }
        
        return $path;
    }

    /**
     * match(): defined by Route interface.
     *
     * @see    Route::match()
     * @param  Request $request
     * @return RouteMatch
     */
    public function match(Request $request, $pathOffset = null)
    {
        if (!method_exists($request, 'uri')) {
            return null;
        }

        $uri  = $request->uri();
        $path = $uri->getPath();
        
        if ($pathOffset !== null) {
            $result = preg_match('#\G' . $this->regex . '#i', $path, $matches, null, $pathOffset);
        } else {
            $result = preg_match('#^' . $this->regex . '$#i', $path, $matches);
        }
       
        if (!$result) {
            return null;
        }
        
        $matchedLength = strlen($matches[0]);
        
        foreach ($matches as $key => $value) {
            if (is_numeric($key) || is_int($key)) {
                unset($matches[$key]);
            }
        }

        $matches = array_merge($this->defaults, $matches);
        return new RouteMatch($matches, $this, $matchedLength);
    }

    /**
     * assemble(): Defined by Route interface.
     *
     * @see    Route::assemble()
     * @param  array $params
     * @param  array $options
     * @return mixed
     */
    public function assemble(array $params = null, array $options = null)
    {
        $path = $this->buildPath($this->parts, array_merge($this->defaults, $params));
        
        if ($path === null) {
            throw new Exception\InvalidArgumentException('Parameters missing');
        }
        
        return $path;
    }
}
