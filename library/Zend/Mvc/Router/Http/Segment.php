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
 * @package    Zend_Mvc_Router
 * @subpackage Http
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Mvc\Router\Http;

use Traversable,
    Zend\Stdlib\IteratorToArray,
    Zend\Stdlib\RequestDescription as Request,
    Zend\Mvc\Router\Exception,
    Zend\Mvc\Router\Route as BaseRoute;

/**
 * Segment route.
 *
 * @package    Zend_Mvc_Router
 * @subpackage Http
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
class Segment implements BaseRoute
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
     * Create a new regex route.
     * 
     * @param  string $route
     * @param  array  $constraints 
     * @param  array  $defaults 
     * @return void
     */
    public function __construct($route, array $constraints = array(), array $defaults = array())
    {
        $this->defaults = $defaults;
        $this->parts    = $this->parseRouteDefinition($route);
        $this->regex    = $this->buildRegex($this->parts, $constraints) . '(?:/|$)?';
    }
    
    /**
     * factory(): defined by Route interface.
     *
     * @see    Route::factory()
     * @param  array|Traversable $options
     * @return void
     */
    public static function factory($options = array())
    {
        if (!is_array($options) && !$options instanceof Traversable) {
            throw new Exception\InvalidArgumentException(__METHOD__ . ' expects an array or Traversable set of options');
        }

        // Convert options to array if Traversable object not implementing ArrayAccess
        if ($options instanceof Traversable && !$options instanceof ArrayAccess) {
            $options = IteratorToArray::convert($options);
        }

        if (!isset($options['route'])) {
            throw new Exception\InvalidArgumentException('Missing "route" in options array');
        }

        if (!isset($options['constraints'])) {
            $options['constraints'] = array();
        }
        
        if (!isset($options['defaults'])) {
            $options['defaults'] = array();
        }

        return new static($options['route'], $options['constraints'], $options['defaults']);
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
                    if (!preg_match('(\G(?<name>[^:/{\[\]]+)(?:{(?<delimiters>[^}]+)})?:?)', $def, $matches, 0, $currentPos)) {
                        throw new Exception\RuntimeException('Found empty parameter name');
                    }
                    
                    $levelParts[$level][] = array('parameter', $matches['name'], isset($matches['delimiters']) ? $matches['delimiters'] : null);
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
     * @param  array $constraints
     * @return string
     */
    protected function buildRegex(array $parts, array $constraints)
    {
        $regex = '';
        
        foreach ($parts as $part) {
            switch ($part[0]) {
                case 'literal':
                    $regex .= preg_quote($part[1]);
                    break;
                               
                case 'parameter':
                    if (isset($constraints[$part[1]])) {
                        $regex .= '(?<' . $part[1] . '>' . $constraints[$part[1]] . ')';
                    } elseif ($part[2] === null) {
                        $regex .= '(?<' . $part[1] . '>[^/]+)';
                    } else {
                        $regex .= '(?<' . $part[1] . '>[^' . $part[2] . ']+)';
                    }
                    break;
                
                case 'optional':
                    $regex .= '(?:' . $this->buildRegex($part[1], $constraints) . ')?';
                    break;
                
                case 'translated-literal':
                    throw new Exception\RuntimeException('Translated literals are not implemented yet');
                    break;
                
                case 'translated-parameter':
                    throw new Exception\RuntimeException('Translated parameters are not implemented yet');
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
            $result = preg_match('(\G' . $this->regex . ')', $path, $matches, null, $pathOffset);
        } else {
            $result = preg_match('(^' . $this->regex . '$)', $path, $matches);
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

        return new RouteMatch(array_merge($this->defaults, $matches), $matchedLength);
    }

    /**
     * assemble(): Defined by Route interface.
     *
     * @see    Route::assemble()
     * @param  array $params
     * @param  array $options
     * @return mixed
     */
    public function assemble(array $params = array(), array $options = array())
    {
        $path = $this->buildPath($this->parts, array_merge($this->defaults, $params));
        
        if ($path === null) {
            throw new Exception\InvalidArgumentException('Parameters missing');
        }
        
        return $path;
    }
}
