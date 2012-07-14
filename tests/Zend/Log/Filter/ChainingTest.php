<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */

namespace ZendTest\Log\Filter;

use Zend\Log\Logger;
use Zend\Log\Writer;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @group      Zend_Log
 */
class ChainingTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->log = fopen('php://memory', 'w');
        $this->logger = new Logger();
        $this->logger->addWriter(new Writer\Stream($this->log));
    }

    public function tearDown()
    {
        fclose($this->log);
    }

    /**
     * @group disable
     */
    public function testFilterAllWriters()
    {
        // filter out anything above a WARNing for all writers
        $this->logger->addFilter(Logger::WARN);

        $this->logger->info($ignored = 'info-message-ignored');
        $this->logger->warn($logged  = 'warn-message-logged');

        rewind($this->log);
        $logdata = stream_get_contents($this->log);

        $this->assertNotContains($ignored, $logdata);
        $this->assertContains($logged, $logdata);
    }

    public function testFilterOnSpecificWriter()
    {
        $log2 = fopen('php://memory', 'w');
        $writer2 = new Writer\Stream($log2);
        $writer2->addFilter(Logger::ERR);

        $this->logger->addWriter($writer2);

        $this->logger->warn($warn = 'warn-message');
        $this->logger->err($err = 'err-message');

        rewind($this->log);
        $logdata = stream_get_contents($this->log);

        $this->assertContains($warn, $logdata);
        $this->assertContains($err, $logdata);

        rewind($log2);
        $logdata = stream_get_contents($log2);

        $this->assertContains($err, $logdata);
        $this->assertNotContains($warn, $logdata);
    }
}
