<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Amf
 */

namespace ZendTest\Amf\TestAsset;

/**
 * @category   Zend
 * @package    Zend_Amf
 * @subpackage UnitTests
 * @group      Zend_Amf
 */
class IntrospectorTest
{
    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Overloading: get properties
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        $prop = '_' . $name;
        if (!isset($this->$prop)) {
            return null;
        }
        return $this->$prop;
    }

    /**
     * Foobar
     *
     * @param  string|int $arg
     * @return string|stdClass
     */
    public function foobar($arg)
    {
    }

    /**
     * Barbaz
     *
     * @param  ZendTest\Amf\TestAsset\IntrospectorTestCustomType $arg
     * @return boolean
     */
    public function barbaz($arg)
    {
    }

    /**
     * Bazbat
     *
     * @return ZendTest\Amf\TestAsset\IntrospectorTestExplicitType
     */
    public function bazbat()
    {
    }
}

