<?php

if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Controller_Action_Helper_CacheTest::main");
}

require_once dirname(__FILE__) . '/../../../../TestHelper.php';
require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Controller/Action/Helper/Cache.php';
require_once 'Zend/Controller/Action/HelperBroker.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Request/Http.php';
require_once 'Zend/Controller/Response/Http.php';
require_once 'Zend/Cache.php';
require_once 'Zend/Cache/Core.php';
require_once 'Zend/Cache/Backend.php';

/**
 * Test class for Zend_Controller_Action_Helper_Cache
 */
class Zend_Controller_Action_Helper_CacheTest extends PHPUnit_Framework_TestCase
{

    protected $_requestUriOld;

    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Controller_Action_Helper_CacheTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        $this->_requestUriOld =
            isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
        $_SERVER['REQUEST_URI'] = '/foo';
        $this->front = Zend_Controller_Front::getInstance();
        $this->front->resetInstance();
        $this->request = new Zend_Controller_Request_Http();
        $this->request->setModuleName('foo')
                ->setControllerName('bar')
                ->setActionName('baz');
        $this->front->setRequest($this->request);
    }

    public function tearDown()
    {
        $_SERVER['REQUEST_URI'] = $this->_requestUriOld;
    }

    public function testGetterInstantiatesManager()
    {
        $helper = new Zend_Controller_Action_Helper_Cache;
        $this->assertTrue($helper->getManager() instanceof Zend_Cache_Manager);
    }

    public function testMethodsProxyToManager()
    {
        $helper = new Zend_Controller_Action_Helper_Cache;
        $this->assertTrue($helper->hasCache('page'));
    }

    public function testCacheableActionsStoredAtInit()
    {
        $helper = new Zend_Controller_Action_Helper_Cache;
        $helper->direct(array('action1'));
        $cacheable = $helper->getCacheableActions();
        $this->assertEquals('action1', $cacheable['bar'][0]);
    }

    public function testCacheableActionTagsStoredAtInit()
    {
        $helper = new Zend_Controller_Action_Helper_Cache;
        $helper->direct(array('action1'), array('tag1','tag2'));
        $cacheable = $helper->getCacheableTags();
        $this->assertSame(array('tag1','tag2'), $cacheable['bar']['action1']);
    }

    public function testCacheableActionsNeverDuplicated()
    {
        $helper = new Zend_Controller_Action_Helper_Cache;
        $helper->direct(array('action1','action1'));
        $cacheable = $helper->getCacheableActions();
        $this->assertEquals('action1', $cacheable['bar'][0]);
    }

    public function testCacheableActionTagsNeverDuplicated()
    {
        $helper = new Zend_Controller_Action_Helper_Cache;
        $helper->direct(array('action1'), array('tag1','tag1','tag2','tag2'));
        $cacheable = $helper->getCacheableTags();
        $this->assertSame(array('tag1','tag2'), $cacheable['bar']['action1']);
    }

    public function testRemovePageCallsPageCacheRemoveMethodCorrectly()
    {
        $helper = new Zend_Controller_Action_Helper_Cache;
        $cache = new Mock_Zend_Cache_Page_1;
        $helper->setCache('page', $cache);
        $this->assertEquals('verified', $helper->removePage('/foo'));
    }

    public function testRemovePageCallsPageCacheRemoveRecursiveMethodCorrectly()
    {
        $helper = new Zend_Controller_Action_Helper_Cache;
        $cache = new Mock_Zend_Cache_Page_1;
        $backend = new Mock_Zend_Cache_Page_2;
        $cache->setBackend($backend);
        $helper->setCache('page', $cache);
        $this->assertEquals('verified', $helper->removePage('/foo', true));
    }

    public function testRemovePagesTaggedCallsPageCacheCleanMethodCorrectly()
    {
        $helper = new Zend_Controller_Action_Helper_Cache;
        $cache = new Mock_Zend_Cache_Page_3;
        $helper->setCache('page', $cache);
        $this->assertEquals('verified', $helper->removePagesTagged(array('tag1')));
    }

    public function testPreDispatchCallsCachesStartMethod()
    {
        $helper = new Zend_Controller_Action_Helper_Cache;
        $cache = new Mock_Zend_Cache_Page_4;
        $helper->setCache('page', $cache);
        $helper->direct(array('baz'));
        $helper->preDispatch();
        $this->assertEquals('verified', $cache->ranStart);
    }

    public function testPreDispatchCallsCachesStartMethodWithTags()
    {
        $helper = new Zend_Controller_Action_Helper_Cache;
        $cache = new Mock_Zend_Cache_Page_6;
        $helper->setCache('page', $cache);
        $helper->direct(array('baz'), array('tag1','tag2'));
        $helper->preDispatch();
        $this->assertEquals('verified', $cache->ranStart);
    }

    public function testPreDispatchDoesNotCallCachesStartMethodWithBadAction()
    {
        $helper = new Zend_Controller_Action_Helper_Cache;
        $cache = new Mock_Zend_Cache_Page_4;
        $helper->setCache('page', $cache);
        $helper->preDispatch();
        $this->assertNotEquals('verified', $cache->res);
    }

    /**public function testPostDispatchEndsOutputBufferPageCaching()
    {
        $helper = new Zend_Controller_Action_Helper_Cache;
        $cache = new Mock_Zend_Cache_Page_5;
        $helper->setCache('page', $cache);
        $helper->direct(array('baz'));
        $helper->preDispatch();
        $helper->postDispatch();
        $this->assertEquals('verified', $cache->res);
    }

    public function testPostDispatchNotEndsOutputBufferPageCachingWithBadAction()
    {
        $helper = new Zend_Controller_Action_Helper_Cache;
        $cache = new Mock_Zend_Cache_Page_5;
        $helper->setCache('page', $cache);
        $helper->direct(array('action1'));
        $helper->preDispatch();
        $helper->postDispatch();
        $this->assertNotEquals('verified', $cache->res);
    }**/

}

class Mock_Zend_Cache_Page_1 extends Zend_Cache_Core
{
    public function remove($id)
    {
        if ($id == '/foo') {return 'verified';}
    }
}
class Mock_Zend_Cache_Page_2 extends Zend_Cache_Backend
{
    public function removeRecursively($id)
    {
        if ($id == '/foo') {return 'verified';}
    }
}
class Mock_Zend_Cache_Page_3 extends Zend_Cache_Core
{
    public function clean($mode = 'all', $tags = array())
    {
        if ($mode == 'matchingAnyTag' && $tags == array('tag1'))
        {return 'verified';}
    }
}
class Mock_Zend_Cache_Page_4 extends Zend_Cache_Core
{
    public $res;
    public $ranStart;
    public function start($id, array $tags = array()) 
    {
        $this->ranStart = 'verified';
        if ($id == '/foo') {
            $this->res = 'verified';
        }
    }
}
class Mock_Zend_Cache_Page_6 extends Zend_Cache_Core
{
    public $res;
    public $ranStart;
    public function start($id, array $tags = array()) 
    {
        $this->ranStart = 'verified';
        if ($id == '/foo' && $tags == array('tag1','tag2')) {
            $this->res = 'verified';
        }
    }
}

/**class Mock_Zend_Cache_Page_5 extends Zend_Cache_Core
{
    public $res;
    public function start() {}
    public function end() {$this->res = 'verified';}
}**/

if (PHPUnit_MAIN_METHOD == "Zend_Controller_Action_Helper_CacheTest::main") {
    Zend_Controller_Action_Helper_CacheTest::main();
}
