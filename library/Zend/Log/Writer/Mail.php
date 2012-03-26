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
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Log\Writer;

use Zend\Log\Formatter\Simple as SimpleFormatter,
    Zend\Log\Exception,
    Zend\Mail\Message,
    Zend\Mail\Transport,
    Zend\Mail\Transport\Exception as MailException,
    Zend\Mail\Transport\Sendmail as SendmailTransport;

/**
 * Class used for writing log messages to email via Zend_Mail.
 *
 * Allows for emailing log messages at and above a certain level via a
 * Zend_Mail object.  Note that this class only sends the email upon
 * completion, so any log entries accumulated are sent in a single email.
 *
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Mail extends AbstractWriter
{
    /**
     * Array of formatted events to include in message body.
     *
     * @var array
     */
    protected $eventsToMail = array();


    /**
     * Mail message instance to use
     *
     * @var Message
     */
    protected $mail;

    /**
     * Mail transport instance to use; optional.
     *
     * @var Transport
     */
    protected $transport;

    /**
     * Array keeping track of the number of entries per priority level.
     *
     * @var array
     */
    protected $numEntriesPerPriority = array();

    /**
     * Subject prepend text.
     *
     * Can only be used of the Zend_Mail object has not already had its
     * subject line set.  Using this will cause the subject to have the entry
     * counts per-priority level appended to it.
     *
     * @var string|null
     */
    protected $subjectPrependText;

    /**
     * Constructor
     *
     * Constructs the mail writer; requires a Zend_Mail instance, and takes an
     * optional Zend_Layout instance.  If Zend_Layout is being used,
     * $this->layout->events will be set for use in the layout template.
     *
     * @param  Mailer $mail Mail instance
     * @param  Layout|null $layout Layout instance
     * @return Mail
     */
    public function __construct(Message $mail, Transport $transport = null)
    {
        $this->mail = $mail;
        if (null !== $transport) {
            $this->setTransport($transport);
        } else {
            $this->setTransport(new SendmailTransport());
        }
        $this->formatter = new SimpleFormatter();
    }

    /**
     * Set the transport message
     *
     * @param  Transport $layout
     * @return Mail
     */
    public function setTransport(Transport $transport)
    {
        $this->transport = $transport;
        return $this;
    }

    /**
     * Places event line into array of lines to be used as message body.
     *
     * @param array $event Event data
     * @return void
     */
    protected function doWrite(array $event)
    {
        // Track the number of entries per priority level.
        if (!isset($this->numEntriesPerPriority[$event['priorityName']])) {
            $this->numEntriesPerPriority[$event['priorityName']] = 1;
        } else {
            $this->numEntriesPerPriority[$event['priorityName']]++;
        }

        // All plaintext events are to use the standard formatter.
        $this->eventsToMail[] = $this->formatter->format($event);
    }

    /**
     * Allows caller to have the mail subject dynamically set to contain the
     * entry counts per-priority level.
     *
     * Sets the text for use in the subject, with entry counts per-priority
     * level appended to the end.  Since a Zend_Mail_Message subject can only be set
     * once, this method cannot be used if the Zend_Mail_Message object already has a
     * subject set.
     *
     * @param  string $subject Subject prepend text
     * @return Mail
     * @throws Exception\RuntimeException
     */
    public function setSubjectPrependText($subject)
    {
        if ($this->mail->getSubject()) {
            throw new Exception\RuntimeException(
                'subject already set on mail; cannot set subject prepend text'
            );
        }

        $this->subjectPrependText = (string) $subject;
        return $this;
    }

    /**
     * Sends mail to recipient(s) if log entries are present.  Note that both
     * plaintext and HTML portions of email are handled here.
     *
     * @return void
     */
    public function shutdown()
    {
        // If there are events to mail, use them as message body.  Otherwise,
        // there is no mail to be sent.
        if (empty($this->eventsToMail)) {
            return;
        }

        if ($this->subjectPrependText !== null) {
            // Tack on the summary of entries per-priority to the subject
            // line and set it on the Zend_Mail object.
            $numEntries = $this->getFormattedNumEntriesPerPriority();
            $this->mail->setSubject("{$this->subjectPrependText} ({$numEntries})");
        }

        // Always provide events to mail as plaintext.
        $this->mail->setBody(implode('', $this->eventsToMail));

        // Finally, send the mail.  If an exception occurs, convert it into a
        // warning-level message so we can avoid an exception thrown without a
        // stack frame.
        try {
            $this->transport->send($this->mail);
        } catch (MailException $e) {
            trigger_error(
                "unable to send log entries via email; " .
                    "message = {$e->getMessage()}; " .
                    "code = {$e->getCode()}; " .
                        "exception class = " . get_class($e),
                E_USER_WARNING);
        }
    }

    /**
     * Gets a string of number of entries per-priority level that occurred, or
     * an empty string if none occurred.
     *
     * @return string
     */
    protected function getFormattedNumEntriesPerPriority()
    {
        $strings = array();

        foreach ($this->numEntriesPerPriority as $priority => $numEntries) {
            $strings[] = "{$priority}={$numEntries}";
        }

        return implode(', ', $strings);
    }
}
