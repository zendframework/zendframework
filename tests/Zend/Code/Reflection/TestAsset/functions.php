<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Code\Reflection\TestAsset;

function function1() {
    return 'foo';
}


/**
 * Zend Function Two
 *
 * This is the long description for funciton two
 *
 * @param unknown_type $one
 * @param unknown_type $two
 * @return string
 */
function function2($one, $two = 'two') {

    return 'blah';
}


/**
 * Enter description here...
 *
 * @param string $one
 * @param int $two
 * @return true
 */
function function6($one, $two = 2) {
    return true;
}

