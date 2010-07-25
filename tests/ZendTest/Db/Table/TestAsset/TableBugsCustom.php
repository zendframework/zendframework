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
namespace ZendTest\Db\Table\TestAsset;


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class TableBugsCustom extends TableBugs
{
    public $isMetadataFromCache = false;

    protected $_metadataCacheInClass = false;

    protected $_rowClass    = '\ZendTest\Db\Table\TestAsset\Row\TestMyRow';
    protected $_rowsetClass = '\ZendTest\Db\Table\TestAsset\Rowset\TestMyRowset';

    protected $_dependentTables = array('\ZendTest\Db\Table\TestAsset\TableBugsProductsCustom');

    protected $_referenceMap    = array(
        'Reporter' => array(
            'columns'           => array('reported_by'),
            'refTableClass'     => '\ZendTest\Db\Table\TestAsset\TableAccountsCustom',
            'refColumns'        => array('account_name'),
            'onDelete'          => self::CASCADE,
            'onUpdate'          => self::CASCADE
        ),
        'Engineer' => array(
            'columns'           => 'assigned_to',
            'refTableClass'     => '\ZendTest\Db\Table\TestAsset\TableAccountsCustom',
            'refColumns'        => 'account_name'
        ),
        'Verifier' => array(
            'columns'           => 'verified_by',
            'refTableClass'     => '\ZendTest\Db\Table\TestAsset\TableAccountsCustom',
            'refColumns'        => 'account_name'
        )
    );

    /**
     * Public proxy to setup functionality
     *
     * @return void
     */
    public function setup()
    {
        $this->_setup();
    }

    /**
     * Turnkey for initialization of a table object.
     *
     * @return void
     */
    protected function _setup()
    {
        $this->_setupDatabaseAdapter();
        $this->_setupTableName();
        $this->isMetadataFromCache = $this->_setupMetadata();
        $this->_setupPrimaryKey();
    }
}
