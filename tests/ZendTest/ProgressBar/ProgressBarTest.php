<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ProgressBar
 */

namespace ZendTest\ProgressBar;

/**
 * @category   Zend
 * @package    Zend_Console
 * @subpackage UnitTests
 * @group      Zend_ProgressBar
 */
class ProgressBarTest extends \PHPUnit_Framework_TestCase
{

    public function testGreaterMin()
    {
        $this->setExpectedException('Zend\ProgressBar\Exception\OutOfRangeException', '$max must be greater than $min');
        $progressBar = $this->_getProgressBar(1, 0);
    }

    public function testPersistence()
    {
        $progressBar = $this->_getProgressBar(0, 100, 'foobar');
        $progressBar->update(25);

        $progressBar = $this->_getProgressBar(0, 100, 'foobar');
        $progressBar->update();
        $this->assertEquals(25, $progressBar->getCurrent());
    }

    public function testDefaultPercentage()
    {
        $progressBar = $this->_getProgressBar(0, 100);
        $progressBar->update(25);

        $this->assertEquals(.25, $progressBar->getPercent());
    }

    public function testPositiveToPositivePercentage()
    {
        $progressBar = $this->_getProgressBar(10, 20);
        $progressBar->update(12.5);

        $this->assertEquals(.25, $progressBar->getPercent());
    }

    public function testNegativeToPositivePercentage()
    {
        $progressBar = $this->_getProgressBar(-5, 5);
        $progressBar->update(-2.5);

        $this->assertEquals(.25, $progressBar->getPercent());
    }

    public function testNegativeToNegativePercentage()
    {
        $progressBar = $this->_getProgressBar(-20, -10);
        $progressBar->update(-17.5);

        $this->assertEquals(.25, $progressBar->getPercent());
    }

    public function testEtaCalculation()
    {
        $progressBar = $this->_getProgressBar(0, 100);

        $progressBar->sleep(3);
        $progressBar->update(33);
        $progressBar->sleep(3);
        $progressBar->update(66);

        $this->assertEquals(3, $progressBar->getTimeRemaining());
    }

    public function testEtaZeroPercent()
    {
        $progressBar = $this->_getProgressBar(0, 100);

        $progressBar->sleep(5);
        $progressBar->update(0);

        $this->assertEquals(null, $progressBar->getTimeRemaining());
    }

    protected function _getProgressBar($min, $max, $persistenceNamespace = null)
    {
        return new Stub(new MockUp(), $min, $max, $persistenceNamespace);
    }
}

class Stub extends \Zend\ProgressBar\ProgressBar
{
    public function sleep($seconds)
    {
        $this->startTime -= $seconds;
    }

    public function getCurrent()
    {
        return $this->adapter->getCurrent();
    }

    public function getMax()
    {
        return $this->adapter->getMax();
    }

    public function getPercent()
    {
        return $this->adapter->getPercent();
    }

    public function getTimeTaken()
    {
        return $this->adapter->getTimeTaken();
    }

    public function getTimeRemaining()
    {
        return $this->adapter->getTimeRemaining();
    }

    public function getText()
    {
        return $this->adapter->getText();
    }
}

class MockUp extends \Zend\ProgressBar\Adapter\AbstractAdapter
{
    protected $_current;
    protected $_max;
    protected $_percent;
    protected $_timeTaken;
    protected $_timeRemaining;
    protected $_text;

    public function notify($current, $max, $percent, $timeTaken, $timeRemaining, $text)
    {
        $this->_current       = $current;
        $this->_max           = $max;
        $this->_percent       = $percent;
        $this->_timeTaken     = $timeTaken;
        $this->_timeRemaining = $timeRemaining;
        $this->_text          = $text;
    }

    public function finish()
    {

    }

    public function getCurrent()
    {
        return $this->_current;
    }

    public function getMax()
    {
        return $this->_max;
    }

    public function getPercent()
    {
        return $this->_percent;
    }

    public function getTimeTaken()
    {
        return $this->_timeTaken;
    }

    public function getTimeRemaining()
    {
        return $this->_timeRemaining;
    }

    public function getText()
    {
        return $this->_text;
    }
}
