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
 * @package    Zend_Mvc
 * @subpackage Controller
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Mvc\Controller\Plugin;

use Zend\Mvc\Exception\RuntimeException;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mvc\InjectApplicationEventInterface;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Controller
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Params extends AbstractPlugin
{
    /**
     * Grabs a param from route match by default.
     *
     * @param string $param
     * @param mixed $default
     * @return mixed
     */
    public function __invoke($param = NULL, $default = null)
    {
        if ($param === NULL) {
            return $this;
        }
        return $this->fromRoute($param, $default);
    }

    /**
     * Get a param from the route match.
     *
     * @param string $param
     * @param mixed $default
     * @return mixed
     * @throws RuntimeException
     */
    public function fromRoute($param, $default = null)
    {
        $controller = $this->getController();

        if (!$controller instanceof InjectApplicationEventInterface) {
            throw new RuntimeException(
                'Controllers must implement Zend\Mvc\InjectApplicationEventInterface to use this plugin.'
            );
        }

        return $controller->getEvent()->getRouteMatch()->getParam($param, $default);
    }

    /**
     * Get a param from POST.
     *
     * @param string $param
     * @param mixed $default
     * @return mixed
     */
    public function fromPost($param, $default = null)
    {
        return $this->getController()->getRequest()->getPost()->get($param, $default);
    }

    /**
     * Get a param from QUERY.
     *
     * @param string $param
     * @param mixed $default
     * @return mixed
     */
    public function fromQuery($param, $default = null)
    {
        return $this->getController()->getRequest()->getQuery()->get($param, $default);
    }
}