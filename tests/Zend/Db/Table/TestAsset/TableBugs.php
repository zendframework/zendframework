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
class TableBugs extends \Zend\Db\Table\AbstractTable
{

    protected $_name = 'zfbugs';
    protected $_primary = 'bug_id'; // Deliberate non-array value

    protected $_dependentTables = array('\ZendTest\Db\Table\TestAsset\TableBugsProducts');

    protected $_referenceMap    = array(
        'Reporter' => array(
            'columns'           => array('reported_by'),
            'refTableClass'     => '\ZendTest\Db\Table\TestAsset\TableAccounts',
            'refColumns'        => array('account_name')
        ),
        'Engineer' => array(
            'columns'           => array('assigned_to'),
            'refTableClass'     => '\ZendTest\Db\Table\TestAsset\TableAccounts',
            'refColumns'        => array('account_name')
        ),
        'Verifier' => array(
            'columns'           => array('verified_by'),
            'refTableClass'     => '\ZendTest\Db\Table\TestAsset\TableAccounts',
            'refColumns'        => array('account_name')
        )
    );

}
