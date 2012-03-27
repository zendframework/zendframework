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
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Controller\Action\Helper;
use Zend\Controller\Action\Helper;

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @group      Zend_Controller
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class CacheTest extends \PHPUnit_Framework_TestCase
{

    protected $_requestUriOld;


    public function setUp()
    {
        $this->_requestUriOld =
            isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
        $_SERVER['REQUEST_URI'] = '/foo';
        $this->front = \Zend\Controller\Front::getInstance();
        $this->front->resetInstance();
        $this->request = new \Zend\Controller\Request\Http();
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
        $helper = new Helper\Cache;
        $this->assertTrue($helper->getManager() instanceof \Zend\Cache\Manager);
    }

    public function testMethodsProxyToManager()
    {
        $helper = new Helper\Cache;
        $this->assertTrue($helper->hasCache('page'));
    }

    public function testCacheableActionsStoredAtInit()
    {
        $helper = new Helper\Cache;
        $helper->direct(array('action1'));
        $cacheable = $helper->getCacheableActions();
        $this->assertEquals('action1', $cacheable['bar'][0]);
    }

    public function testCacheableActionTagsStoredAtInit()
    {
        $helper = new Helper\Cache;
        $helper->direct(array('action1'), array('tag1','tag2'));
        $cacheable = $helper->getCacheableTags();
        $this->assertSame(array('tag1','tag2'), $cacheable['bar']['action1']);
    }

    public function testCacheableActionsNeverDuplicated()
    {
        $helper = new Helper\Cache;
        $helper->direct(array('action1','action1'));
        $cacheable = $helper->getCacheableActions();
        $this->assertEquals('action1', $cacheable['bar'][0]);
    }

    public function testCacheableActionTagsNeverDuplicated()
    {
        $helper = new Helper\Cache;
        $helper->direct(array('action1'), array('tag1','tag1','tag2','tag2'));
        $cacheable = $helper->getCacheableTags();
        $this->assertSame(array('tag1','tag2'), $cacheable['bar']['action1']);
    }

    public function testRemovePageCallsPageCacheRemoveMethodCorrectly()
    {
        $helper = new Helper\Cache;
        $cache = new Mock1;
        $helper->setCache('page', $cache);
        $this->assertEquals('verified', $helper->removePage('/foo'));
    }

    public function testRemovePageCallsPageCacheRemoveRecursiveMethodCorrectly()
    {
        $helper = new Helper\Cache;
        $cache = new Mock1;
        $backend = new Mock2;
        $cache->setBackend($backend);
        $helper->setCache('page', $cache);
        $this->assertEquals('verified', $helper->removePage('/foo', true));
    }

    public function testRemovePagesTaggedCallsPageCacheCleanMethodCorrectly()
    {
        $helper = new Helper\Cache;
        $cache = new Mock3;
        $helper->setCache('page', $cache);
        $this->assertEquals('verified', $helper->removePagesTagged(array('tag1')));
    }

    public function testPreDispatchCallsCachesStartMethod()
    {
        $helper = new Helper\Cache;
        $cache = new Mock4;
        $helper->setCache('page', $cache);
        $helper->direct(array('baz'));
        $helper->preDispatch();
        $this->assertEquals('verified', $cache->ranStart);
    }

    public function testPreDispatchCallsCachesStartMethodWithTags()
    {
        $helper = new Helper\Cache;
        $cache = new Mock6;
        $helper->setCache('page', $cache);
        $helper->direct(array('baz'), array('tag1','tag2'));
        $helper->preDispatch();
        $this->assertEquals('verified', $cache->ranStart);
    }

    public function testPreDispatchDoesNotCallCachesStartMethodWithBadAction()
    {
        $helper = new Helper\Cache;
        $cache = new Mock4;
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

class Mock1 extends \Zend\Cache\Frontend\Core
{
    public function remove($id)
    {
        if ($id == '/foo') {return 'verified';}
    }
}
class Mock2 implements \Zend\Cache\Backend
{
    public function removeRecursively($id)
    {
        if ($id == '/foo') {return 'verified';}
    }
    
    public function setDirectives($directives) {}
    public function load($id, $doNotTestCacheValidity = false) {}
    public function test($id) {}
    public function save($data, $id, $tags = array(), $specificLifetime = false) {}
    public function remove($id) {}
    public function clean($mode = Cache::CLEANING_MODE_ALL, $tags = array()) {}
    
}
class Mock3 extends \Zend\Cache\Frontend\Core
{
    public function clean($mode = 'all', $tags = array())
    {
        if ($mode == 'matchingAnyTag' && $tags == array('tag1'))
        {return 'verified';}
    }
}
class Mock4 extends \Zend\Cache\Frontend\Core
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
class Mock6 extends \Zend\Cache\Frontend\Core
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
