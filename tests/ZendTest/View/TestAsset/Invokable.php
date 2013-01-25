<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View\TestAsset;

use Zend\View\Helper\AbstractHelper as Helper;

class Invokable extends Helper
{
    /**
     * Invokable functor
     *
     * @param  string $message
     * @return string
     */
    public function __invoke($message)
    {
        return __METHOD__ . ': ' . $message;
    }
}
