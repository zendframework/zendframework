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

$function1 = function()
{
    return 'function1';
};

$function2 = function() { return 'function2'; };

$function3 = function($arg) {
    return 'function3';
};

$function4 = function()
{
    $closure = function() { return 'bar'; };
    return 'function4';
};

$list1 = array('closure' => function() { return 'function5'; });

$list2 = array(function() { return 'function6'; });

$list3 = array(function() { return $c = function() { return 'function7'; }; return $c(); });

$function8 = function() use ($list1) { return 'function 8'; };

/**
 * closure doc block
 */
$function9 = function() {};

eval("\$function10 = function() { return 'function10'; };");
