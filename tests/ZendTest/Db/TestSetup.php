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
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\DB;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Db
 */
abstract class TestSetup extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ZendTest\Db\TestUtil
     */
    protected $_util = null;

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db = null;

    public abstract function getDriver();

    /**
     * Subclasses should call parent::setUp() before
     * doing their own logic, e.g. creating metadata.
     */
    public function setUp()
    {
        $this->_setUpTestUtil();
        
        if (!$this->_util->isEnabled()) {
            $this->markTestSkipped('Driver ' . $this->getDriver() . ' is not enabled in TestConfiguration.php');
            return;
        }
        
        $this->_setUpAdapter();
        $this->_util->setUp($this->_db);
    }

    /**
     * Get a TestUtil class for the current RDBMS brand.
     */
    protected function _setUpTestUtil()
    {
        $driver = $this->getDriver();
        $utilClass = 'ZendTest\Db\TestUtil\\' . $driver;
        $this->_util = new $utilClass();
    }

    /**
     * Open a new database connection
     */
    protected function _setUpAdapter()
    {
        $this->_db = \Zend\Db\DB::factory($this->getDriver(), $this->_util->getParams());
        try {
            $conn = $this->_db->getConnection();
        } catch (\Zend\Exception $e) {
            $this->_db = null;
            $this->assertType('Zend\Db\Adapter\Exception', $e,
                'Expecting Zend_Db_Adapter_Exception, got ' . get_class($e));
            $this->markTestSkipped($e->getMessage());
        }
    }

    /**
     * Subclasses should call parent::tearDown() after
     * doing their own logic, e.g. deleting metadata.
     */
    public function tearDown()
    {
        if (isset($this->_util) && $this->_util->isEnabled()) {
            $this->_util->tearDown();
        }

        if ($this->_db) {
            $this->_db->closeConnection();
            $this->_db = null;            
        }

    }

}
