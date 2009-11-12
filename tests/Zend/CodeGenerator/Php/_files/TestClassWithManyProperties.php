<?php

class Zend_CodeGenerator_Php_TestClassWithManyProperties
{

    const FOO = 'foo';

    public static $fooStaticProperty = null;

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
