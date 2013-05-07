<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Di;

use Zend\Di\Di;
use Zend\Di\DefinitionList;
use Zend\Di\InstanceManager;
use Zend\Di\Config;
use Zend\Di\Definition;

use Zend\Form\Form;

class DiCompatibilityTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @dataProvider providesSimpleClasses
     * @param string $class
     */
    public function testDiSimple($class)
    {
        $di = new Di();

        $bareObject = new $class;

        $diObject = $di->get($class);

        $this->assertInstanceOf($class, $bareObject, 'Test instantiate simple');
        $this->assertInstanceOf($class, $diObject, 'Test $di->get');
    }

    /**
     * provides known classes invokable without parameters
     *
     * @return array
     */
    public function providesSimpleClasses()
    {
        return array(
            array('Zend\Di\Di'),
            array('Zend\EventManager\EventManager'),
            array('Zend\Filter\Null'),
            array('Zend\Form\Form'),
            array('Zend\Stdlib\SplStack'),
            array('Zend\View\Model\ViewModel'),
        );
    }
}
