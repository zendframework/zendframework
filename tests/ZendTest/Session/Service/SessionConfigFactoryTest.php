<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Session\Service;

use Zend\ServiceManager\ServiceManager;
use Zend\Session\Service\SessionConfigFactory;

/**
 * @group      Zend_Session
 */
class SessionConfigFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->services = new ServiceManager();
        $this->services->setFactory('Zend\Session\Config\ConfigInterface', 'Zend\Session\Service\SessionConfigFactory');
    }

    public function testCreatesSessionConfigByDefault()
    {
        $this->services->setService('Config', array(
            'session_config' => array(),
        ));
        $config = $this->services->get('Zend\Session\Config\ConfigInterface');
        $this->assertInstanceOf('Zend\Session\Config\SessionConfig', $config);
    }

    public function testCanCreateAlternateSessionConfigTypeViaConfigClassKey()
    {
        $this->services->setService('Config', array(
            'session_config' => array(
                'config_class' => 'Zend\Session\Config\StandardConfig',
            ),
        ));
        $config = $this->services->get('Zend\Session\Config\ConfigInterface');
        $this->assertInstanceOf('Zend\Session\Config\StandardConfig', $config);
        // Since SessionConfig extends StandardConfig, need to test that it's not that
        $this->assertNotInstanceOf('Zend\Session\Config\SessionConfig', $config);
    }

    public function testServiceReceivesConfiguration()
    {
        $this->services->setService('Config', array(
            'session_config' => array(
                'config_class' => 'Zend\Session\Config\StandardConfig',
                'name'         => 'zf2',
            ),
        ));
        $config = $this->services->get('Zend\Session\Config\ConfigInterface');
        $this->assertEquals('zf2', $config->getName());
    }
}
