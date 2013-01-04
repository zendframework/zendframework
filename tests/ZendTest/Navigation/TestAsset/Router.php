<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Navigation
 */

namespace ZendTest\Navigation\TestAsset;

/**
 * @category   Zend
 * @package    Zend_Navigation
 * @subpackage UnitTests
 */
class Router extends \Zend\Mvc\Router\Http\TreeRouteStack
{
    const RETURN_URL = 'spotify:track:2nd6CTjR9zjHGT0QtpfLHe';

    public function assemble(array $params = array(), array $options = array())
    {
        return self::RETURN_URL;
    }
}
