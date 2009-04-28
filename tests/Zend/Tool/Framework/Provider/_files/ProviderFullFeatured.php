<?php

require_once 'Zend/Tool/Framework/Provider/Abstract.php';

class Zend_Tool_Framework_Provider_ProviderFullFeatured extends Zend_Tool_Framework_Provider_Abstract
{
    
    protected $_specialties = array('Hi', 'BloodyMurder', 'ForYourTeam');
    
    public function getName()
    {
        return 'FooBarBaz';
    }
    
    public function say($what)
    {
        
    }
    
    public function scream($what = 'HELLO')
    {
        
    }
    
    public function sayHi()
    {
        
    }
    
    public function screamBloodyMurder()
    {
        
    }
    
    public function screamForYourTeam()
    {
        
    }
    
    protected function _iAmNotCallable()
    {
        
    }
    
    public function _testReturnInternals()
    {
        return array($this->_registry->getRequest(), $this->_registry->getResponse());
    }
    
}

