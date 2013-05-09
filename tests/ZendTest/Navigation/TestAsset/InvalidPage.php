<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Navigation
 */

namespace ZendTest\Navigation\TestAsset;

/**
 * @category   Zend
 * @package    Zend_Navigation
 * @subpackage UnitTests
 */
class InvalidPage
{
    /**
     * Returns the page's href
     *
     * @return string
     */
    public function getHref()
    {
        return '#';
    }
}
