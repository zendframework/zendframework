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
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * Exclude from code coverage report
 */
PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 * Patch for default timezone in PHP >= 5.1.0
 */
if (!ini_get('date.timezone')) {
    date_default_timezone_set(@date_default_timezone_get());
}

/**
 * @see Zend_Service_Technorati
 */
require_once 'Zend/Service/Technorati.php';


/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Technorati_TestCase extends PHPUnit_Framework_TestCase
{
    protected function _testConstruct($className, $args)
    {
        $reflection = new ReflectionClass($className);
        try {
            $object = $reflection->newInstanceArgs($args);
            $this->assertType($className, $object);
        } catch (Zend_Service_Technorati_Exception $e) {
            $this->fail("Exception " . $e->getMessage() . " thrown");
        }
    }

    protected function _testConstructThrowsExceptionWithInvalidDom($className, $match)
    {
        if (self::skipInvalidArgumentTypeTests()) {
            $this->markTestIncomplete('Failure to meet type hint results in fatal error in PHP < 5.2.0');
            return;
        }

        // This test is unnecessary. PHP type hinting is well tested, and will throw 
        // catchable fatal errors on invalid argument types. Do nothing here.
    }

    protected function _testResultSetItemsInstanceOfResult($resultSetClassName, $args, $resultClassName)
    {
        $reflection = new ReflectionClass($resultSetClassName);
        $resultset = $reflection->newInstanceArgs($args);
        foreach ($resultset as $result) {
            $this->assertType($resultClassName, $result);
        }
    }

    protected function _testResultSetSerialization($resultSet)
    {
        $unobject = unserialize(serialize($resultSet));
        $unresult = null;

        $this->assertType(get_class($resultSet), $unobject);

        foreach ($resultSet as $index => $result) {
            try {
                $unobject->seek($index);
                $unresult = $unobject->current();
            } catch(OutOfBoundsException $e) {
                $this->fail("Missing result index $index");
            }
            $this->assertEquals($result, $unresult);
        }
    }

    protected function _testResultSerialization($result)
    {
        /**
         * Both Result and ResultSet objects includes variables
         * that references special objects such as DomDocuments.
         * 
         * Unlike ResultSet(s), Result instances uses Dom fragments
         * only to construct the instance itself, then both Dom and Xpath objects
         * are no longer required.
         * 
         * It means serializing a Result is not a painful job.
         * We don't need to implement any __wakeup or _sleep function
         * because PHP is able to create a perfect serialized snapshot
         * of current object status.
         * 
         * Thought this situation makes our life easier, it's not safe
         * to assume things will not change in the future.
         * Testing each object now against a serialization request
         * makes this library more secure in the future!
         */
        $unresult = unserialize(serialize($result));

        $this->assertType(get_class($result), $unresult);
        $this->assertEquals($result, $unresult);
    }

    public static function getTestFilePath($file)
    {
        return dirname(__FILE__) . '/_files/' . $file;
    }

    public static function getTestFileContentAsDom($file)
    {
        $dom = new DOMDocument();
        $dom->load(self::getTestFilePath($file));
        return $dom;
    }

    public static function getTestFileElementsAsDom($file, $exp = '//item')
    {
        $dom = self::getTestFileContentAsDom($file);
        $xpath = new DOMXPath($dom);
        return $xpath->query($exp);
    }

    public static function getTestFileElementAsDom($file, $exp = '//item', $item = 0)
    {
        $dom = self::getTestFileContentAsDom($file);
        $xpath = new DOMXPath($dom);
        $domElements = $xpath->query($exp);
        return $domElements->item($item);
    }

    public static function skipInvalidArgumentTypeTests()
    {
        // PHP < 5.2.0 returns a fatal error
        // instead of a catchable Exception (ZF-2334)
        return version_compare(phpversion(), "5.2.0", "<");
    }

}
