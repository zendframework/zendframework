<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace ZendTest\Mvc\Router\TestAsset;

use Zend\Mvc\Router\RouteInterface;
use Zend\Mvc\Router\RouteMatch;
use Zend\Stdlib\RequestInterface;

/**
 * Dummy route.
 *
 * @category   Zend
 * @package    Zend_Mvc_Router
 * @subpackage UnitTests
 */
class DummyRouteWithParam extends DummyRoute
{
    /**
     * match(): defined by RouteInterface interface.
     *
     * @see    Route::match()
     * @param  RequestInterface $request
     * @return RouteMatch
     */
    public function match(RequestInterface $request)
    {
        return new RouteMatch(array('foo' => 'bar'));
    }

    /**
     * assemble(): defined by RouteInterface interface.
     *
     * @see    Route::assemble()
     * @param  array $params
     * @param  array $options
     * @return mixed
     */
    public function assemble(array $params = null, array $options = null)
    {
        if (isset($params['foo'])) {
            return $params['foo'];
        }

        return '';
    }
}
