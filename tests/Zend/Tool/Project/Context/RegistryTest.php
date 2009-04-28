<?php


require_once dirname(__FILE__) . '/../../../../TestHelper.php';

require_once 'Zend/Tool/Project/Context/Repository.php';

require_once 'Zend/Debug.php';

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
