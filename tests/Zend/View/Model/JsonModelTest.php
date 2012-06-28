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
 * @package    Zend_View
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\View\Model;

use ArrayObject;
use stdClass,
    PHPUnit_Framework_TestCase as TestCase,
    Zend\View\Model\JsonModel,
    Zend\View\Variables as ViewVariables,
    Zend\Json\Json;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class JsonModelTest extends TestCase
{
    public function testAllowsEmptyConstructor()
    {
        $model = new JsonModel();
        $this->assertInstanceOf('Zend\View\Variables', $model->getVariables());
        $this->assertEquals(array(), $model->getOptions());
    }

    public function testCanSerializeVariablesToJson()
    {
        $array = array('foo' => 'bar');
        $model = new JsonModel($array);
        $this->assertEquals($array, $model->getVariables());
        $this->assertEquals(Json::encode($array), $model->serialize());
    }


    public function testCanSerializeWithJsonpCallback()
    {
        $array = array('foo' => 'bar');
        $model = new JsonModel($array);
        $model->setJsonpCallback('callback');
        $this->assertEquals('callback(' . Json::encode($array) . ');', $model->serialize());
    }
}
