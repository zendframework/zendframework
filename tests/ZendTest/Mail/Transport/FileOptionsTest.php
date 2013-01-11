<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mail
 */

namespace ZendTest\Mail\Transport;

use Zend\Mail\Transport\FileOptions;

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @group      Zend_Mail
 */
class FileOptionsTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->options = new FileOptions();
    }

    public function testPathIsSysTempDirByDefault()
    {
        $this->assertEquals(sys_get_temp_dir(), $this->options->getPath());
    }

    public function testDefaultCallbackIsSetByDefault()
    {
        $callback = $this->options->getCallback();
        $this->assertTrue(is_callable($callback));
        $test     = call_user_func($callback, '');
        $this->assertRegExp('#^ZendMail_\d+_\d+\.tmp$#', $test);
    }

    public function testPathIsMutable()
    {
        $original = $this->options->getPath();
        $this->options->setPath(__DIR__);
        $test     = $this->options->getPath();
        $this->assertNotEquals($original, $test);
        $this->assertEquals(__DIR__, $test);
    }

    public function testCallbackIsMutable()
    {
        $original = $this->options->getCallback();
        $new      = function ($transport) {};
        $this->options->setCallback($new);
        $test     = $this->options->getCallback();
        $this->assertNotSame($original, $test);
        $this->assertSame($new, $test);
    }
}
