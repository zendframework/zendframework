<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace Zend\Mvc\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mvc\Exception\RuntimeException;
use Zend\Mvc\InjectApplicationEventInterface;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Controller
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
     * Retrieve a named $_FILES value
     *
     * @param  string $name
     * @param  mixed $default
     * @return array|\ArrayAccess|null
     */
    public function fromFiles($name, $default = null)
    {
        return $this->getController()->getRequest()->getFiles($name, $default);
    }

    /**
     * Get a header
     *
     * @param  string $header
     * @param  mixed $default
     * @return null|\Zend\Http\Header\HeaderInterface
     */
    public function fromHeader($header, $default = null)
    {
        return $this->getController()->getRequest()->getHeaders($header, $default);
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
        return $this->getController()->getRequest()->getPost($param, $default);
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
        return $this->getController()->getRequest()->getQuery($param, $default);
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
}
