<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Code
 */

namespace ZendTest\Code\Reflection\TestAsset;

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
