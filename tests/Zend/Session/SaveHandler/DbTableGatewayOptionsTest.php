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
 * @package    Zend_Session
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Session\SaveHandler;

use Zend\Session\SaveHandler\DbTableGatewayOptions;

/**
 * Unit testing for DbTableGatewayOptions
 *
 * @category   Zend
 * @package    Zend_Session
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Session
 */
class DbTableGatewayOptionsTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $options = new DbTableGatewayOptions();
        $this->assertEquals('id', $options->getIdColumn());
        $this->assertEquals('name', $options->getNameColumn());
        $this->assertEquals('modified', $options->getModifiedColumn());
        $this->assertEquals('lifetime', $options->getLifetimeColumn());
        $this->assertEquals('data', $options->getDataColumn());
    }

    public function testSetConstructor()
    {
        $options = new DbTableGatewayOptions(array(
            'idColumn' => 'testId',
            'nameColumn' => 'testName',
            'modifiedColumn' => 'testModified',
            'lifetimeColumn' => 'testLifetime',
            'dataColumn' => 'testData',
        ));

        $this->assertEquals('testId', $options->getIdColumn());
        $this->assertEquals('testName', $options->getNameColumn());
        $this->assertEquals('testModified', $options->getModifiedColumn());
        $this->assertEquals('testLifetime', $options->getLifetimeColumn());
        $this->assertEquals('testData', $options->getDataColumn());
    }

    public function testSetters()
    {
        $options = new DbTableGatewayOptions();
        $options->setIdColumn('testId')
                ->setNameColumn('testName')
                ->setModifiedColumn('testModified')
                ->setLifetimeColumn('testLifetime')
                ->setDataColumn('testData');

        $this->assertEquals('testId', $options->getIdColumn());
        $this->assertEquals('testName', $options->getNameColumn());
        $this->assertEquals('testModified', $options->getModifiedColumn());
        $this->assertEquals('testLifetime', $options->getLifetimeColumn());
        $this->assertEquals('testData', $options->getDataColumn());
    }

    /**
     * @expectedException Zend\Session\Exception\InvalidArgumentException
     */
    public function testInvalidIdColumn()
    {
        $options = new DbTableGatewayOptions(array(
            'idColumn' => null,
        ));
    }

    /**
     * @expectedException Zend\Session\Exception\InvalidArgumentException
     */
    public function testInvalidNameColumn()
    {
        $options = new DbTableGatewayOptions(array(
            'nameColumn' => null,
        ));
    }

    /**
     * @expectedException Zend\Session\Exception\InvalidArgumentException
     */
    public function testInvalidModifiedColumn()
    {
        $options = new DbTableGatewayOptions(array(
            'modifiedColumn' => null,
        ));
    }

    /**
     * @expectedException Zend\Session\Exception\InvalidArgumentException
     */
    public function testInvalidLifetimeColumn()
    {
        $options = new DbTableGatewayOptions(array(
            'lifetimeColumn' => null,
        ));
    }

    /**
     * @expectedException Zend\Session\Exception\InvalidArgumentException
     */
    public function testInvalidDataColumn()
    {
        $options = new DbTableGatewayOptions(array(
            'dataColumn' => null,
        ));
    }
}
