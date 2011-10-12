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
     * Regex to match.
     * 
     * @var string
     */
    protected $regex;

    /**
     * Default values.
     *
     * @var array
     */
    protected $defaults;

    /**
     * Matches.
     * 
     * @var array
     */
    protected $matches = array();

    /**
     * Specification for URL assembly.
     *
     * Parameters accepting subsitutions should be denoted as "%key%"
     * 
     * @var string
     */
    protected $spec;
    
    /**
     * Create a new regex route.
     * 
     * @param  string $regex
     * @param  string $spec
     * @param  array  $defaults 
     * @return void
     */
    public function __construct($regex, $spec, array $defaults = array())
    {
        $this->regex    = $regex;
        $this->spec     = $spec;
        $this->defaults = $defaults;
    }
    
    /**
     * factory(): defined by Route interface.
     *
     * @see    Route::factory()
     * @param  mixed $options
     * @return void
     */
    public static function factory(array $options = array())
    {
        if (!isset($options['regex'])) {
            throw new Exception\InvalidArgumentException('Missing "regex" in options array');
        }
        
        if (!isset($options['spec'])) {
            throw new Exception\InvalidArgumentException('Missing "spec" in options array');
        }

        if (!isset($options['defaults'])) {
            $options['defaults'] = array();
        }

        return new static($options['regex'], $options['spec'], $options['defaults']);
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
            $result = preg_match('#\G' . $this->regex . '#i', $path, $match, null, $pathOffset);
        } else {
            $result = preg_match('#^' . $this->regex . '$#i', $path, $match);
        }

        if (!$result) {
            return null;
        }
        
        $matchedLength = strlen($match[0]);

        foreach ($match as $key => $value) {
            if (is_numeric($key) || is_int($key)) {
                unset($match[$key]);
            }
        }

        $matches       = array_merge($this->defaults, $match);
        $this->matches = $matches;
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
        $params  = (array) $params;
        $values  = array_merge($this->matches, $params);

        $url = $this->spec;
        foreach ($values as $key => $value) {
            $spec = '%' . $key . '%';
            if (strstr($url, $spec)) {
                $url = str_replace($spec, urlencode($value), $url);
            }
        }
        return $url;
    }
}
