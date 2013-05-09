<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ServiceManager
 */

namespace ZendTest\Mvc\Service;

use Zend\Mvc\Service\DiFactory;
use Zend\ServiceManager\ServiceManager;

class DiFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testWillInitializeDiAndDiAbstractFactory()
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setService('Config', array('di' => array('')));
        $serviceManager->setFactory('Di', new DiFactory());

        $di = $serviceManager->get('Di');
        $this->assertInstanceOf('Zend\Di\Di', $di);
    }
}
