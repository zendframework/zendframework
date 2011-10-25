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
    Zend\Mvc\Router\Route;

/**
 * Wildcard route.
 *
 * @package    Zend_Mvc_Router
 * @subpackage Http
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
class Wildcard implements Route
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
     * Create a new wildcard route.
     * 
     * @param  string $keyValueDelimiter
     * @param  string $paramDelimiter
     * @return void
     */
    public function __construct($keyValueDelimiter = '/', $paramDelimiter = '/')
    {
        $this->keyValueDelimiter = $keyValueDelimiter;
        $this->paramDelimiter    = $paramDelimiter;
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

        if (!isset($options['key_value_delimiter'])) {
            $options['key_value_delimiter'] = '/';
        }

        if (!isset($options['param_delimiter'])) {
            $options['param_delimiter'] = '/';
        }

        return new static($options['key_value_delimiter'], $options['param_delimiter']);
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
            $path = substr($path, $pathOffset);
        }

        $matches = array();
        $params  = explode($this->paramDelimiter, $path);

        if ($params) {
            if ($params[0] !== '') {
                return null;
            }

            array_shift($params);

            $count = count($params);

            for ($i = 0; $i < $count; $i += 2) {
                $matches[urldecode($params[$i])] = (isset($params[$i + 1]) ? urldecode($params[$i + 1]) : null);
            }
        }

        return new RouteMatch($matches, strlen($path));
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
        $elements = array();

        foreach ($params as $key => $value) {
            $elements[] = urlencode($key) . $this->keyValueDelimiter . urlencode($value);
        }

        return $this->paramDelimiter . implode($this->paramDelimiter, $elements);
    }
}
