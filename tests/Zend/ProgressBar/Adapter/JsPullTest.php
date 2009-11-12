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
 * @package    Zend_ProgressBar
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_ProgressBar_Adapter_JsPullTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_ProgressBar_Adapter_JsPullTest::main");
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * Zend_ProgressBar_Adapter_JsPull
 */
require_once 'Zend/ProgressBar/Adapter/JsPull.php';

/**
 * @category   Zend
 * @package    Zend_ProgressBar
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_ProgressBar
 */
class Zend_ProgressBar_Adapter_JsPullTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_ProgressBar_Adapter_JsPullTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testJson()
    {
        $adapter = new Zend_ProgressBar_Adapter_JsPull_Stub();
        $adapter->notify(0, 2, 0.5, 1, 1, 'status');
        $output = $adapter->getLastOutput();

        $data = json_decode($output, true);

        $this->assertEquals(0, $data['current']);
        $this->assertEquals(2, $data['max']);
        $this->assertEquals(50, $data['percent']);
        $this->assertEquals(1, $data['timeTaken']);
        $this->assertEquals(1, $data['timeRemaining']);
        $this->assertEquals('status', $data['text']);
        $this->assertFalse($data['finished']);

        $adapter->finish();
        $output = $adapter->getLastOutput();

        $data = json_decode($output, true);

        $this->assertTrue($data['finished']);
    }
}

class Zend_ProgressBar_Adapter_JsPull_Stub extends Zend_ProgressBar_Adapter_JsPull
{
    protected $_lastOutput = null;

    public function getLastOutput()
    {
        return $this->_lastOutput;
    }

    protected function _outputData($data)
    {
        $this->_lastOutput = $data;
    }
}

// Call Zend_ProgressBar_Adapter_JsPullTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_ProgressBar_Adapter_JsPullTest::main") {
    Zend_ProgressBar_Adapter_JsPullTest::main();
}
