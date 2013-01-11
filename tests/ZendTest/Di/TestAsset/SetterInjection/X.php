<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Di
 */

namespace ZendTest\Di\TestAsset\SetterInjection;

class X
{
    public $one = null;
    public $two = null;
    public function setOne($one)
    {
        $this->one = $one;
    }
    public function setTwo($two)
    {
        $this->two = $two;
    }
}
