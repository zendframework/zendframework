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

/* NOTE: You must have Zend Framework in your include path! */

/*
 * Add our Firebug Log Writer to the registry
 */

require_once 'Zend/Registry.php';
require_once 'Zend/Log.php';
require_once 'Zend/Log/Writer/Firebug.php';

$writer = new Zend_Log_Writer_Firebug();
$writer->setPriorityStyle(8, 'TABLE');
$writer->setPriorityStyle(9, 'TRACE');

$logger = new Zend_Log($writer);
$logger->addPriority('TABLE', 8);
$logger->addPriority('TRACE', 9);

Zend_Registry::set('logger',$logger);


/*
 * Add our Firebug DB Profiler to the registry
 */

require_once 'Zend/Db.php';
require_once 'Zend/Db/Profiler/Firebug.php';

$profiler = new Zend_Db_Profiler_Firebug('All DB Queries');

$db = Zend_Db::factory('PDO_SQLITE',
                       array('dbname' => ':memory:',
                             'profiler' => $profiler));

$db->getProfiler()->setEnabled(true);

Zend_Registry::set('db',$db);


/*
 * Run the front controller
 */

require_once 'Zend/Controller/Front.php';

Zend_Controller_Front::run(dirname(__DIR__).'/application/controllers');
