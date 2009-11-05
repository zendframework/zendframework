<?php

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * Zend_Config
 */
require_once 'Zend/Config.php';

/**
 * Zend_Config_Ini
 */
require_once 'Zend/Config/Ini.php';

/**
 * Zend_Config_Writer_Ini
 */
require_once 'Zend/Config/Writer/Ini.php';

require_once "Zend/Config/Writer/SimpleIni.php";

class Zend_Config_Writer_SimpleIniTest extends PHPUnit_Framework_TestCase
{
    public function testRender()
    {
        $config = new Zend_Config(array('test' => 'foo', 'test2' => array('test3' => 'bar')));

        $writer = new Zend_Config_Writer_SimpleIni();
        $iniString = $writer->setConfig($config)->render();

        $expected = <<<ECS
test = "foo"
test2.test3 = "bar"

ECS;
        $this->assertEquals($expected, $iniString);
    }

    public function testRender2()
    {
        $config = new Zend_Config_Ini(dirname(__FILE__) . '/files/allsections.ini', null, array('skipExtends' => true));

        $writer = new Zend_Config_Writer_SimpleIni();
        $iniString = $writer->setConfig($config)->render();

        $expected = <<<ECS
all.hostname = "all"
all.name = "thisname"
all.db.host = "127.0.0.1"
all.db.user = "username"
all.db.pass = "password"
all.db.name = "live"
all.one.two.three = "multi"
staging.hostname = "staging"
staging.db.name = "dbstaging"
staging.debug = ""
debug.hostname = "debug"
debug.debug = "1"
debug.values.changed = "1"
debug.db.name = "dbdebug"
debug.special.no = ""
debug.special.null = ""
debug.special.false = ""
other_staging.only_in = "otherStaging"
other_staging.db.pass = "anotherpwd"

ECS;
        $this->assertEquals($expected, $iniString);
    }
}