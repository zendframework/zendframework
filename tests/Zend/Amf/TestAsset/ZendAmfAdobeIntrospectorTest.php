<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Amf
 */

/**
 * Explicitly load this so that the Introspector can find it
 */
require_once __DIR__ . '/ZendAmfAdobeIntrospectorTestType.php';

/**
 * @category   Zend
 * @package    Zend_Amf
 * @subpackage UnitTests
 * @group      Zend_Amf
 */
class ZendAmfAdobeIntrospectorTest
{
    /**
     * Foobar
     *
     * @param  string $arg
     * @return ZendAmfAdobeIntrospectorTestType
     */
    public function foo($arg)
    {
    }
}

