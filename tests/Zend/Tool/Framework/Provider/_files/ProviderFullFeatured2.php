<?php

require_once 'Zend/Tool/Framework/Provider/Abstract.php';

class Zend_Tool_Framework_Provider_ProviderFullFeatured2 extends Zend_Tool_Framework_Provider_Abstract
{
    
    
    
    public function getName()
    {
        return 'FooBarBaz';
    }
    
    public function getSpecialties()
    {
        return array('Hi', 'BloodyMurder', 'ForYourTeam');
    }
    
    /**
     * Enter description here...
     *
     * @param string $what What is a string
     */
    public function say($what)
    {
        
    }
    
    public function scream($what = 'HELLO')
    {
        
    }
    
    public function sayHiAction()
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
    
}

