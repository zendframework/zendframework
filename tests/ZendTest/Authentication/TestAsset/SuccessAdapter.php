<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Authentication
 */

namespace ZendTest\Authentication\TestAsset;

use Zend\Authentication\Adapter\AbstractAdapter as AuthenticationAdapter;
use Zend\Authentication\Result as AuthenticationResult;

class SuccessAdapter extends AuthenticationAdapter
{
    public function authenticate()
    {
        return new AuthenticationResult(true, 'someIdentity');
    }
}
