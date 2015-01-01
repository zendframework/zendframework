<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Generator\TestAsset;

class TestClassWithManyProperties
{

    const FOO = 'foo';

    public static $fooStaticProperty = null;

    /**
     * @var bool
     */
    public $fooProperty = true;

    protected static $_barStaticProperty = 1;

    protected $_barProperty = 1.1115;

    private static $_bazStaticProperty = self::FOO;

    private $_bazProperty = array(true, false, true);

    protected $_complexType = array(
        5,
        'one' => 1,
        'two' => '2',
        array(
            'bar',
            'baz',
            //PHP_EOL
        )
    );

}
