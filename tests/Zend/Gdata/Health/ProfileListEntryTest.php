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
 * @package    Zend_Gdata_Health
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

require_once 'Zend/Gdata/Health.php';
require_once 'Zend/Gdata/Health/ProfileListEntry.php';

/**
 * @category   Zend
 * @package    Zend_Gdata_Health
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Gdata
 * @group      Zend_Gdata_Health
 */
class Zend_Gdata_Health_ProfileListEntryTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->entry = new Zend_Gdata_Health_ProfileListEntry();
        $this->entryText = file_get_contents(
            'Zend/Gdata/Health/_files/TestDataHealthProfileListEntrySample.xml', true);
    }

    public function testEmptyProfileEntryToAndFromStringShouldMatch() {
        $this->entry->transferFromXML($this->entryText);
        $entryXml = $this->entry->saveXML();
        $newProfileListEntry = new Zend_Gdata_Health_ProfileListEntry();
        $newProfileListEntry->transferFromXML($entryXml);
        $newProfileListEntryXML = $newProfileListEntry->saveXML();
        $this->assertTrue($entryXml == $newProfileListEntryXML);
    }

    public function testGetProfileID() {
        $this->entry->transferFromXML($this->entryText);
        $this->assertEquals('vndCn5sdfwdEIY', $this->entry->getProfileID());
    }

    public function testGetProfileName() {
        $this->entry->transferFromXML($this->entryText);
        $this->assertEquals('profile name', $this->entry->getProfileName());
    }
}

