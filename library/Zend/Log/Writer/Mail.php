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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Log\Writer;

use Zend\Log\Formatter\Simple as SimpleFormatter,
    Zend\Log\Formatter,
    Zend\Log\Exception,
    Zend\Mail\Mail as Mailer,
    Zend\Layout\Layout;

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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
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
     * Array of formatted lines for use in an HTML email body; these events
     * are formatted with an optional formatter if the caller is using
     * Zend_Layout.
     *
     * @var array
     */
    protected $layoutEventsToMail = array();

    /**
     * Zend_Mail instance to use
     *
     * @var Mailer
     */
    protected $mail;

    /**
     * Zend_Layout instance to use; optional.
     *
     * @var Layout
     */
    protected $layout;

    /**
     * Optional formatter for use when rendering with Zend_Layout.
     *
     * @var Formatter
     */
    protected $layoutFormatter;

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
     * @param Mailer $mail Mail instance
     * @param Layout|null $layout Layout instance
     * @return Mail
     */
    public function __construct(Mailer $mail, Layout $layout = null)
    {
        $this->mail = $mail;
        if (null !== $layout) {
            $this->setLayout($layout);
        }
        $this->formatter = new SimpleFormatter();
    }

    /**
     * Set the layout
     *
     * @param Layout $layout
     * @return Mail
     */
    public function setLayout(Layout $layout)
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * Places event line into array of lines to be used as message body.
     *
     * Handles the formatting of both plaintext entries, as well as those
     * rendered with Zend_Layout.
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

        $formattedEvent = $this->formatter->format($event);

        // All plaintext events are to use the standard formatter.
        $this->eventsToMail[] = $formattedEvent;

        // If we have a Zend_Layout instance, use a specific formatter for the
        // layout if one exists.  Otherwise, just use the event with its
        // default format.
        if ($this->layout) {
            if ($this->layoutFormatter) {
                $this->layoutEventsToMail[] = $this->layoutFormatter->format($event);
            } else {
                $this->layoutEventsToMail[] = $formattedEvent;
            }
        }
    }

    /**
     * Gets instance of Zend_Log_Formatter used for formatting a
     * message using Zend_Layout, if applicable.
     *
     * @return Formatter|null The formatter, or null.
     */
    public function getLayoutFormatter()
    {
        return $this->layoutFormatter;
    }

    /**
     * Sets a specific formatter for use with Zend_Layout events.
     *
     * Allows use of a second formatter on lines that will be rendered with
     * Zend_Layout.  In the event that Zend_Layout is not being used, this
     * formatter cannot be set, so an exception will be thrown.
     *
     * @param Formatter $formatter
     * @return Mail
     * @throws Exception\InvalidArgumentException
     */
    public function setLayoutFormatter(Formatter $formatter)
    {
        if (!$this->layout) {
            throw new Exception\InvalidArgumentException(
                'cannot set formatter for layout; '
                    . 'a Zend\Layout\Layout instance is not in use'
            );
        }

        $this->layoutFormatter = $formatter;
        return $this;
    }

    /**
     * Allows caller to have the mail subject dynamically set to contain the
     * entry counts per-priority level.
     *
     * Sets the text for use in the subject, with entry counts per-priority
     * level appended to the end.  Since a Zend_Mail subject can only be set
     * once, this method cannot be used if the Zend_Mail object already has a
     * subject set.
     *
     * @param string $subject Subject prepend text
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
        $this->mail->setBodyText(implode('', $this->eventsToMail));

        // If a Zend_Layout instance is being used, set its "events"
        // value to the lines formatted for use with the layout.
        if ($this->layout) {
            // Set the required "messages" value for the layout.  Here we
            // are assuming that the layout is for use with HTML.
            $this->layout->events = implode('', $this->layoutEventsToMail);

            // If an exception occurs during rendering, convert it to a notice
            // so we can avoid an exception thrown without a stack frame.
            try {
                $this->mail->setBodyHtml($this->layout->render());
            } catch (\Exception $e) {
                trigger_error(
                    "exception occurred when rendering layout; "
                        . "unable to set html body for message; "
                        . "message = {$e->getMessage()}; "
                        . "code = {$e->getCode()}; "
                        . "exception class = " . get_class($e),
                    E_USER_NOTICE);
            }
        }

        // Finally, send the mail.  If an exception occurs, convert it into a
        // warning-level message so we can avoid an exception thrown without a
        // stack frame.
        try {
            $this->mail->send();
        } catch (\Exception $e) {
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