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
 * @see My_ZendDbTable_TableAccounts
 */
require_once 'TableAccounts.php';


/**
 * require other test files needed, this will
 * ensure that Zend_Loader::loadClass is not called
 */
require_once 'TableBugsCustom.php';



PHPUnit_Util_Filter::addFileToFilter(__FILE__);


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class My_ZendDbTable_TableAccountsCustom extends My_ZendDbTable_TableAccounts
{
    protected $_rowClass    = 'My_ZendDbTable_Row_TestMyRow';
    protected $_rowsetClass = 'My_ZendDbTable_Rowset_TestMyRowset';

    protected $_dependentTables = array('My_ZendDbTable_TableBugsCustom');
}
