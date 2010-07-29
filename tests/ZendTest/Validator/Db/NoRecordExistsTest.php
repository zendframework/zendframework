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
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Validator\Db;

use Zend\Db\Table\AbstractTable,
    Zend\Validator\Db\NoRecordExists as NoRecordExistsValidator;


/**
 * @category   Zend
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */
class NoRecordExistsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_adapterHasResult;

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_adapterNoResult;

    /**
     * Set up test configuration
     *
     * @return void
     */
    public function setUp()
    {
        $this->_adapterHasResult = new TestAsset\MockHasResult();
        $this->_adapterNoResult = new TestAsset\MockNoResult();
    }

    /**
     * Test basic function of RecordExists (no exclusion)
     *
     * @return void
     */
    public function testBasicFindsRecord()
    {
        AbstractTable::setDefaultAdapter($this->_adapterHasResult);
        $validator = new NoRecordExistsValidator('users', 'field1');
        $this->assertFalse($validator->isValid('value1'));
    }

    /**
     * Test basic function of RecordExists (no exclusion)
     *
     * @return void
     */
    public function testBasicFindsNoRecord()
    {
        AbstractTable::setDefaultAdapter($this->_adapterNoResult);
        $validator = new NoRecordExistsValidator('users', 'field1');
        $this->assertTrue($validator->isValid('nosuchvalue'));
    }

    /**
     * Test the exclusion function
     *
     * @return void
     */
    public function testExcludeWithArray()
    {
        AbstractTable::setDefaultAdapter($this->_adapterHasResult);
        $validator = new NoRecordExistsValidator('users', 'field1', array('field' => 'id', 'value' => 1));
        $this->assertFalse($validator->isValid('value3'));
    }

    /**
     * Test the exclusion function
     * with an array
     *
     * @return void
     */
    public function testExcludeWithArrayNoRecord()
    {
        AbstractTable::setDefaultAdapter($this->_adapterNoResult);
        $validator = new NoRecordExistsValidator('users', 'field1', array('field' => 'id', 'value' => 1));
        $this->assertTrue($validator->isValid('nosuchvalue'));
    }

    /**
     * Test the exclusion function
     * with a string
     *
     * @return void
     */
    public function testExcludeWithString()
    {
        AbstractTable::setDefaultAdapter($this->_adapterHasResult);
        $validator = new NoRecordExistsValidator('users', 'field1', 'id != 1');
        $this->assertFalse($validator->isValid('value3'));
    }

    /**
     * Test the exclusion function
     * with a string
     *
     * @return void
     */
    public function testExcludeWithStringNoRecord()
    {
        AbstractTable::setDefaultAdapter($this->_adapterNoResult);
        $validator = new NoRecordExistsValidator('users', 'field1', 'id != 1');
        $this->assertTrue($validator->isValid('nosuchvalue'));
    }

    /**
     * Test that the class throws an exception if no adapter is provided
     * and no default is set.
     *
     * @return void
     */
    public function testThrowsExceptionWithNoAdapter()
    {
        AbstractTable::setDefaultAdapter(null);
        $validator = new NoRecordExistsValidator('users', 'field1', 'id != 1');
        $this->setExpectedException('Zend\Validator\Exception');
        $valid = $validator->isValid('nosuchvalue');
    }

    /**
     * Test that schemas are supported and run without error
     *
     * @return void
     */
    public function testWithSchema()
    {
        AbstractTable::setDefaultAdapter($this->_adapterHasResult);
        $validator = new NoRecordExistsValidator(array('table' => 'users',
                                                               'schema' => 'my'),
                                                         'field1');
        $this->assertFalse($validator->isValid('value1'));
    }

    /**
     * Test that schemas are supported and run without error
     *
     * @return void
     */
    public function testWithSchemaNoResult()
    {
        AbstractTable::setDefaultAdapter($this->_adapterNoResult);
        $validator = new NoRecordExistsValidator(array('table' => 'users',
                                                               'schema' => 'my'),
                                                         'field1');
        $this->assertTrue($validator->isValid('value1'));
    }

    /**
     * Test when adapter is provided
     *
     * @return void
     */
    public function testAdapterProvided()
    {
        //clear the default adapter to ensure provided one is used
        AbstractTable::setDefaultAdapter(null);
        try {
            $validator = new NoRecordExistsValidator('users', 'field1', null, $this->_adapterHasResult);
            $this->assertFalse($validator->isValid('value1'));
        } catch (\Exception $e) {
            $this->markTestSkipped('No database available');
        }
    }

    /**
     * Test when adapter is provided
     *
     * @return void
     */
    public function testAdapterProvidedNoResult()
    {
        //clear the default adapter to ensure provided one is used
        AbstractTable::setDefaultAdapter(null);
        try {
            $validator = new NoRecordExistsValidator('users', 'field1', null, $this->_adapterNoResult);
            $this->assertTrue($validator->isValid('value1'));
        } catch (\Exception $e) {
            $this->markTestSkipped('No database available');
        }
    }
}
