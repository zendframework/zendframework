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
 * @package    Zend_Wildfire
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';

/**
 * Tests for Zend_Db_Profiler_Firebug
 *
 * @category   Zend
 * @package    Zend_Wildfire
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendDbProfilerFirebugController extends Zend_Controller_Action
{

    public function testloggingAction()
    {
        $db = Zend_Registry::get('db');

        $db->getConnection()->exec('CREATE TABLE foo (
                                      id      INTEGNER NOT NULL,
                                      col1    VARCHAR(10) NOT NULL
                                    )');

        $db->insert('foo', array('id'=>1,'col1'=>'original'));

        $db->fetchAll('SELECT * FROM foo WHERE id = ?', 1);

        $db->update('foo', array('col1'=>'new'), 'id = 1');

        $db->fetchAll('SELECT * FROM foo WHERE id = ?', 1);

        $db->delete('foo', 'id = 1');

        $db->getConnection()->exec('DROP TABLE foo');
    }

    public function testmultipledatabasesAction()
    {
        $profiler1 = new Zend_Db_Profiler_Firebug('All DB Queries for first database');

        $db1 = Zend_Db::factory('PDO_SQLITE',
                               array('dbname' => ':memory:',
                                     'profiler' => $profiler1));

        $db1->getProfiler()->setEnabled(true);

        $profiler2 = new Zend_Db_Profiler_Firebug('All DB Queries for second database');

        $db2 = Zend_Db::factory('PDO_SQLITE',
                               array('dbname' => ':memory:',
                                     'profiler' => $profiler2));

        $db2->getProfiler()->setEnabled(true);

        $db1->getConnection()->exec('CREATE TABLE foo (
                                      id      INTEGNER NOT NULL,
                                      col1    VARCHAR(10) NOT NULL
                                    )');

        $db1->insert('foo', array('id'=>1,'col1'=>'original'));

        $db2->getConnection()->exec('CREATE TABLE foo (
                                      id      INTEGNER NOT NULL,
                                      col1    VARCHAR(10) NOT NULL
                                    )');

        $db2->insert('foo', array('id'=>1,'col1'=>'original'));
    }

}

