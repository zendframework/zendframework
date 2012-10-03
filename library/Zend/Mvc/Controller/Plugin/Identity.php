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

use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Exception;

/**
 * Controller plugin to fetch the authenticated identity.
 *
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Controller
 */
class Identity extends AbstractPlugin
{

    /**
     *
     * @var \Zend\Authentication\AuthenticationService
     */
    protected $authenticationService;

    /**
     *
     * @return \Zend\Authentication\AuthenticationService
     */
    public function getAuthenticationService()
    {
        return $this->authenticationService;
    }

    /**
     *
     * @param \Zend\Authentication\AuthenticationService $authetnicationService
     */
    public function setAuthenticationService(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    /**
     *
     * @return mixed | null
     * @throws Exception\RuntimeException
     */
    public function __invoke()
    {
        if ( ! $this->authenticationService instanceof AuthenticationService){
            throw new Exception\RuntimeException('No AuthenticationService instance provided');
        }
        if ($this->authenticationService->hasIdentity()) {
            return $this->authenticationService->getIdentity();
        }
        return null;
    }
}