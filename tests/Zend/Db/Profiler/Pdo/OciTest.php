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
 * @see Zend_Db_Profiler_TestCommon
 */


PHPUnit_Util_Filter::addFileToFilter(__FILE__);


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Db
 * @group      Zend_Db_Profiler
 */
class Zend_Db_Profiler_Pdo_OciTest extends Zend_Db_Profiler_TestCommon
{

    /**
     * Ensures that setFilterQueryType() actually filters
     *
     * @return void
     */
    protected function _testProfilerSetFilterQueryTypeCommon($queryType)
    {
        $bugs = $this->_db->quoteIdentifier('zfbugs', true);
        $bug_id = $this->_db->quoteIdentifier('bug_id', true);
        $bug_status = $this->_db->quoteIdentifier('bug_status', true);

        $prof = $this->_db->getProfiler();
        $prof->setEnabled(true);

        $this->assertSame($prof->setFilterQueryType($queryType), $prof);
        $this->assertEquals($queryType, $prof->getFilterQueryType());

        $this->_db->query("SELECT * FROM $bugs");
        $this->_db->query("INSERT INTO $bugs ($bug_id, $bug_status) VALUES (:id, :status)", array(':id' => 100,':status' => 'NEW'));
        $this->_db->query("DELETE FROM $bugs");
        $this->_db->query("UPDATE $bugs SET $bug_status = :status", array(':status'=>'FIXED'));

        $qps = $prof->getQueryProfiles();
        $this->assertType('array', $qps, 'Expecting some query profiles, got none');
        foreach ($qps as $qp) {
            $qtype = $qp->getQueryType();
            $this->assertEquals($queryType, $qtype,
                "Found query type $qtype, which should have been filtered out");
        }

        $prof->setEnabled(false);
    }

    public function getDriver()
    {
        return 'Pdo_Oci';
    }
}
