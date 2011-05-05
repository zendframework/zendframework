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
use Zend\Log;

/**
 * Class used for writing log messages to email via Zend_Mail.
 *
 * Allows for emailing log messages at and above a certain level via a
 * Zend_Mail object.  Note that this class only sends the email upon
 * completion, so any log entries accumulated are sent in a single email.
 *
 * @uses       \Zend\Log\Exception\InvalidArgumentException
 * @uses       \Zend\Log\Exception\RuntimeException
 * @uses       \Zend\Log\Formatter\Simple
 * @uses       \Zend\Log\Writer\AbstractWriter
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
    protected $_eventsToMail = array();

    /**
     * Array of formatted lines for use in an HTML email body; these events
     * are formatted with an optional formatter if the caller is using
     * Zend_Layout.
     *
     * @var array
     */
    protected $_layoutEventsToMail = array();

    /**
     * Zend_Mail instance to use
     *
     * @var \Zend\Mail\Mail
     */
    protected $_mail;

    /**
     * Zend_Layout instance to use; optional.
     *
     * @var \Zend\Layout\Layout
     */
    protected $_layout;

    /**
     * Optional formatter for use when rendering with Zend_Layout.
     *
     * @var \Zend\Log\Formatter
     */
    protected $_layoutFormatter;

    /**
     * Array keeping track of the number of entries per priority level.
     *
     * @var array
     */
    protected $_numEntriesPerPriority = array();

    /**
     * Subject prepend text.
     *
     * Can only be used of the Zend_Mail object has not already had its
     * subject line set.  Using this will cause the subject to have the entry
     * counts per-priority level appended to it.
     *
     * @var string|null
     */
    protected $_subjectPrependText;

    /**
     * MethodMap for \Zend\Mail\Mail's headers
     *
     * @var array
     */
    protected static $_methodMapHeaders = array(
        'from' => 'setFrom',
        'to' => 'addTo',
        'cc' => 'addCc',
        'bcc' => 'addBcc',
    );

    /**
     * Class constructor.
     *
     * Constructs the mail writer; requires a Zend_Mail instance, and takes an
     * optional Zend_Layout instance.  If Zend_Layout is being used,
     * $this->_layout->events will be set for use in the layout template.
     *
     * @param  \Zend\Mail\Mail $mail Mail instance
     * @param  \Zend\Layout\Layout $layout Layout instance; optional
     * @return void
     */
    public function __construct(\Zend\Mail\Mail $mail, \Zend\Layout\Layout $layout = null)
    {
        $this->_mail = $mail;
        if (null !== $layout) {
            $this->setLayout($layout);
        }
        $this->_formatter = new Log\Formatter\Simple();
    }

    /**
     * Create a new instance of Zend_Log_Writer_Mail
     *
     * @param  array|\Zend\Config\Config $config
     * @return \Zend\Log\Writer\Mail
     */
    static public function factory($config = array())
    {
        $config = self::_parseConfig($config);
        $mail = self::_constructMailFromConfig($config);
        $writer = new self($mail);

        if (isset($config['layout']) || isset($config['layoutOptions'])) {
            $writer->setLayout($config);
        }
        if (isset($config['layoutFormatter'])) {
            $layoutFormatter = new $config['layoutFormatter'];
            $writer->setLayoutFormatter($layoutFormatter);
        }
        if (isset($config['subjectPrependText'])) {
            $writer->setSubjectPrependText($config['subjectPrependText']);
        }

        return $writer;
    }

    /**
     * Set the layout
     *
     * @param \Zend\Layout\Layout|array $layout
     * @throws \Zend\Log\Exception\InvalidArgumentException
     * @return \Zend\Log\Writer\Mail
     */
    public function setLayout($layout)
    {
        if (is_array($layout)) {
            $layout = $this->_constructLayoutFromConfig($layout);
        }

        if (!$layout instanceof \Zend\Layout\Layout) {
            require_once 'Zend/Log/Exception.php';
            throw new Log\Exception\InvalidArgumentException(
                'Mail must be an instance of \Zend\Layout\Layout or an array'
            );
        }
        $this->_layout = $layout;

        return $this;
    }

    /**
     * Construct a \Zend\Mail\Mail instance based on a configuration array
     *
     * @param array $config
     * @throws \Zend\Log\Exception\InvalidArgumentException
     * @return \Zend\Mail\Mail
     */
    protected static function _constructMailFromConfig(array $config)
    {
        $mailClass = 'Zend\Mail\Mail';
        if (isset($config['mail'])) {
            $mailClass = $config['mail'];
        }

        if (!array_key_exists('charset', $config)) {
            $config['charset'] = null;
        }
        $mail = new $mailClass($config['charset']);
        if (!$mail instanceof \Zend\Mail\Mail) {
            throw new Log\Exception\InvalidArgumentException($mail . 'must extend \Zend\Mail\Mail');
        }

        if (isset($config['subject'])) {
            $mail->setSubject($config['subject']);
        }

        $headerAddresses = array_intersect_key($config, self::$_methodMapHeaders);
        if (count($headerAddresses)) {
            foreach ($headerAddresses as $header => $address) {
                $method = self::$_methodMapHeaders[$header];
                if (is_array($address) && isset($address['name'])
                    && !is_numeric($address['name'])
                ) {
                    $params = array(
                        $address['email'],
                        $address['name']
                    );
                } else if (is_array($address) && isset($address['email'])) {
                    $params = array($address['email']);
                } else {
                    $params = array($address);
                }
                call_user_func_array(array($mail, $method), $params);
            }
        }

        return $mail;
    }

    /**
     * Construct a \Zend\Layout\Layout instance based on a configuration array
     *
     * @param array $config
     * @throws \Zend\Log\Exception\InvalidArgumentException
     * @return \Zend\Layout\Layout
     */
    protected function _constructLayoutFromConfig(array $config)
    {
        $config = array_merge(array(
            'layout' => 'Zend\Layout\Layout',
            'layoutOptions' => null
        ), $config);

        $layoutClass = $config['layout'];
        $layout = new $layoutClass($config['layoutOptions']);
        if (!$layout instanceof \Zend\Layout\Layout) {
            throw new Log\Exception\InvalidArgumentException(
                $layout . 'must extend \Zend\Layout\Layout'
            );
        }

        return $layout;
    }

    /**
     * Places event line into array of lines to be used as message body.
     *
     * Handles the formatting of both plaintext entries, as well as those
     * rendered with Zend_Layout.
     *
     * @param  array $event Event data
     * @return void
     */
    protected function _write($event)
    {
        // Track the number of entries per priority level.
        if (!isset($this->_numEntriesPerPriority[$event['priorityName']])) {
            $this->_numEntriesPerPriority[$event['priorityName']] = 1;
        } else {
            $this->_numEntriesPerPriority[$event['priorityName']]++;
        }

        $formattedEvent = $this->_formatter->format($event);

        // All plaintext events are to use the standard formatter.
        $this->_eventsToMail[] = $formattedEvent;

        // If we have a Zend_Layout instance, use a specific formatter for the
        // layout if one exists.  Otherwise, just use the event with its
        // default format.
        if ($this->_layout) {
            if ($this->_layoutFormatter) {
                $this->_layoutEventsToMail[] =
                    $this->_layoutFormatter->format($event);
            } else {
                $this->_layoutEventsToMail[] = $formattedEvent;
            }
        }
    }

    /**
     * Gets instance of Zend_Log_Formatter_Instance used for formatting a
     * message using Zend_Layout, if applicable.
     *
     * @return \Zend\Log\Formatter|null The formatter, or null.
     */
    public function getLayoutFormatter()
    {
        return $this->_layoutFormatter;
    }

    /**
     * Sets a specific formatter for use with Zend_Layout events.
     *
     * Allows use of a second formatter on lines that will be rendered with
     * Zend_Layout.  In the event that Zend_Layout is not being used, this
     * formatter cannot be set, so an exception will be thrown.
     *
     * @param  \Zend\Log\Formatter $formatter
     * @return \Zend\Log\Writer\Mail
     * @throws \Zend\Log\Exception\InvalidArgumentException
     */
    public function setLayoutFormatter(Log\Formatter $formatter)
    {
        if (!$this->_layout) {
            throw new Log\Exception\InvalidArgumentException(
                'cannot set formatter for layout; ' .
                    'a Zend\Layout\Layout instance is not in use');
        }

        $this->_layoutFormatter = $formatter;
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
     * @param  string $subject Subject prepend text.
     * @throws \Zend\Log\Exception\RuntimeException
     * @return \Zend\Log\Writer\Mail
     */
    public function setSubjectPrependText($subject)
    {
        if ($this->_mail->getSubject()) {
            throw new Log\Exception\RuntimeException(
                'subject already set on mail; ' .
                    'cannot set subject prepend text');
        }

        $this->_subjectPrependText = (string) $subject;
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
        if (empty($this->_eventsToMail)) {
            return;
        }

        if ($this->_subjectPrependText !== null) {
            // Tack on the summary of entries per-priority to the subject
            // line and set it on the Zend_Mail object.
            $numEntries = $this->_getFormattedNumEntriesPerPriority();
            $this->_mail->setSubject(
                "{$this->_subjectPrependText} ({$numEntries})");
        }


        // Always provide events to mail as plaintext.
        $this->_mail->setBodyText(implode('', $this->_eventsToMail));

        // If a Zend_Layout instance is being used, set its "events"
        // value to the lines formatted for use with the layout.
        if ($this->_layout) {
            // Set the required "messages" value for the layout.  Here we
            // are assuming that the layout is for use with HTML.
            $this->_layout->events =
                implode('', $this->_layoutEventsToMail);

            // If an exception occurs during rendering, convert it to a notice
            // so we can avoid an exception thrown without a stack frame.
            try {
                $this->_mail->setBodyHtml($this->_layout->render());
            } catch (\Exception $e) {
                trigger_error(
                    "exception occurred when rendering layout; " .
                        "unable to set html body for message; " .
                        "message = {$e->getMessage()}; " .
                        "code = {$e->getCode()}; " .
                        "exception class = " . get_class($e),
                    E_USER_NOTICE);
            }
        }

        // Finally, send the mail.  If an exception occurs, convert it into a
        // warning-level message so we can avoid an exception thrown without a
        // stack frame.
        try {
            $this->_mail->send();
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
     * an emptry string if none occurred.
     *
     * @return string
     */
    protected function _getFormattedNumEntriesPerPriority()
    {
        $strings = array();

        foreach ($this->_numEntriesPerPriority as $priority => $numEntries) {
            $strings[] = "{$priority}={$numEntries}";
        }

        return implode(', ', $strings);
    }
}
