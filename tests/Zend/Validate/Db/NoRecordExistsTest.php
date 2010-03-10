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
 * Mock No Result Adapter
 */
require_once dirname(__FILE__) . '/_files/Db/MockNoResult.php';

/**
 * Mock Result Adapter
 */
require_once dirname(__FILE__) . '/_files/Db/MockHasResult.php';

/**
 * @category   Zend
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */
class Zend_Validate_Db_NoRecordExistsTest extends PHPUnit_Framework_TestCase
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
        $this->_adapterHasResult = new Db_MockHasResult();
        $this->_adapterNoResult = new Db_MockNoResult();

    }

    /**
     * Test basic function of RecordExists (no exclusion)
     *
     * @return void
     */
    public function testBasicFindsRecord()
    {
        Zend_Db_Table_Abstract::setDefaultAdapter($this->_adapterHasResult);
        $validator = new Zend_Validate_Db_NoRecordExists('users', 'field1');
        $this->assertFalse($validator->isValid('value1'));
    }

    /**
     * Test basic function of RecordExists (no exclusion)
     *
     * @return void
     */
    public function testBasicFindsNoRecord()
    {
        Zend_Db_Table_Abstract::setDefaultAdapter($this->_adapterNoResult);
        $validator = new Zend_Validate_Db_NoRecordExists('users', 'field1');
        $this->assertTrue($validator->isValid('nosuchvalue'));
    }

    /**
     * Test the exclusion function
     *
     * @return void
     */
    public function testExcludeWithArray()
    {
        Zend_Db_Table_Abstract::setDefaultAdapter($this->_adapterHasResult);
        $validator = new Zend_Validate_Db_NoRecordExists('users', 'field1', array('field' => 'id', 'value' => 1));
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
        Zend_Db_Table_Abstract::setDefaultAdapter($this->_adapterNoResult);
        $validator = new Zend_Validate_Db_NoRecordExists('users', 'field1', array('field' => 'id', 'value' => 1));
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
        Zend_Db_Table_Abstract::setDefaultAdapter($this->_adapterHasResult);
        $validator = new Zend_Validate_Db_NoRecordExists('users', 'field1', 'id != 1');
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
        Zend_Db_Table_Abstract::setDefaultAdapter($this->_adapterNoResult);
        $validator = new Zend_Validate_Db_NoRecordExists('users', 'field1', 'id != 1');
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
        Zend_Db_Table_Abstract::setDefaultAdapter(null);
        try {
            $validator = new Zend_Validate_Db_NoRecordExists('users', 'field1', 'id != 1');
            $valid = $validator->isValid('nosuchvalue');
            $this->markTestFailed('Did not throw exception');
        } catch (Exception $e) {
        }
    }

    /**
     * Test that schemas are supported and run without error
     *
     * @return void
     */
    public function testWithSchema()
    {
        Zend_Db_Table_Abstract::setDefaultAdapter($this->_adapterHasResult);
        $validator = new Zend_Validate_Db_NoRecordExists(array('table' => 'users',
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
        Zend_Db_Table_Abstract::setDefaultAdapter($this->_adapterNoResult);
        $validator = new Zend_Validate_Db_NoRecordExists(array('table' => 'users',
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
        Zend_Db_Table_Abstract::setDefaultAdapter(null);
        try {
            $validator = new Zend_Validate_Db_NoRecordExists('users', 'field1', null, $this->_adapterHasResult);
            $this->assertFalse($validator->isValid('value1'));
        } catch (Exception $e) {
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
        Zend_Db_Table_Abstract::setDefaultAdapter(null);
        try {
            $validator = new Zend_Validate_Db_NoRecordExists('users', 'field1', null, $this->_adapterNoResult);
            $this->assertTrue($validator->isValid('value1'));
        } catch (Exception $e) {
            $this->markTestSkipped('No database available');
        }
    }
}
