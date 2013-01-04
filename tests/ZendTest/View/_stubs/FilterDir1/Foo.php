<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View\Filter;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 */
class Foo
{
    public function filter($buffer)
    {
        return 'foo';
    }
}
