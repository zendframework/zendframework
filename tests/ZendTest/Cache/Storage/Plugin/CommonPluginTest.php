<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cache
 */

namespace ZendTest\Cache\Storage\Plugin;

/**
 * PHPUnit test case
 */

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @group      Zend_Cache
 */
abstract class CommonPluginTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The storage plugin
     *
     * @var \Zend\Cache\Storage\Plugin\PluginInterface
     */
    protected $_plugin;

    public function testOptionObjectAvailable()
    {
        $options = $this->_plugin->getOptions();
        $this->assertInstanceOf('Zend\Cache\Storage\Plugin\PluginOptions', $options);
    }

    public function testOptionsGetAndSetDefault()
    {
        $options = $this->_plugin->getOptions();
        $this->_plugin->setOptions($options);
        $this->assertSame($options, $this->_plugin->getOptions());
    }
}
