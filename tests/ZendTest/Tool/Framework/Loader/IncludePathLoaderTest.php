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
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Tool\Framework\Loader;

/**
 * @category   Zend
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group Zend_Tool
 * @group Zend_Tool_Framework
 * @group Zend_Tool_Framework_Loader
 */
class IncludePathLoaderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Zend_Tool_Framework_Registry
     */
    protected $_registry = null;

    public function setUp()
    {
        $this->markTestSkipped('Skipped until refactoring');
    }

    public function tearDown()
    {
        //\Zend\Tool\Framework\Registry\FrameworkRegistry::resetInstance();
    }

    public function testStub()
    {
        $this->assertTrue(true);
    }
    
    /** running these tests need to happen in separate process */
    /**


    public function testLoaderFindsIncludePathFilesAreFound()
    {
        $loader = new Zend_Tool_Framework_Loader_IncludePathLoader();
        $loader->load();
        $files = $loader->getLoadRetrievedFiles();
        foreach ($files as $index => $file) {
            $files[$index] = substr($file, strpos($file, 'Zend'));
        }
        $this->assertContains('Zend/Tool/Framework/System/Manifest.php', $files);
    }

    public function testLoaderFindsIncludePathFilesAreLoaded()
    {
        $loader = new Zend_Tool_Framework_Loader_IncludePathLoader();
        $loader->load();
        $classes = $loader->getLoadLoadedClasses();
        $this->assertContains('Zend_Tool_Framework_System_Manifest', $classes);
    }

    */

}
