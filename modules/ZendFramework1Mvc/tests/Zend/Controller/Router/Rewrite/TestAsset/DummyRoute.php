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
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Controller\Router\Rewrite\TestAsset;
use Zend\Controller\Router\Rewrite\Route\Route;
use Zend\Controller\Request\HTTP as HTTPRequest;

/**
 * Dummy route
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DummyRoute implements Route
{
    /**
     * match(): defined by Route interface
     *
     * @see    Route::match()
     * @param  HTTPRequest $request
     * @param  integer     $pathOffset
     * @return boolean
     */
    public function match(HTTPRequest $request, $pathOffset = null)
    {
        return true;
    }

    /**
     * assemble(): defined by Route interface
     *
     * @see    Route::assemble()
     * @param  array $params
     * @param  array $options
     * @return string
     */
    public function assemble(array $params = null, array $options = null)
    {
        return '';
    }
}
