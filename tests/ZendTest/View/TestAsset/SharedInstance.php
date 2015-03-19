<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\View\TestAsset;

use Zend\View\Helper\AbstractHelper as Helper;

class SharedInstance extends Helper
{
    protected $count = 0;

    /**
     * Invokable functor
     *
     * @return int
     */
    public function __invoke()
    {
        $this->count++;

        return $this->count;
    }
}
