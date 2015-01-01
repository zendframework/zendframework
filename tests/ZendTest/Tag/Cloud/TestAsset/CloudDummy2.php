<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Tag\Cloud\TestAsset;

class CloudDummy2 extends \Zend\Tag\Cloud\Decorator\HtmlCloud
{
    protected $_foo;

    public function setFoo($value)
    {
        $this->_foo = $value;
    }

    public function getFoo()
    {
        return $this->_foo;
    }
}
