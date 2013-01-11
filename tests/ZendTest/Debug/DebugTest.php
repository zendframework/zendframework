<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_DebugTest.php
 */

namespace ZendTest;

use Zend\Debug\Debug;

/**
 * @category   Zend
 * @package    Zend_Debug
 * @subpackage UnitTests
 * @group      Zend_Debug
 */
class DebugTest extends \PHPUnit_Framework_TestCase
{

    public function testDebugDefaultSapi()
    {
        $sapi = php_sapi_name();
        Debug::setSapi(null);
        $data = 'string';
        $result = Debug::Dump($data, null, false);
        $this->assertEquals($sapi, Debug::getSapi());
    }

    public function testDebugDump()
    {
        Debug::setSapi('cli');
        $data = 'string';
        $result = Debug::Dump($data, null, false);
        $result = str_replace(array(PHP_EOL, "\n"), '_', $result);
        $expected = "__string(6) \"string\"__";
        $this->assertEquals($expected, $result);
    }

    public function testDebugCgi()
    {
        Debug::setSapi('cgi');
        $data = 'string';
        $result = Debug::Dump($data, null, false);

        // Has to check for two strings, because xdebug internally handles CLI vs Web
        $this->assertContains($result,
            array(
                "<pre>string(6) \"string\"\n</pre>",
                "<pre>string(6) &quot;string&quot;\n</pre>",
            )
        );
    }

    public function testDebugDumpEcho()
    {
        Debug::setSapi('cli');
        $data = 'string';

        ob_start();
        $result1 = Debug::Dump($data, null, true);
        $result2 = ob_get_contents();
        ob_end_clean();

        $this->assertContains('string(6) "string"', $result1);
        $this->assertEquals($result1, $result2);
    }

    public function testDebugDumpLabel()
    {
        Debug::setSapi('cli');
        $data = 'string';
        $label = 'LABEL';
        $result = Debug::Dump($data, $label, false);
        $result = str_replace(array(PHP_EOL, "\n"), '_', $result);
        $expected = "_{$label} _string(6) \"string\"__";
        $this->assertEquals($expected, $result);
    }

    /**
     * @group ZF-4136
     * @group ZF-1663
     */
    public function testXdebugEnabledAndNonCliSapiDoesNotEscapeSpecialChars()
    {
        if (!extension_loaded('xdebug')) {
            $this->markTestSkipped("This test only works in combination with xdebug.");
        }

        Debug::setSapi('apache');
        $a = array("a" => "b");

        $result = Debug::dump($a, "LABEL", false);
        $this->assertContains("<pre>", $result);
        $this->assertContains("</pre>", $result);
    }

}
