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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @see Zend_Db_Adapter_Pdo_Sqlite
 */
require_once 'Zend/Db/Adapter/Pdo/Sqlite.php';

/**
 * @see Zend_Db_Table_Abstract
 */
require_once 'Zend/Db/Table/Abstract.php';

/**
 * @see Zend_Validate_Db_Abstract.php
 */
require_once 'Zend/Validate/Db/Abstract.php';

/**
 * @see Zend_Validate_Db_RecordExists.php
 */
require_once 'Zend/Validate/Db/RecordExists.php';

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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */
class Zend_Validate_Db_RecordExistsTest extends PHPUnit_Framework_TestCase
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
        $validator = new Zend_Validate_Db_RecordExists('users', 'field1');
        $this->assertTrue($validator->isValid('value1'));
    }

    /**
     * Test basic function of RecordExists (no exclusion)
     *
     * @return void
     */
    public function testBasicFindsNoRecord()
    {
        Zend_Db_Table_Abstract::setDefaultAdapter($this->_adapterNoResult);
        $validator = new Zend_Validate_Db_RecordExists('users', 'field1');
        $this->assertFalse($validator->isValid('nosuchvalue'));
    }

    /**
     * Test the exclusion function
     *
     * @return void
     */
    public function testExcludeWithArray()
    {
        Zend_Db_Table_Abstract::setDefaultAdapter($this->_adapterHasResult);
        $validator = new Zend_Validate_Db_RecordExists('users', 'field1', array('field' => 'id', 'value' => 1));
        $this->assertTrue($validator->isValid('value3'));
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
        $validator = new Zend_Validate_Db_RecordExists('users', 'field1', array('field' => 'id', 'value' => 1));
        $this->assertFalse($validator->isValid('nosuchvalue'));
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
        $validator = new Zend_Validate_Db_RecordExists('users', 'field1', 'id != 1');
        $this->assertTrue($validator->isValid('value3'));
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
        $validator = new Zend_Validate_Db_RecordExists('users', 'field1', 'id != 1');
        $this->assertFalse($validator->isValid('nosuchvalue'));
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
            $validator = new Zend_Validate_Db_RecordExists('users', 'field1', 'id != 1');
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
        $validator = new Zend_Validate_Db_RecordExists(array('table' => 'users',
                                                               'schema' => 'my'),
                                                         'field1');
        $this->assertTrue($validator->isValid('value1'));
    }
    
    /**
     * Test that schemas are supported and run without error
     *
     * @return void
     */
    public function testWithSchemaNoResult()
    {
        Zend_Db_Table_Abstract::setDefaultAdapter($this->_adapterNoResult);
        $validator = new Zend_Validate_Db_RecordExists(array('table' => 'users',
                                                               'schema' => 'my'),
                                                         'field1');
        $this->assertFalse($validator->isValid('value1'));
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
            $validator = new Zend_Validate_Db_RecordExists('users', 'field1', null, $this->_adapterHasResult);
            $this->assertTrue($validator->isValid('value1'));
        } catch (Exception $e) {
            $this->markTestFailed('Threw an exception when adapter was provided');
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
            $validator = new Zend_Validate_Db_RecordExists('users', 'field1', null, $this->_adapterNoResult);
            $this->assertFalse($validator->isValid('value1'));
        } catch (Exception $e) {
            $this->markTestFailed('Threw an exception when adapter was provided');
        }
    }
}
