<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Hydrator
 */

namespace ZendTest\Stdlib\Hydrator;

use \PHPUnit_Framework_TestCase as TestCase;

/**
 * @requires PHP 5.4
 */
class HydratorAwareTraitTest extends TestCase
{
    public function testSetHydrator()
    {
        $object = $this->getObjectForTrait('\Zend\Stdlib\Hydrator\HydratorAwareTrait');

        $this->assertAttributeEquals(null, 'hydrator', $object);

        $hydrator = $this->getMockForAbstractClass('\Zend\Stdlib\Hydrator\AbstractHydrator');

        $object->setHydrator($hydrator);

        $this->assertAttributeEquals($hydrator, 'hydrator', $object);
    }

    public function testGetHydrator()
    {
        $object = $this->getObjectForTrait('\Zend\Stdlib\Hydrator\HydratorAwareTrait');

        $this->assertNull($object->getHydrator());

        $hydrator = $this->getMockForAbstractClass('\Zend\Stdlib\Hydrator\AbstractHydrator');

        $object->setHydrator($hydrator);

        $this->assertEquals($hydrator, $object->getHydrator());
    }
}
