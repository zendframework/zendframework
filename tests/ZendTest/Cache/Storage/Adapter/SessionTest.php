<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cache
 */

namespace ZendTest\Cache\Storage\Adapter;

use Zend\Cache;
use Zend\Session\Container as SessionContainer;
use Zend\Session\Config\StandardConfig as SessionConfig;
use ZendTest\Session\TestAsset\TestManager as TestSessionManager;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @group      Zend_Cache
 */
class SessionTest extends CommonAdapterTest
{

    public function setUp()
    {
        $_SESSION = array();
        SessionContainer::setDefaultManager(null);
        $sessionConfig    = new SessionConfig(array('storage' => 'Zend\Session\Storage\ArrayStorage'));
        $sessionManager   = $manager = new TestSessionManager($sessionConfig);
        $sessionContainer = new SessionContainer('Default', $manager);

        $this->_options = new Cache\Storage\Adapter\SessionOptions(array(
            'session_container' => $sessionContainer
        ));
        $this->_storage = new Cache\Storage\Adapter\Session();
        $this->_storage->setOptions($this->_options);

        parent::setUp();
    }

    public function tearDown()
    {
        $_SESSION = array();
        SessionContainer::setDefaultManager(null);
    }
}
