<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Session
 */

namespace ZendTest\Session\SaveHandler;

use Zend\Session\SaveHandler\MongoDBOptions;

/**
 * @category   Zend
 * @package    Zend_Session
 * @subpackage UnitTests
 * @group      Zend_Session
 */
class MongoDBOptionsTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $options = new MongoDBOptions();
        $this->assertNull($options->getDatabase());
        $this->assertNull($options->getCollection());
        $this->assertEquals(array('safe' => true), $options->getSaveOptions());
        $this->assertEquals('name', $options->getNameField());
        $this->assertEquals('data', $options->getDataField());
        $this->assertEquals('lifetime', $options->getLifetimeField());
        $this->assertEquals('modified', $options->getModifiedField());
    }

    public function testSetConstructor()
    {
        $options = new MongoDBOptions(array(
            'database' => 'testDatabase',
            'collection' => 'testCollection',
            'saveOptions' => array('safe' => 2),
            'nameField' => 'testName',
            'dataField' => 'testData',
            'lifetimeField' => 'testLifetime',
            'modifiedField' => 'testModified',
        ));

        $this->assertEquals('testDatabase', $options->getDatabase());
        $this->assertEquals('testCollection', $options->getCollection());
        $this->assertEquals(array('safe' => 2), $options->getSaveOptions());
        $this->assertEquals('testName', $options->getNameField());
        $this->assertEquals('testData', $options->getDataField());
        $this->assertEquals('testLifetime', $options->getLifetimeField());
        $this->assertEquals('testModified', $options->getModifiedField());
    }

    public function testSetters()
    {
        $options = new MongoDBOptions();
        $options->setDatabase('testDatabase')
                ->setCollection('testCollection')
                ->setSaveOptions(array('safe' => 2))
                ->setNameField('testName')
                ->setDataField('testData')
                ->setLifetimeField('testLifetime')
                ->setModifiedField('testModified');

        $this->assertEquals('testDatabase', $options->getDatabase());
        $this->assertEquals('testCollection', $options->getCollection());
        $this->assertEquals(array('safe' => 2), $options->getSaveOptions());
        $this->assertEquals('testName', $options->getNameField());
        $this->assertEquals('testData', $options->getDataField());
        $this->assertEquals('testLifetime', $options->getLifetimeField());
        $this->assertEquals('testModified', $options->getModifiedField());
    }

    /**
     * @expectedException Zend\Session\Exception\InvalidArgumentException
     */
    public function testInvalidDatabase()
    {
        $options = new MongoDBOptions(array(
            'database' => null,
        ));
    }

    /**
     * @expectedException Zend\Session\Exception\InvalidArgumentException
     */
    public function testInvalidCollection()
    {
        $options = new MongoDBOptions(array(
            'collection' => null,
        ));
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testInvalidSaveOptions()
    {
        $options = new MongoDBOptions(array(
            'saveOptions' => null,
        ));
    }

    /**
     * @expectedException Zend\Session\Exception\InvalidArgumentException
     */
    public function testInvalidNameField()
    {
        $options = new MongoDBOptions(array(
            'nameField' => null,
        ));
    }

    /**
     * @expectedException Zend\Session\Exception\InvalidArgumentException
     */
    public function testInvalidModifiedField()
    {
        $options = new MongoDBOptions(array(
            'modifiedField' => null,
        ));
    }

    /**
     * @expectedException Zend\Session\Exception\InvalidArgumentException
     */
    public function testInvalidLifetimeField()
    {
        $options = new MongoDBOptions(array(
            'lifetimeField' => null,
        ));
    }

    /**
     * @expectedException Zend\Session\Exception\InvalidArgumentException
     */
    public function testInvalidDataField()
    {
        $options = new MongoDBOptions(array(
            'dataField' => null,
        ));
    }
}
