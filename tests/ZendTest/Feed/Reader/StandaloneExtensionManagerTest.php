<?php
namespace ZendTest\Feed\Reader;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Feed\Reader\StandaloneExtensionManager;

class StandaloneExtensionManagerTest extends TestCase
{
    public function setUp()
    {
        $this->extensions = new StandaloneExtensionManager();
    }

    public function testIsAnExtensionManagerImplementation()
    {
        $this->assertInstanceOf('Zend\Feed\Reader\ExtensionManagerInterface', $this->extensions);
    }

    public function defaultPlugins()
    {
        return array(
            'Atom\Entry'            => array('Atom\Entry', 'Zend\Feed\Reader\Extension\Atom\Entry'),
            'Atom\Feed'             => array('Atom\Feed', 'Zend\Feed\Reader\Extension\Atom\Feed'),
            'Content\Entry'         => array('Content\Entry', 'Zend\Feed\Reader\Extension\Content\Entry'),
            'CreativeCommons\Entry' => array(
                'CreativeCommons\Entry',
                'Zend\Feed\Reader\Extension\CreativeCommons\Entry'
            ),
            'CreativeCommons\Feed'  => array('CreativeCommons\Feed', 'Zend\Feed\Reader\Extension\CreativeCommons\Feed'),
            'DublinCore\Entry'      => array('DublinCore\Entry', 'Zend\Feed\Reader\Extension\DublinCore\Entry'),
            'DublinCore\Feed'       => array('DublinCore\Feed', 'Zend\Feed\Reader\Extension\DublinCore\Feed'),
            'Podcast\Entry'         => array('Podcast\Entry', 'Zend\Feed\Reader\Extension\Podcast\Entry'),
            'Podcast\Feed'          => array('Podcast\Feed', 'Zend\Feed\Reader\Extension\Podcast\Feed'),
            'Slash\Entry'           => array('Slash\Entry', 'Zend\Feed\Reader\Extension\Slash\Entry'),
            'Syndication\Feed'      => array('Syndication\Feed', 'Zend\Feed\Reader\Extension\Syndication\Feed'),
            'Thread\Entry'          => array('Thread\Entry', 'Zend\Feed\Reader\Extension\Thread\Entry'),
            'WellFormedWeb\Entry'   => array('WellFormedWeb\Entry', 'Zend\Feed\Reader\Extension\WellFormedWeb\Entry'),
        );
    }

    /**
     * @dataProvider defaultPlugins
     */
    public function testHasAllDefaultPlugins($pluginName, $pluginClass)
    {
        $this->assertTrue($this->extensions->has($pluginName));
    }

    /**
     * @dataProvider defaultPlugins
     */
    public function testCanRetrieveDefaultPluginInstances($pluginName, $pluginClass)
    {
        $extension = $this->extensions->get($pluginName);
        $this->assertInstanceOf($pluginClass, $extension);
    }

    /**
     * @dataProvider defaultPlugins
     */
    public function testEachPluginRetrievalReturnsNewInstance($pluginName, $pluginClass)
    {
        $extension = $this->extensions->get($pluginName);
        $this->assertInstanceOf($pluginClass, $extension);

        $test = $this->extensions->get($pluginName);
        $this->assertInstanceOf($pluginClass, $test);
        $this->assertNotSame($extension, $test);
    }
}
