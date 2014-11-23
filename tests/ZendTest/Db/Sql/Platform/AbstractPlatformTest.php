<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\Sql\Platform;

use ReflectionMethod;

/**
 * Tests for {@see \ZendTest\Db\Sql\Platform\AbstractPlatform}
 *
 * @covers \ZendTest\Db\Sql\Platform\AbstractPlatform
 */
class AbstractPlatformTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group 6890
     */
    public function testAbstractPlatformCrashesGracefullyOnMissingDefaultPlatform()
    {
        $platform = $this->getMockForAbstractClass('Zend\Db\Sql\Platform\AbstractPlatform');

        $reflectionMethod = new ReflectionMethod($platform, 'resolvePlatform');

        $reflectionMethod->setAccessible(true);

        $this->setExpectedException('Zend\Db\Sql\Exception\RuntimeException', '$this->defaultPlatform was not set');

        $reflectionMethod->invoke($platform);
    }
}
