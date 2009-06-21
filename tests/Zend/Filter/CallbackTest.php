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
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * @see Zend_Filter_Callback
 */
require_once 'Zend/Filter/Callback.php';

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Filter_CallbackTest extends PHPUnit_Framework_TestCase
{
    public function testObjectCallback()
    {
        $filter = new Zend_Filter_Callback(array($this, 'objectCallback'));
        $this->assertEquals('objectCallback-test', $filter->filter('test'));
    }

    public function testStaticCallback()
    {
        $filter = new Zend_Filter_Callback(
            array('Zend_Filter_CallbackTest', 'staticCallback')
        );
        $this->assertEquals('staticCallback-test', $filter->filter('test'));
    }

    public function testSettingDefaultOptions()
    {
        $filter = new Zend_Filter_Callback(array($this, 'objectCallback'), 'options');
        $this->assertEquals('options', $filter->getOptions());
        $this->assertEquals('objectCallback-test', $filter->filter('test'));
    }

    public function testSettingDefaultOptionsAfterwards()
    {
        $filter = new Zend_Filter_Callback(array($this, 'objectCallback'));
        $filter->setOptions('options');
        $this->assertEquals('options', $filter->getOptions());
        $this->assertEquals('objectCallback-test', $filter->filter('test'));
    }

    public function testCallbackWithStringParameter()
    {
        $filter = new Zend_Filter_Callback('strrev');
        $this->assertEquals('!olleH', $filter->filter('Hello!'));
    }

    public function testCallbackWithArrayParameters()
    {
        $filter = new Zend_Filter_Callback('strrev');
        $this->assertEquals('!olleH', $filter->filter('Hello!'));
    }

    public function objectCallback($value)
    {
        return 'objectCallback-' . $value;
    }

    public static function staticCallback($value)
    {
        return 'staticCallback-' . $value;
    }
}