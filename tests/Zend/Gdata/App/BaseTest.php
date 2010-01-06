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
 * @package    Zend_Gdata_App
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

require_once 'Zend/Gdata/App/MockBase.php';

/**
 * @category   Zend
 * @package    Zend_Gdata_App
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Gdata
 * @group      Zend_Gdata_App
 */
class Zend_Gdata_App_BaseTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->fileName = 'Zend/Gdata/App/_files/FeedSample1.xml';
        $this->base = new Zend_Gdata_App_MockBase();
    }

    public function testUnknownNamespaceReturnsInput() {
        $this->assertEquals('example',
                $this->base->lookupNamespace('example'));
    }
    public function testAtomV1NamespaceReturnedByDefault() {
        $this->assertEquals('http://www.w3.org/2005/Atom',
                $this->base->lookupNamespace('atom'));
    }

    public function testAtomPubV1NamespaceReturnedByDefault() {
        $this->assertEquals('http://purl.org/atom/app#',
                $this->base->lookupNamespace('app'));
    }

    public function testAtomV1NamespaceReturnedWhenSpecifyingMajorVersion() {
        $this->assertEquals('http://www.w3.org/2005/Atom',
                $this->base->lookupNamespace('atom',
                1));
    }

    public function testAtomV1NamespaceReturnedWhenSpecifyingMajorAndMinorVersion() {
        $this->assertEquals('http://www.w3.org/2005/Atom',
                $this->base->lookupNamespace('atom',
                1, 0));
    }

    public function testAtomPubV1NamespaceReturnedWhenSpecifyingMajorVersion() {
        $this->assertEquals('http://purl.org/atom/app#',
                $this->base->lookupNamespace('app',
                1));
    }

    public function testAtomPubV1NamespaceReturnedWhenSpecifyingMajorAndMinorVersion() {
        $this->assertEquals('http://purl.org/atom/app#',
                $this->base->lookupNamespace('app',
                1, 0));
    }

    public function testAtomPubV2NamespaceReturnedWhenSpecifyingMajorVersion() {
        $this->assertEquals('http://www.w3.org/2007/app',
                $this->base->lookupNamespace('app',
                2));
    }

    public function testAtomPubV2NamespaceReturnedWhenSpecifyingMajorAndMinorVersion() {
        $this->assertEquals('http://www.w3.org/2007/app',
                $this->base->lookupNamespace('app',
                2, 0));
    }

    public function testNullReturnsLatestVersion() {
        $this->assertEquals('http://www.w3.org/2007/app',
                $this->base->lookupNamespace('app',
                null, null));
    }

    public function testRegisterNamespaceWorksWithoutVersion() {
        $ns = 'http://example.net/namespaces.foo';
        $prefix = 'foo';
        $this->base->registerNamespace($prefix, $ns);
        $result = $this->base->lookupNamespace($prefix);
        $this->assertEquals($ns, $result);
    }

    public function testRegisterNamespaceAllowsSettingMajorVersion() {
        $ns = 'http://example.net/namespaces.foo';
        $prefix = 'foo';
        $this->base->registerNamespace($prefix, 'wrong-1', 1);
        $this->base->registerNamespace($prefix, $ns, 2);
        $this->base->registerNamespace($prefix, 'wrong-3', 3);
        $this->base->registerNamespace($prefix, 'wrong-4', 4);
        $result = $this->base->lookupNamespace($prefix, 2);
        $this->assertEquals($ns, $result);
    }

    public function testRegisterNamespaceAllowsSettingMinorVersion() {
        $ns = 'http://example.net/namespaces.foo';
        $prefix = 'foo';
        $this->base->registerNamespace($prefix, 'wrong-1', 1);
        $this->base->registerNamespace($prefix, 'wrong-2-0', 2,0);
        $this->base->registerNamespace($prefix, 'wrong-2-1', 2,1);
        $this->base->registerNamespace($prefix, 'wrong-2-2', 2,2);
        $this->base->registerNamespace($prefix, $ns, 2, 3);
        $this->base->registerNamespace($prefix, 'wrong-2-4', 2,4);
        $this->base->registerNamespace($prefix, 'wrong-3-0', 3-0);
        $this->base->registerNamespace($prefix, 'wrong-3-1', 3-1);
        $this->base->registerNamespace($prefix, 'wrong-4', 4);
        $result = $this->base->lookupNamespace($prefix, 2, 3);
        $this->assertEquals($ns, $result);
    }

}
