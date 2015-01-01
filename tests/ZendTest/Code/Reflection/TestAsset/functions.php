<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Reflection\TestAsset;

/***
 * /!\ Don't fix this file with the coding style.
 * The class Zend\Code\Reflection\FunctionReflection must parse a lot of closure formats
 */

function function1()
{
    return 'function1';
}


/**
 * Zend Function Two
 *
 * This is the long description for funciton two
 *
 * @param string $one
 * @param string $two
 * @return string
 */
function function2($one, $two = 'two')
{
    return 'blah';
}


/**
 * Enter description here...
 *
 * @param string $one
 * @param int $two
 * @return true
 */
function function3($one, $two = 2)
{
    return true;
}

function function4($arg) {
    return 'function4';
}

function function5() { return 'function5'; }

function function6()
{
    $closure = function() { return 'bar'; };
    return 'function6';
}

$foo = 'foo'; function function7() { return 'function7'; }

function function8() { return 'function8'; } function function9() { return 'function9'; }

function function10() { $closure = function() { return 'function10'; }; return $closure(); } function function11() { return 'function11'; }

function function12() {}
