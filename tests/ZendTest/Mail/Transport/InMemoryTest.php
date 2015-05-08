<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mail\Transport;

use Zend\Mail\Message;
use Zend\Mail\Transport\InMemory;

/**
 * @group      Zend_Mail
 */
class InMemoryTest extends \PHPUnit_Framework_TestCase
{
    public function getMessage()
    {
        $message = new Message();
        $message->addTo('zf-devteam@zend.com', 'ZF DevTeam')
                ->addCc('matthew@zend.com')
                ->addBcc('zf-crteam@lists.zend.com', 'CR-Team, ZF Project')
                ->addFrom(array(
                    'zf-devteam@zend.com',
                    'matthew@zend.com' => 'Matthew',
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
        $transport = new InMemory();

        $transport->send($message);

        $this->assertSame($message, $transport->getLastMessage());
    }
}
