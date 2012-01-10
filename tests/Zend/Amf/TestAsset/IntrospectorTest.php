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
 * @package    Zend_Amf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Amf\TestAsset;

/**
 * @category   Zend
 * @package    Zend_Amf
 * @subpackage UnitTests
 * @group      Zend_Amf
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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

