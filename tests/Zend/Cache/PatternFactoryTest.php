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
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Cache;
use Zend\Cache,
    Zend\Loader\Broker;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cache
 */
class PatternFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        Cache\PatternFactory::resetBroker();
    }

    public function tearDown()
    {
        Cache\PatternFactory::resetBroker();
    }

    public function testDefaultBroker()
    {
        $broker = Cache\PatternFactory::getBroker();
        $this->assertInstanceOf('Zend\Cache\PatternBroker', $broker);
    }

    public function testChangeBroker()
    {
        $broker = new Cache\PatternBroker();
        Cache\PatternFactory::setBroker($broker);
        $this->assertSame($broker, Cache\PatternFactory::getBroker());
    }

}
