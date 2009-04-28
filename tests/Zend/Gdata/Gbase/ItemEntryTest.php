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
class Zend_Gdata_Gbase_ItemEntryTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->itemEntry = new Zend_Gdata_Gbase_ItemEntry();
    }

    public function testToAndFromString()
    {
        $this->itemEntry->setItemType('products');
        $this->assertEquals($this->itemEntry->getItemType()->getText(), 'products');

        $this->itemEntry->addGbaseAttribute('price', '10.99 USD', 'floatUnit');
        $baseAttribute = $this->itemEntry->getGbaseAttribute('price');
        $this->assertEquals(count($baseAttribute), 1);
        $this->assertEquals($baseAttribute[0]->getName(), 'price');
        $this->assertEquals($baseAttribute[0]->getText(), '10.99 USD');
        $this->assertEquals($baseAttribute[0]->getType(), 'floatUnit');

        $newItemEntry = new Zend_Gdata_Gbase_ItemEntry();
        $doc = new DOMDocument();
        $doc->loadXML($this->itemEntry->saveXML());
        $newItemEntry->transferFromDom($doc->documentElement);
        $rowDataFromXML = $newItemEntry->getGbaseAttribute('price');

        $this->assertEquals($this->itemEntry->getItemType()->getText(), $newItemEntry->getItemType()->getText());
        $this->assertEquals(count($rowDataFromXML), 1);
        $this->assertEquals($rowDataFromXML[0]->getName(), 'price');
        $this->assertEquals($rowDataFromXML[0]->getText(), '10.99 USD');
        $this->assertEquals($rowDataFromXML[0]->getType(), 'floatUnit');        
    }

}
