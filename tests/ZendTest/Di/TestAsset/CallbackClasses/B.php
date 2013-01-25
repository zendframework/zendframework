<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Di
 */

namespace ZendTest\Di\TestAsset\CallbackClasses;

class B
{
    public $c, $params = null;

    public static function factory(C $c, array $params = array())
    {
        $b = new B();
        $b->c = $c;
        $b->params = $params;
        return $b;
    }

    protected function __construct()
    {
        // no dice
    }
}
