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
    Zend\Http\Request,
    Zend\Mvc\Router\Exception,
    Zend\Mvc\Router\Route,
    Zend\Mvc\Router\RouteMatch;

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
     * Route to match.
     * 
     * @var string
     */
    protected $route;

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
        
        $this->route    = $options['route'];
        $this->defaults = $options['defaults'];
        
        $this->parts = $this->parseRouteDefinition($options['route']);
        $this->regex = $this->buildRegex($this->parts);
        
        var_dump($this->parts);
        var_dump($this->regex);
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
                    $regex .= '(?<' . $part[1] . '>.*?)';
                    break;
                
                case 'optional':
                    $regex .= '(?:' . $this->buildRegex($part[1]) . ')?';
                    break;
            }
        }
        
        return $regex;
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
        $uri  = $request->uri();
        $path = $uri->getPath();
        
        if ($pathOffset !== null) {
            if (strpos($path, $this->route) === $pathOffset) {
                return new RouteMatch($this->defaults, $this);
            }
        } else {
            if ($path === $this->route) {
                return new RouteMatch($this->defaults, $this);
            }
        }

        return null;
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
        return $this->route;
    }
}
