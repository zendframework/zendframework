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
namespace Zf2Mvc\Router\Http;

use Traversable,
    Zend\Config\Config,
    Zend\Http\Request,
    Zf2Mvc\Router\Exception,
    Zf2Mvc\Router\Route,
    Zf2Mvc\Router\RouteMatch;

/**
 * Regex route.
 *
 * @package    Zend_Router
 * @subpackage Route
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
class Regex implements Route
{
    /**
     * Regex to match
     * 
     * @var string
     */
    protected $regex;

    /**
     * Default values
     *
     * @var array
     */
    protected $defaults;

    /**
     * Matches
     * 
     * @var array
     */
    protected $matches = array();

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

        if (!isset($options['regex']) || !is_string($options['regex'])) {
            throw new Exception\InvalidArgumentException('Regex not defined nor not a string');
        }
        
        $this->regex    = $options['regex'];
        $this->defaults = isset($options['defaults']) ? $options['defaults'] : array();
        $this->spec     = isset($options['spec']) ? $options['spec'] : "%s";
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
            $result = preg_match('#\G' . $this->regex . '#i', $path, $match, null, $pathOffset);
        } else {
            $result = preg_match('#^' . $this->regex . '$#i', $path, $match);
        }

        if (!$result) {
            return null;
        }

        foreach ($match as $key => $value) {
            if (is_numeric($key) || is_int($key)) {
                unset($match[$key]);
            }
        }

        $matches       = array_merge($this->defaults, $match);
        $this->matches = $matches;
        return new RouteMatch($matches, $this);
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
        $params  = (array) $params;
        $values  = array_merge($this->matches, $params);
        $escaped = array_map('urlencode', $values);
        return vsprintf($this->spec, array_values($escaped));
    }
}
