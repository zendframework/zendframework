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
    Zend\Mvc\Router\Exception;

/**
 * QueryString route.
 *
 * @package    Zend_Mvc_Router
 * @subpackage Http
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
class QueryString implements Route
{
    /**
     * Delimiter between keys and values.
     *
     * @var string
     */
    protected $keyValueDelimiter;

    /**
     * Delimtier before parameters.
     *
     * @var array
     */
    protected $paramDelimiter;
    
    /**
     * Boundary character for query string
     * @var string
     */
    protected $queryBoundary;
    
    /**
     * Default values.
     * 
     * @var array
     */
    protected $defaults;
    
    /**
     * List of assembled parameters.
     * 
     * @var array
     */
    protected $assembledParams = array();

    /**
     * Create a new wildcard route.
     * 
     * @param  string $keyValueDelimiter
     * @param  string $paramDelimiter
     * @param  array  $defaults
     * @return void
     */
    public function __construct($keyValueDelimiter = '=', $paramDelimiter = '&', array $defaults = array(), $queryBoundary = '?')
    {
        $this->keyValueDelimiter = $keyValueDelimiter;
        $this->queryBoundary = $queryBoundary;
        $this->paramDelimiter    = $paramDelimiter;
        $this->defaults          = $defaults;
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
        if ($options instanceof Traversable) {
            $options = IteratorToArray::convert($options);
        } elseif (!is_array($options)) {
            throw new Exception\InvalidArgumentException(__METHOD__ . ' expects an array or Traversable set of options');
        }

        if (!isset($options['key_value_delimiter'])) {
            $options['key_value_delimiter'] = '=';
        }

        if (!isset($options['param_delimiter'])) {
            $options['param_delimiter'] = '&';
        }
        
        if (!isset($options['query_boundary'])) {
            $options['query_boundary'] = '?';
        }
        
        if (!isset($options['defaults'])) {
            $options['defaults'] = array();
        }

        return new static($options['key_value_delimiter'], $options['param_delimiter'], $options['defaults']);
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
        // return null as not matching query string
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
    public function assemble(array $params = array(), array $options = array())
    {
        $elements              = array();
        $mergedParams          = array_merge($this->defaults, $params);
        $this->assembledParams = array();

        if ($mergedParams) {
            foreach ($mergedParams as $key => $value) {
                $elements[] = urlencode($key) . $this->keyValueDelimiter . urlencode($value);

                $this->assembledParams[] = $key;
            }

            return $this->queryBoundary . implode($this->paramDelimiter, $elements);
        }
        
        return '';
    }
    
    /**
     * getAssembledParams(): defined by Route interface.
     * 
     * @see    Route::getAssembledParams
     * @return array
     */
    public function getAssembledParams()
    {
        return $this->assembledParams;
    }
}
