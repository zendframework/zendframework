<?php
/**
 * @package    Zend_Cache
 * @subpackage UnitTests
 */

namespace ZendTest\Cache;
use Zend\Cache;

/**
 * @package    Zend_Cache
 * @subpackage UnitTests
 */
class StaticBackendTest extends TestCommonBackend 
{

    protected $_instance;
    protected $_instance2;
    protected $_cache_dir;
    protected $_requestUriOld;
    protected $_innerCache;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct('\Zend\Cache\Backend\StaticBackend', $data, $dataName);
    }

    public function setUp($notag = false)
    {
        $this->mkdir();
        $this->_cache_dir = $this->mkdir();
        @mkdir($this->_cache_dir.'/tags');

        $this->_innerCache = Cache\Cache::factory('Core','File',
            array('automatic_serialization'=>true), array('cache_dir'=>$this->_cache_dir.'/tags')
        );
        $this->_instance = new Cache\Backend\StaticBackend(array(
            'public_dir' => $this->_cache_dir,
            'tag_cache' => $this->_innerCache
        ));

        $logger = new \Zend\Log\Logger(new \Zend\Log\Writer\Null());
        $this->_instance->setDirectives(array('logger' => $logger));

        $this->_requestUriOld =
            isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
        $_SERVER['REQUEST_URI'] = '/foo';

        $this->_instance->setDirectives(array('logging' => true));

        $this->_instance->save('bar : data to cache', bin2hex('/bar'), array('tag3', 'tag4'));
        $this->_instance->save('bar2 : data to cache', bin2hex('/bar2'), array('tag3', 'tag1'));
        $this->_instance->save('bar3 : data to cache', bin2hex('/bar3'), array('tag2', 'tag3'));
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->_instance);
        $_SERVER['REQUEST_URI'] = $this->_requestUriOld;
        $this->rmdir();
    }

    public function testConstructorCorrectCall()
    {
        $test = new Cache\Backend\StaticBackend(array());
    }

    public function testRemoveCorrectCall()
    {
        $this->assertTrue($this->_instance->remove('/bar'));
        $this->assertFalse($this->_instance->test(bin2hex('/bar')));
        $this->assertFalse($this->_instance->remove('/barbar'));
        $this->assertFalse($this->_instance->test(bin2hex('/barbar')));
    }

    public function testOptionsSetTagCache()
    {
        $test = new Cache\Backend\StaticBackend(array('tag_cache'=>$this->_innerCache));
        $this->assertTrue($test->getInnerCache() instanceof Cache\Frontend);
    }

    public function testSaveCorrectCall()
    {
        $res = $this->_instance->save('data to cache', bin2hex('/foo'), array('tag1', 'tag2'));
        $this->assertTrue($res);
    }

    public function testSaveWithNullLifeTime()
    {
        $this->_instance->setDirectives(array('lifetime' => null));
        $res = $this->_instance->save('data to cache', bin2hex('/foo'), array('tag1', 'tag2'));
        $this->assertTrue($res);
    }

    public function testSaveWithSpecificLifeTime()
    {
        $this->_instance->setDirectives(array('lifetime' => 3600));
        $res = $this->_instance->save('data to cache', bin2hex('/foo'), array('tag1', 'tag2'), 10);
        $this->assertTrue($res);
    }
    
    public function testSaveWithSpecificExtension()
    {
        $res = $this->_instance->save(serialize(array('data to cache', 'xml')), bin2hex('/foo2'));
        $this->assertTrue($this->_instance->test(bin2hex('/foo2')));
        unlink($this->_instance->getOption('public_dir') . '/foo2.xml');
    }

    public function testSaveWithSpecificExtensionWithTag()
    {
        $res = $this->_instance->save(serialize(array('data to cache', 'xml')), bin2hex('/foo'), array('tag1'));
        $this->assertTrue($this->_instance->test(bin2hex('/foo')));
        unlink($this->_instance->getOption('public_dir') . '/foo.xml');
    }
    
    public function testRemovalWithSpecificExtension()
    {
        $res = $this->_instance->save(serialize(array('data to cache', 'xml')), bin2hex('/foo3'), array('tag1'));
        $this->assertTrue($this->_instance->test(bin2hex('/foo3')));
        $this->assertTrue($this->_instance->remove('/foo3'));
        $this->assertFalse($this->_instance->test(bin2hex('/foo3')));
    }

    public function testTestWithAnExistingCacheId()
    {
        $res = $this->_instance->test(bin2hex('/bar'));
        if (!$res) {
            $this->fail('test() return false');
        }
        return;
    }

    public function testTestWithANonExistingCacheId()
    {
        $this->assertFalse($this->_instance->test(bin2hex('/barbar')));
    }

    public function testTestWithAnExistingCacheIdAndANullLifeTime()
    {
        $this->_instance->setDirectives(array('lifetime' => null));
        $res = $this->_instance->test(bin2hex('/bar'));
        if (!$res) {
            $this->fail('test() return false');
        }
        return;
    }

    public function testGetWithANonExistingCacheId()
    {
        $this->assertFalse($this->_instance->load(bin2hex('/barbar')));
    }

    public function testGetWithAnExistingCacheId()
    {
        $this->assertEquals('bar : data to cache', $this->_instance->load(bin2hex('/bar')));
    }

    public function testGetWithAnExistingCacheIdAndUTFCharacters()
    {
        $data = '"""""' . "'" . '\n' . 'ééééé';
        $this->_instance->save($data, bin2hex('/foo'));
        $this->assertEquals($data, $this->_instance->load(bin2hex('/foo')));
    }

    public function testCleanModeMatchingTags()
    {
        $this->assertTrue($this->_instance->clean('matchingTag', array('tag3')));
        $this->assertFalse($this->_instance->test(bin2hex('/bar')));
        $this->assertFalse($this->_instance->test(bin2hex('/bar2')));
    }

    public function testCleanModeMatchingTags2()
    {
        $this->assertTrue($this->_instance->clean('matchingTag', array('tag3', 'tag4')));
        $this->assertFalse($this->_instance->test(bin2hex('/bar')));
    }

    public function testCleanModeNotMatchingTags()
    {
        $this->assertTrue($this->_instance->clean('notMatchingTag', array('tag3')));
        $this->assertTrue($this->_instance->test(bin2hex('/bar')));
        $this->assertTrue($this->_instance->test(bin2hex('/bar2')));
    }

    public function testCleanModeNotMatchingTags2()
    {
        $this->assertTrue($this->_instance->clean('notMatchingTag', array('tag4')));
        $this->assertTrue($this->_instance->test(bin2hex('/bar')));
        $this->assertFalse($this->_instance->test(bin2hex('/bar2')));
    }

    public function testCleanModeNotMatchingTags3()
    {
        $this->assertTrue($this->_instance->clean('notMatchingTag', array('tag4', 'tag1')));
        $this->assertTrue($this->_instance->test(bin2hex('/bar')));
        $this->assertTrue($this->_instance->test(bin2hex('/bar2')));
        $this->assertFalse($this->_instance->test(bin2hex('/bar3')));
    }

    public function testCleanModeAll()
    {
        $this->assertTrue($this->_instance->clean('all'));
        $this->assertFalse($this->_instance->test(bin2hex('bar')));
        $this->assertFalse($this->_instance->test(bin2hex('bar2')));
    }


    // Irrelevant Tests (from common tests)

    public function testGetWithAnExpiredCacheId()
    {
        $this->markTestSkipped('Irrelevant Test');
    }

    public function testCleanModeOld()
    {
        $this->markTestSkipped('Irrelevant Test');
    }

    // Helper Methods

    public function mkdir()
    {
        $tmp = $this->getTmpDir();
        @mkdir($tmp);
        return $tmp;
    }

    public function rmdir()
    {
        $tmpDir = $this->getTmpDir(false);
        foreach (glob("$tmpDir*") as $dirname) {
            @rmdir($dirname);
        }
    }

    public function getTmpDir($date = true)
    {
        $suffix = '';
        $tmp = sys_get_temp_dir();
        if ($date) {
            $suffix = date('mdyHis');
        }
        if (is_writeable($tmp)) {
            return $tmp . DIRECTORY_SEPARATOR . 'zend_cache_tmp_dir_' . $suffix;
        } else {
            throw new Exception("no writable tmpdir found");
        }
    }

}
