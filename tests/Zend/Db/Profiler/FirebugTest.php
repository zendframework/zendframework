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
 * Test helper
 */

/** PHPUnit_Framework_TestCase */

/** Zend_Db */

/** Zend_Db_Profiler_Firebug */

/** Zend_Wildfire_Plugin_FirePhp */

/** Zend_Wildfire_Channel_HttpHeaders */

/** Zend_Controller_Request_Http */

/** Zend_Controller_Response_Http */


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Db
 * @group      Zend_Db_Profiler
 */
class Zend_Db_Profiler_FirebugTest extends PHPUnit_Framework_TestCase
{

    protected $_controller = null;
    protected $_request = null;
    protected $_response = null;
    protected $_profiler = null;
    protected $_db = null;

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Db_Profiler_FirebugTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('Requires PDO_Sqlite extension');
        }

        date_default_timezone_set('America/Los_Angeles');

        $this->_request = new Zend_Db_Profiler_FirebugTest_Request();
        $this->_response = new Zend_Db_Profiler_FirebugTest_Response();

        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        $channel->setRequest($this->_request);
        $channel->setResponse($this->_response);

        $this->_profiler = new Zend_Db_Profiler_Firebug();
        $this->_db = Zend_Db::factory('PDO_SQLITE',
                               array('dbname' => ':memory:',
                                     'profiler' => $this->_profiler));
        $this->_db->getConnection()->exec('CREATE TABLE foo (
                                              id      INTEGNER NOT NULL,
                                              col1    VARCHAR(10) NOT NULL
                                            )');
    }

    public function tearDown()
    {
        $this->_db->getConnection()->exec('DROP TABLE foo');

        Zend_Wildfire_Channel_HttpHeaders::destroyInstance();
        Zend_Wildfire_Plugin_FirePhp::destroyInstance();
    }

    public function testEnable()
    {
        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        $protocol = $channel->getProtocol(Zend_Wildfire_Plugin_FirePhp::PROTOCOL_URI);

        $this->_db->insert('foo', array('id'=>1,'col1'=>'original'));

        Zend_Wildfire_Channel_HttpHeaders::getInstance()->flush();

        $this->assertFalse($protocol->getMessages());

        $this->_profiler->setEnabled(true);

        $this->_db->insert('foo', array('id'=>1,'col1'=>'original'));

        Zend_Wildfire_Channel_HttpHeaders::getInstance()->flush();

        $messages = $protocol->getMessages();

        $this->assertEquals(substr($messages[Zend_Wildfire_Plugin_FirePhp::STRUCTURE_URI_FIREBUGCONSOLE]
                                            [Zend_Wildfire_Plugin_FirePhp::PLUGIN_URI][0],0,55),
                            '[{"Type":"TABLE","Label":"Zend_Db_Profiler_Firebug (1 @');
    }

    public function testDisable()
    {
        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        $protocol = $channel->getProtocol(Zend_Wildfire_Plugin_FirePhp::PROTOCOL_URI);

        $this->_profiler->setEnabled(true);

        $this->_db->insert('foo', array('id'=>1,'col1'=>'original'));

        $this->_profiler->setEnabled(false);

        Zend_Wildfire_Channel_HttpHeaders::getInstance()->flush();

        $this->assertFalse($protocol->getMessages());
    }

    public function testCustomLabel()
    {
        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        $protocol = $channel->getProtocol(Zend_Wildfire_Plugin_FirePhp::PROTOCOL_URI);

        $this->_profiler = new Zend_Db_Profiler_Firebug('Label 1');
        $this->_profiler->setEnabled(true);
        $this->_db->setProfiler($this->_profiler);
        $this->_db->insert('foo', array('id'=>1,'col1'=>'original'));

        Zend_Wildfire_Channel_HttpHeaders::getInstance()->flush();

        $messages = $protocol->getMessages();

        $this->assertEquals(substr($messages[Zend_Wildfire_Plugin_FirePhp::STRUCTURE_URI_FIREBUGCONSOLE]
                                            [Zend_Wildfire_Plugin_FirePhp::PLUGIN_URI][0],0,38),
                            '[{"Type":"TABLE","Label":"Label 1 (1 @');
    }

    public function testNoQueries()
    {
        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        $protocol = $channel->getProtocol(Zend_Wildfire_Plugin_FirePhp::PROTOCOL_URI);

        $this->_profiler->setEnabled(true);

        Zend_Wildfire_Channel_HttpHeaders::getInstance()->flush();

        $messages = $protocol->getMessages();

        $this->assertFalse($messages);
    }

    /**
     * @group ZF-6395
     */
    public function testNoQueriesAfterFiltering()
    {
        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        $protocol = $channel->getProtocol(Zend_Wildfire_Plugin_FirePhp::PROTOCOL_URI);

        $profiler = $this->_profiler->setEnabled(true);
        $profiler->setFilterQueryType(Zend_Db_Profiler::INSERT | Zend_Db_Profiler::UPDATE);
        $this->_db->fetchAll('select * from foo');

        Zend_Wildfire_Channel_HttpHeaders::getInstance()->flush();

        $messages = $protocol->getMessages();

        $this->assertFalse($messages);
    }

}


class Zend_Db_Profiler_FirebugTest_Request extends Zend_Controller_Request_Http
{
    public function getHeader($header)
    {
        if ($header == 'User-Agent') {
            return 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X; en-US; rv:1.8.1.14) Gecko/20080404 Firefox/2.0.0.14 FirePHP/0.1.0';
        }
    }
}

class Zend_Db_Profiler_FirebugTest_Response extends Zend_Controller_Response_Http
{
    public function canSendHeaders($throw = false)
    {
        return true;
    }
}
