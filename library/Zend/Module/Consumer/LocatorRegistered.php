<?php

namespace Zend\Module\Consumer;

/**
 * LocatorRegistered 
 *
 * By implementing this interface in a Module class, the instance of the Module 
 * class will be automatically injected into any DI-configured object which has 
 * a constructor or setter parameter which is type hinted with the Module class 
 * name. Implementing this interface obviously does not require adding any 
 * methods to your class.
 */
interface LocatorRegistered {}
