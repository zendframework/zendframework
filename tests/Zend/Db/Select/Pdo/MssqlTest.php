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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Db/Select/TestCommon.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class Zend_Db_Select_Pdo_MssqlTest extends Zend_Db_Select_TestCommon
{

    public function testSelectFromQualified()
    {
        $this->markTestIncomplete($this->getDriver() . ' needs more syntax for qualified table names.');
    }

    public function testSelectJoinQualified()
    {
        $this->markTestIncomplete($this->getDriver() . ' needs more syntax for qualified table names.');
    }

    public function getDriver()
    {
        return 'Pdo_Mssql';
    }

}
