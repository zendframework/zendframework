<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Amf
 */

namespace ZendTest\Amf\TestAsset\Authentication;

use Zend\Acl\Acl as ZendAcl;

class Acl
{
    public function hello()
    {
        return "hello!";
    }

    public function hello2()
    {
        return "hello2!";
    }

    public function initAcl(ZendAcl $acl)
    {
        $acl->allow("testrole", null, "hello");
        $acl->allow("testrole2", null, "hello2");
        return true;
    }
}
