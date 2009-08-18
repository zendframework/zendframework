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

// Call Zend_ProgressBar_Adapter_jsPushTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_ProgressBar_Adapter_jsPushTest::main");
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * Zend_ProgressBar_Adapter_JsPush
 */
require_once 'Zend/ProgressBar/Adapter/JsPush.php';

/**
 * @category   Zend
 * @package    Zend_ProgressBar
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_ProgressBar
 */
class Zend_ProgressBar_Adapter_jsPushTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_ProgressBar_Adapter_jsPushTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testJson()
    {
        $result = array();
        
        $adapter = new Zend_ProgressBar_Adapter_jsPush_Stub(array('finishMethodName' => 'Zend_ProgressBar_Finish'));      
        $adapter->notify(0, 2, 0.5, 1, 1, 'status');
        $output = $adapter->getLastOutput();

        $matches = preg_match('#<script type="text/javascript">parent.Zend_ProgressBar_Update\((.*?)\);</script>#', $output, $result);
        $this->assertEquals(1, $matches);
        
        $data = json_decode($result[1], true);
        
        $this->assertEquals(0, $data['current']);
        $this->assertEquals(2, $data['max']);
        $this->assertEquals(50, $data['percent']);
        $this->assertEquals(1, $data['timeTaken']);
        $this->assertEquals(1, $data['timeRemaining']);
        $this->assertEquals('status', $data['text']);
        
        $adapter->finish();
        $output = $adapter->getLastOutput();

        $matches = preg_match('#<script type="text/javascript">parent.Zend_ProgressBar_Finish\(\);</script>#', $output, $result);
        $this->assertEquals(1, $matches);
    }
}

class Zend_ProgressBar_Adapter_jsPush_Stub extends Zend_ProgressBar_Adapter_jsPush
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

// Call Zend_ProgressBar_Adapter_jsPushTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_ProgressBar_Adapter_jsPushTest::main") {
    Zend_ProgressBar_Adapter_jsPushTest::main();
}
