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
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Tool\Project\Context;
use Zend\Tool\Project\Context;

/**
 * @category   Zend
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group Zend_Tool
 * @group Zend_Tool_Project
 */
class RepositoryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        Context\Repository::resetInstance();
    }

    public function testGetInstanceReturnsIntstance()
    {
        $this->assertEquals('Zend\Tool\Project\Context\Repository', get_class(Context\Repository::getInstance()));
    }

    public function testNewRegistryHasSystemContexts()
    {
        $this->assertEquals(3, Context\Repository::getInstance()->count());
    }

    public function testRegistryReturnsSystemContext()
    {
        $this->assertEquals('Zend\Tool\Project\Context\System\ProjectProfileFile', get_class(Context\Repository::getInstance()->getContext('projectProfileFile')));
    }

    public function testRegistryLoadsZFContexts()
    {
        $this->_loadZfSystem();
        // the number of initial ZF Components
        $count = Context\Repository::getInstance()->count();
        $this->assertGreaterThanOrEqual(32, $count);
    }

    public function testRegistryThrowsExceptionOnUnallowedContextOverwrite()
    {
        $this->setExpectedException('Zend\Tool\Project\Context\Exception');
        Context\Repository::getInstance()->addContextClass('Zend\Tool\Project\Context\System\ProjectDirectory');
    }

    public function testRegistryThrowsExceptionOnUnknownContextRequest()
    {
        $this->setExpectedException('Zend\Tool\Project\Context\Exception');
        Context\Repository::getInstance()->getContext('somethingUnknown');
    }


    protected function _loadZfSystem()
    {
        $conextRegistry = Context\Repository::getInstance();
        $conextRegistry->addContextsFromDirectory(__DIR__ . '/../../../../../library/Zend/Tool/Project/Context/Zf/', 'Zend\Tool\Project\Context\Zf\\');
    }
}
