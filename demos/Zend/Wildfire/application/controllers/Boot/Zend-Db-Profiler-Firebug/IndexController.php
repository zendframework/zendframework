<?php

require_once 'Zend/Controller/Action.php';

class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $db = Zend_Registry::get('db');

        $db->getConnection()->exec('CREATE TABLE foo (
                                      id      INTEGNER NOT NULL,
                                      col1    VARCHAR(10) NOT NULL
                                    )');

        $db->insert('foo', array('id'=>1,'col1'=>'original'));
    }
}
