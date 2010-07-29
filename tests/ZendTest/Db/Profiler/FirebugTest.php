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
namespace ZendTest\Db\Profiler;
use Zend\Wildfire\Channel;
use Zend\Db\Profiler;
use Zend\Wildfire\Plugin\FirePhp;


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Db
 * @group      Zend_Db_Profiler
 */
class FirebugTest extends \PHPUnit_Framework_TestCase
{

    protected $_controller = null;
    protected $_request = null;
    protected $_response = null;
    protected $_profiler = null;
    protected $_db = null;

    public function setUp()
    {
        $this->markTestSkipped('This suite is skipped until Zend\Db can be refactored.');
        
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('Requires PDO Sqlite extension');
        }

        date_default_timezone_set('America/Los_Angeles');

        $this->_request  = new \Zend_Db_Profiler_FirebugTest_Request();
        $this->_response = new \Zend_Db_Profiler_FirebugTest_Response();

        $channel = Channel\HttpHeaders::getInstance();
        $channel->setRequest($this->_request);
        $channel->setResponse($this->_response);

        $this->_profiler = new Profiler\Firebug();
        $this->_db = \Zend\Db\DB::factory('Pdo\Sqlite',
                               array('dbname' => ':memory:',
                                     'profiler' => $this->_profiler));
        $this->_db->getConnection()->exec('CREATE TABLE foo (
                                              id      INTEGNER NOT NULL,
                                              col1    VARCHAR(10) NOT NULL
                                            )');
    }

    public function tearDown()
    {
        if (extension_loaded('pdo_sqlite')) {
            $this->_db->getConnection()->exec('DROP TABLE foo');
        }

        Channel\HttpHeaders::destroyInstance();
        FirePhp\FirePhp::destroyInstance();
    }

    public function testEnable()
    {
        $channel = Channel\HttpHeaders::getInstance();
        $protocol = $channel->getProtocol(FirePhp\FirePhp::PROTOCOL_URI);

        $this->_db->insert('foo', array('id'=>1,'col1'=>'original'));

        Channel\HttpHeaders::getInstance()->flush();

        $this->assertFalse($protocol->getMessages());

        $this->_profiler->setEnabled(true);

        $this->_db->insert('foo', array('id'=>1,'col1'=>'original'));

        Channel\HttpHeaders::getInstance()->flush();

        $messages = $protocol->getMessages();

        $this->assertEquals(substr($messages[FirePhp\FirePhp::STRUCTURE_URI_FIREBUGCONSOLE]
                                            [FirePhp\FirePhp::PLUGIN_URI][0],0,55),
                            '[{"Type":"TABLE","Label":"Zend_Db_Profiler_Firebug (1 @');
    }

    public function testDisable()
    {
        $channel = Channel\HttpHeaders::getInstance();
        $protocol = $channel->getProtocol(FirePhp\FirePhp::PROTOCOL_URI);

        $this->_profiler->setEnabled(true);

        $this->_db->insert('foo', array('id'=>1,'col1'=>'original'));

        $this->_profiler->setEnabled(false);

        Channel\HttpHeaders::getInstance()->flush();

        $this->assertFalse($protocol->getMessages());
    }

    public function testCustomLabel()
    {
        $channel = Channel\HttpHeaders::getInstance();
        $protocol = $channel->getProtocol(FirePhp\FirePhp::PROTOCOL_URI);

        $this->_profiler = new Profiler\Firebug('Label 1');
        $this->_profiler->setEnabled(true);
        $this->_db->setProfiler($this->_profiler);
        $this->_db->insert('foo', array('id'=>1,'col1'=>'original'));

        Channel\HttpHeaders::getInstance()->flush();

        $messages = $protocol->getMessages();

        $this->assertEquals(substr($messages[FirePhp\FirePhp::STRUCTURE_URI_FIREBUGCONSOLE]
                                            [FirePhp\FirePhp::PLUGIN_URI][0],0,38),
                            '[{"Type":"TABLE","Label":"Label 1 (1 @');
    }

    public function testNoQueries()
    {
        $channel = Channel\HttpHeaders::getInstance();
        $protocol = $channel->getProtocol(FirePhp\FirePhp::PROTOCOL_URI);

        $this->_profiler->setEnabled(true);

        Channel\HttpHeaders::getInstance()->flush();

        $messages = $protocol->getMessages();

        $this->assertFalse($messages);
    }

    /**
     * @group ZF-6395
     */
    public function testNoQueriesAfterFiltering()
    {
        $channel = Channel\HttpHeaders::getInstance();
        $protocol = $channel->getProtocol(FirePhp\FirePhp::PROTOCOL_URI);

        $profiler = $this->_profiler->setEnabled(true);
        $profiler->setFilterQueryType(Profiler::INSERT | Profiler::UPDATE);
        $this->_db->fetchAll('select * from foo');

        Channel\HttpHeaders::getInstance()->flush();

        $messages = $protocol->getMessages();

        $this->assertFalse($messages);
    }

}

//
//class Request extends \Zend\Controller\Request\Http
//{
//    public function getHeader($header)
//    {
//        if ($header == 'User-Agent') {
//            return 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X; en-US; rv:1.8.1.14) Gecko/20080404 Firefox/2.0.0.14 FirePHP/0.1.0';
//        }
//    }
//}
//
//class Response extends \Zend\Controller\Response\Http
//{
//    public function canSendHeaders($throw = false)
//    {
//        return true;
//    }
//}
