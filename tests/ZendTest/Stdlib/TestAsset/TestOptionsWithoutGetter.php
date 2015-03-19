<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\TestAsset;

use Zend\Stdlib\AbstractOptions;

/**
 * Dummy TestOptions used to test Stdlib\Options
 */
class TestOptionsWithoutGetter extends AbstractOptions
{
    protected $foo;

    public function setFoo($value)
    {
        $this->foo = $value;
    }
}
