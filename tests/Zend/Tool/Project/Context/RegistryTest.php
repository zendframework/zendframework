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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


require_once dirname(__FILE__) . '/../../../../TestHelper.php';

require_once 'Zend/Tool/Project/Context/Repository.php';

require_once 'Zend/Debug.php';

/**
 * @category   Zend
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * 
 * @group Zend_Tool
 * @group Zend_Tool_Project
 */
class Zend_Tool_Project_Context_RepositoryTest extends PHPUnit_Framework_TestCase
{
    
    public function setUp()
    {
        Zend_Tool_Project_Context_Repository::resetInstance();
    }
    
    public function testGetInstanceReturnsIntstance()
    {
        $this->assertEquals('Zend_Tool_Project_Context_Repository', get_class(Zend_Tool_Project_Context_Repository::getInstance()));
    }
    
    public function testNewRegistryHasSystemContexts()
    {
        $this->assertEquals(3, Zend_Tool_Project_Context_Repository::getInstance()->count());
    }
    
    public function testRegistryReturnsSystemContext()
    {
        $this->assertEquals('Zend_Tool_Project_Context_System_ProjectProfileFile', get_class(Zend_Tool_Project_Context_Repository::getInstance()->getContext('projectProfileFile')));
    }
    
    public function testRegistryLoadsZFContexts()
    {
        $this->_loadZfSystem();
        // the number of initial ZF Components
        $count = Zend_Tool_Project_Context_Repository::getInstance()->count();
        $this->assertGreaterThanOrEqual(32, $count);
    }
    
    /**
     * @expectedException Zend_Tool_Project_Context_Exception
     */
    public function testRegistryThrowsExceptionOnUnallowedContextOverwrite()
    {
        
        Zend_Tool_Project_Context_Repository::getInstance()->addContextClass('Zend_Tool_Project_Context_System_ProjectDirectory');
    }
    
    /**
     * @expectedException Zend_Tool_Project_Context_Exception
     */
    public function testRegistryThrowsExceptionOnUnknownContextRequest()
    {
        Zend_Tool_Project_Context_Repository::getInstance()->getContext('somethingUnknown');
    }
    
    
    protected function _loadZfSystem()
    {
        $conextRegistry = Zend_Tool_Project_Context_Repository::getInstance();
        $conextRegistry->addContextsFromDirectory(dirname(__FILE__) . '/../../../../../library/Zend/Tool/Project/Context/Zf/', 'Zend_Tool_Project_Context_Zf_');
    }
}
