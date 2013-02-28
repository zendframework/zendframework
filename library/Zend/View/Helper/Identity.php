<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\View\Helper;

use Zend\Authentication\AuthenticationService;
use Zend\View\Exception;

/**
 * View helper plugin to fetch the authenticated identity.
 */
class Identity extends AbstractHelper
{
    /**
     * @var AuthenticationService
     */
    protected $authenticationService;

    /**
     * @return AuthenticationService
     */
    public function getAuthenticationService()
    {
        return $this->authenticationService;
    }

    /**
     * @param AuthenticationService $authenticationService
     */
    public function setAuthenticationService(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    /**
     * Retrieve the current identity, if any.
     *
     * If none available, returns null.
     *
     * @return mixed|null
     * @throws Exception\RuntimeException
     */
    public function __invoke()
    {
        if (!$this->authenticationService instanceof AuthenticationService){
            throw new Exception\RuntimeException('No AuthenticationService instance provided');
        }

        if (!$this->authenticationService->hasIdentity()) {
            return null;
        }
        return $this->authenticationService->getIdentity();
    }
}
