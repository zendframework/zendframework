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
 * @package    Zend_GData_Spreadsheets
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\GData\Spreadsheets;
use Zend\GData\Spreadsheets\Extension;

/**
 * @category   Zend
 * @package    Zend_GData_Spreadsheets
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_Spreadsheets
 */
class CustomTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->custom = new Extension\Custom();
    }

    public function testToAndFromString()
    {
        $this->custom->setText('value');
        $this->assertTrue($this->custom->getText() == 'value');
        $this->custom->setColumnName('column_name');
        $this->assertTrue($this->custom->getColumnName() == 'column_name');
        $newCustom = new Extension\Custom();
        $doc = new \DOMDocument();
        $doc->loadXML($this->custom->saveXML());
        $newCustom->transferFromDom($doc->documentElement);
        $this->assertTrue($this->custom->getText() == $newCustom->getText());
        $this->assertTrue($this->custom->getColumnName() == $newCustom->getColumnName());
    }

}
