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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see TestHelper.php
 */
require_once dirname(__FILE__) . '/../../../../TestHelper.php';

/**
 * @see Zend_Tool_Framework_Action_Base
 */
require_once 'Zend/Tool/Framework/Action/Base.php';

/**
 * @category   Zend
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * 
 * @group Zend_Tool
 * @group Zend_Tool_Framework
 * @group Zend_Tool_Framework_Action
 */
class Zend_Tool_Framework_Action_BaseTest extends PHPUnit_Framework_TestCase
{

    public function testBaseActionCanGetNameViaConstructor()
    {
        $baseAction = new Zend_Tool_Framework_Action_Base('Foo');
        $this->assertEquals('Foo', $baseAction->getName());
    }
    
    public function testBaseActionCanGetAndSetName()
    {
        $baseAction = new Zend_Tool_Framework_Action_Base();
        $this->assertEquals('Base', $baseAction->getName());
        $baseAction->setName('Foo');
        $this->assertEquals('Foo', $baseAction->getName());
    }
    
    
}
