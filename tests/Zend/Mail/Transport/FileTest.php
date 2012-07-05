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
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Mail\Transport;

use Zend\Mail\Message,
    Zend\Mail\Transport\File,
    Zend\Mail\Transport\FileOptions;

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Mail
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->tempDir = sys_get_temp_dir() . '/mail_file_transport';
        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir);
        } else {
            $this->cleanup($this->tempDir);
        }

        $fileOptions = new FileOptions(array(
            'path' => $this->tempDir,
        ));
        $this->transport  = new File($fileOptions);
    }

    public function tearDown()
    {
        $this->cleanup($this->tempDir);
        rmdir($this->tempDir);
    }

    protected function cleanup($dir)
    {
        foreach (glob($dir . '/*.*') as $file) {
            unlink($file);
        }
    }

    public function getMessage()
    {
        $message = new Message();
        $message->addTo('zf-devteam@zend.com', 'ZF DevTeam')
                ->addCc('matthew@zend.com')
                ->addBcc('zf-crteam@lists.zend.com', 'CR-Team, ZF Project')
                ->addFrom(array(
                    'zf-devteam@zend.com',
                    'Matthew' => 'matthew@zend.com',
                ))
                ->setSender('ralph.schindler@zend.com', 'Ralph Schindler')
                ->setSubject('Testing Zend\Mail\Transport\Sendmail')
                ->setBody('This is only a test.');
        $message->getHeaders()->addHeaders(array(
            'X-Foo-Bar' => 'Matthew',
        ));
        return $message;
    }

    public function testReceivesMailArtifacts()
    {
        $message = $this->getMessage();
        $this->transport->send($message);

        $this->assertNotNull($this->transport->getLastFile());
        $file = $this->transport->getLastFile();
        $test = file_get_contents($file);

        $this->assertEquals($message->toString(), $test);
    }
}
