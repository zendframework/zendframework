<?php

require_once('Zend/Loader.php');
Zend_Loader::registerAutoload();

switch($_GET['Example']) {

    case 'WithController':

        $profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
        $profiler->setEnabled(true);
        $db = Zend_Db::factory('PDO_SQLITE', array('dbname' => ':memory:'));
        $db->setProfiler($profiler);
        Zend_Registry::set('db',$db);

        $controller = Zend_Controller_Front::getInstance();
        $controller->setParam('useDefaultControllerAlways',true);
        $controller->setParam('noViewRenderer', true);
        $controller->setControllerDirectory(dirname(dirname(dirname(__DIR__))).'/application/controllers/Boot/Zend-Db-Profiler-Firebug');
        $controller->dispatch();

        print 'Test Doc Example with Controller';
        break;

    case 'WithoutController':

        $profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
        $profiler->setEnabled(true);
        $db = Zend_Db::factory('PDO_SQLITE', array('dbname' => ':memory:'));
        $db->setProfiler($profiler);

        $request  = new Zend_Controller_Request_Http();
        $response = new Zend_Controller_Response_Http();
        $channel  = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        $channel->setRequest($request);
        $channel->setResponse($response);

        $db->getConnection()->exec('CREATE TABLE foo (
                                      id      INTEGNER NOT NULL,
                                      col1    VARCHAR(10) NOT NULL
                                    )');

        $db->insert('foo', array('id'=>1,'col1'=>'original'));

        $channel->flush();
        $response->sendHeaders();

        print 'Test Doc Example without Controller';
        break;

}
