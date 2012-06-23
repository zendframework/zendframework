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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\ProgressBar\Adapter;

/**
 * @category   Zend
 * @package    Zend_ProgressBar
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_ProgressBar
 */
class JsPushTest extends \PHPUnit_Framework_TestCase
{

    public function testJson()
    {
        $result = array();

        $adapter = new JsPushStub(array('finishMethodName' => 'Zend\ProgressBar\ProgressBar\Finish'));
        $adapter->notify(0, 2, 0.5, 1, 1, 'status');
        $output = $adapter->getLastOutput();

        $matches = preg_match('#<script type="text/javascript">parent.'. preg_quote('Zend\\ProgressBar\\ProgressBar\\Update') . '\((.*?)\);</script>#', $output, $result);
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

        $matches = preg_match('#<script type="text/javascript">parent.'. preg_quote('Zend\ProgressBar\ProgressBar\Finish') . '\(\);</script>#', $output, $result);
        $this->assertEquals(1, $matches);
    }
}

class JsPushStub extends \Zend\ProgressBar\Adapter\JsPush
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
