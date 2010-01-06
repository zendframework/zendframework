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
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * @see Zend_Feed_Entry_Atom
 */
require_once 'Zend/Feed/Entry/Atom.php';

/**
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Feed
 */
class Zend_Feed_ElementTest extends PHPUnit_Framework_TestCase
{

    public function testIsInitialized()
    {
        $e = new Zend_Feed_Entry_Atom();
        $e->author->name['last'] = 'hagenbuch';
        $e->author->name['first'] = 'chuck';
        $e->author->name->{'chuck:url'} = 'marina.horde.org';

        $e->author->title['foo'] = 'bar';
        if ($e->pants()) {
            $this->fail('<pants> does not exist, it should not have a true value');
            // This should not create an element in the actual tree.
        }
        if ($e->pants()) {
            $this->fail('<pants> should not have been created by testing for it');
            // This should not create an element in the actual tree.
        }

        $xml = $e->saveXml();

        $this->assertFalse(strpos($xml, 'pants'), '<pants> should not be in the xml output');
        $this->assertTrue(strpos($xml, 'marina.horde.org') !== false, 'the url attribute should be set');
    }

    public function testStrings()
    {
        $xml = "<entry>
    <title> Using C++ Intrinsic Functions for Pipelined Text Processing</title>
    <id>http://www.oreillynet.com/pub/wlg/8356</id>
    <link rel='alternate' href='http://www.oreillynet.com/pub/wlg/8356'/>
    <summary type='xhtml'>
    <div xmlns='http://www.w3.org/1999/xhtml'>
    A good C++ programming technique that has almost no published material available on the WWW relates to using the special pipeline instructions in modern CPUs for faster text processing. Here's example code using C++ intrinsic functions to give a fourfold speed increase for a UTF-8 to UTF-16 converter compared to the original C/C++ code.
    </div>
    </summary>
    <author><name>Rick Jelliffe</name></author>
    <updated>2005-11-07T08:15:57-08:00</updated>
</entry>";

        $entry = new Zend_Feed_Entry_Atom('uri', $xml);

        $this->assertTrue($entry->summary instanceof Zend_Feed_Element, '__get access should return an Zend_Feed_Element instance');
        $this->assertFalse($entry->summary() instanceof Zend_Feed_Element, 'method access should not return an Zend_Feed_Element instance');
        $this->assertTrue(is_string($entry->summary()), 'method access should return a string');
        $this->assertFalse(is_string($entry->summary), '__get access should not return a string');
    }

    public function testSetNamespacedAttributes()
    {
        $value = 'value';

        $e = new Zend_Feed_Entry_Atom();
        $e->test['attr']            = $value;
        $e->test['namespace1:attr'] = $value;
        $e->test['namespace2:attr'] = $value;

        $this->assertEquals($value, $e->test['attr']);
        $this->assertEquals($value, $e->test['namespace1:attr']);
        $this->assertEquals($value, $e->test['namespace2:attr']);
    }

    public function testUnsetNamespacedAttributes()
    {
        $value = 'value';

        $e = new Zend_Feed_Entry_Atom();
        $e->test['attr']            = $value;
        $e->test['namespace1:attr'] = $value;
        $e->test['namespace2:attr'] = $value;

        $this->assertEquals($value, $e->test['attr']);
        $this->assertEquals($value, $e->test['namespace1:attr']);
        $this->assertEquals($value, $e->test['namespace2:attr']);

        unset($e->test['attr']);
        unset($e->test['namespace1:attr']);
        unset($e->test['namespace2:attr']);

        $this->assertEquals('', $e->test['attr']);
        $this->assertEquals('', $e->test['namespace1:attr']);
        $this->assertEquals('', $e->test['namespace1:attr']);
    }

    /**
     * @group ZF-2606
     */
    public function testValuesWithXmlSpecialChars()
    {
        $testAmp = '&';
        $testLt  = '<';
        $testGt  = '>';

        $e = new Zend_Feed_Entry_Atom();
        $e->testAmp           = $testAmp;
        $e->{'namespace1:lt'} = $testLt;
        $e->{'namespace1:gt'} = $testGt;

        $this->assertEquals($testAmp, $e->testAmp());
        $this->assertEquals($testLt, $e->{'namespace1:lt'}());
        $this->assertEquals($testGt, $e->{'namespace1:gt'}());
    }

    /**
     * @group ZF-2606
     */
    public function testAttributesWithXmlSpecialChars()
    {
        $testAmp   = '&';
        $testLt    = '<';
        $testGt    = '>';
        $testQuot  = '"';
        $testSquot = "'";

        $e = new Zend_Feed_Entry_Atom();
        $e->test['amp']              = $testAmp;
        $e->test['namespace1:lt']    = $testLt;
        $e->test['namespace1:gt']    = $testGt;
        $e->test['namespace1:quot']  = $testQuot;
        $e->test['namespace1:squot'] = $testSquot;

        $this->assertEquals($testAmp, $e->test['amp']);
        $this->assertEquals($testLt, $e->test['namespace1:lt']);
        $this->assertEquals($testGt, $e->test['namespace1:gt']);
        $this->assertEquals($testQuot, $e->test['namespace1:quot']);
        $this->assertEquals($testSquot, $e->test['namespace1:squot']);
    }

}
