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
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Gdata/Gbase.php';
require_once 'Zend/Http/Client.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_Gbase_BaseAttributeTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->baseAttribute = new Zend_Gdata_Gbase_Extension_BaseAttribute();
    }

    public function testToAndFromString()
    {

        $this->baseAttribute->setName('price');
        $this->baseAttribute->setText('10.99 USD');
        $this->baseAttribute->setType('floatUnit');

        $this->assertTrue($this->baseAttribute->getName() == 'price');
        $this->assertTrue($this->baseAttribute->getText() == '10.99 USD');
        $this->assertTrue($this->baseAttribute->getType() == 'floatUnit');

        $newBaseAttribute = new Zend_Gdata_Gbase_Extension_BaseAttribute();
        $doc = new DOMDocument();
        $doc->loadXML($this->baseAttribute->saveXML());
        $newBaseAttribute->transferFromDom($doc->documentElement);

        $this->assertTrue($this->baseAttribute->getName() == $newBaseAttribute->getName());
        $this->assertTrue($this->baseAttribute->getText() == $newBaseAttribute->getText());
        $this->assertTrue($this->baseAttribute->getType() == $newBaseAttribute->getType());
    }

}
