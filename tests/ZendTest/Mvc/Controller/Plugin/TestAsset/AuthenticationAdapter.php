<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\Mvc\Controller\Plugin\TestAsset;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage UnitTests
 */
class AuthenticationAdapter implements AdapterInterface
{
    protected $identity;

    public function setIdentity($identity)
    {
        $this->identity = $identity;
    }

    public function authenticate()
    {
        return new Result(Result::SUCCESS, $this->identity);
    }
}
