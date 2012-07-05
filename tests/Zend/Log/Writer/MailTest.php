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
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Log\Writer;

use Zend\Log\Logger;
use Zend\Log\Writer\Mail as MailWriter;
use Zend\Log\Formatter\Simple as SimpleFormatter;
use Zend\Mail\Message as MailMessage;
use Zend\Mail\Transport;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Log
 */
class MailTest extends \PHPUnit_Framework_TestCase
{
    const FILENAME = 'message.txt';
    /**
     * @var MailWriter
     */
    protected $writer;
    /**
     * @var Logger 
     */
    protected $log;
    
    protected function setUp()
    {
        $message = new MailMessage();
        $transport = new Transport\File();
        $options   = new Transport\FileOptions(array(
            'path'      => __DIR__,
            'callback'  => function (Transport\File $transport) {
                return MailTest::FILENAME;
            },
        ));
        $transport->setOptions($options);

        $this->writer = new MailWriter($message, $transport);
        $this->log = new Logger();
        $this->log->addWriter($this->writer);   
    }

    protected function tearDown()
    {
        @unlink(__DIR__. '/' . self::FILENAME);
    }

    /**
     * Tests normal logging, but with multiple messages for a level.
     *
     * @return void
     */
    public function testNormalLoggingMultiplePerLevel()
    {                
        $this->log->info('an info message');
        $this->log->info('a second info message');
        unset($this->log);
        
        $contents = file_get_contents(__DIR__ . '/' . self::FILENAME);
        $this->assertContains('an info message', $contents);
        $this->assertContains('a second info message', $contents);
    }

    public function testSetSubjectPrependText()
    {
        $this->writer->setSubjectPrependText('test');
        
        $this->log->info('an info message');
        $this->log->info('a second info message');
        unset($this->log);
        
        $contents = file_get_contents(__DIR__ . '/' . self::FILENAME);
        $this->assertContains('an info message', $contents);
        $this->assertContains('Subject: test', $contents);
    }
}
