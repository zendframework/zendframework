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
 * @subpackage Demos
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';

/**
 * A simple web-mailer based on Zend_Mail_Storage classes.
 *
 * This simple mailer demonstrates the most important features of the mail reading classes. You can
 * use the test mbox and maildir files or a Pop3 or Imap server. It's meant to be run in a web enviroment
 * and CLI is not supported. Copy the files to a directory in your webroot and make sure Zend Framework
 * is in your include path (including incubator!).
 *
 * SSL and TLS are supported by Zend_Mail_Storage_[Pop3|Imap], but not shown here). You'd need to add
 *   'ssl' => 'SSL'
 * or
 *   'ssl' => 'TLS'
 * if you want to use ssl support.
 *
 * Because of problems with Windows filenames (maildir needs : in filenames) the maildir folder is in a tar.
 * Untar maildir.tar in maildir/ to test maildir support (won't work on Windows).
 *
 * The structure of the class is very simple. Every method named show...() output HTML, run() inits mail storage
 * after login and calls a show method, everything else inits and checks variables and mail storage handler.
 *
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Demos
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Demo_Zend_Mail_SimpleMailer
{
    /**
     * Mail storage type (mbox, mbox-folder, maildir, maildir-folder, pop3, imap)
     *
     * @var string
     */
    private $type;

    /**
     * Filename, dirname or hostname for current mailstorage
     *
     * @var string
     */
    private $param;

    /**
     * Selected mail message or null if none
     *
     * @var integer
     */
    private $messageNum;

    /**
     * Mail storage handler
     *
     * @var Zend_Mail_Storage
     */
    private $mail;

    /**
     * Query string with current selection for output
     *
     * @var string
     */
    private $queryString;

   /**
     * Don't run run(), needed for auth
     *
     * @var boolean
     */
    private $noRun = false;

    /**
     * Init class for run() and output
     *
     * @return void
     */
    function __construct()
    {
        $this->initVars();
        $this->loadClasses();
        $this->whitelistParam();

        // we use http auth for username and password or mail storage
        if (($this->type == 'pop3' || $this->type == 'imap') && !isset($_SERVER['PHP_AUTH_USER'])) {
            $this->needAuth();
            return;
        }

        switch ($this->type) {
            case 'mbox':
                $this->mail = new Zend_Mail_Storage_Mbox(array('filename' => $this->param));
                break;
            case 'mbox-folder':
                $this->mail = new Zend_Mail_Storage_Folder_Mbox(array('dirname' => $this->param));
                break;
            case 'maildir':
                $this->mail = new Zend_Mail_Storage_Maildir(array('dirname' => $this->param));
                break;
            case 'maildir-folder':
                $this->mail = new Zend_Mail_Storage_Folder_Maildir(array('dirname' => $this->param));
                break;
            case 'pop3':
                $this->mail = new Zend_Mail_Storage_Pop3(array('host'     => $this->param,
                                                               'user'     => $_SERVER['PHP_AUTH_USER'],
                                                               'password' => $_SERVER['PHP_AUTH_PW']));
                break;
            case 'imap':
                $this->mail = new Zend_Mail_Storage_Imap(array('host'     => $this->param,
                                                               'user'     => $_SERVER['PHP_AUTH_USER'],
                                                               'password' => $_SERVER['PHP_AUTH_PW']));
                break;
            default:
                $this->mail = null;
                break;
        }
    }

    /**
     * Check parameter and type
     *
     * @return void
     */
    function whitelistParam()
    {
        $whitelist = array('mbox'           => array('mbox/INBOX', 'mbox/subfolder/test'),
                           'mbox-folder'    => array('mbox'),
                           'maildir'        => array('maildir', 'maildir/.subfolder', 'maildir/.subfolder.test'),
                           'maildir-folder' => array('maildir', 'maildir/.subfolder', 'maildir/.subfolder.test'),
                           'pop3'           => array(),
                           'imap'           => array());

        if ($this->type === null || @$whitelist[$this->type] === array() || @in_array($this->param, $whitelist[$this->type])) {
            return;
        }

        throw new Exception('Unknown type or param not in whitelist');
    }

    /**
     * Load needed classes
     *
     * @return void
     */
    function loadClasses()
    {
        $classname = array('mbox'           => 'Zend_Mail_Storage_Mbox',
                           'mbox-folder'    => 'Zend_Mail_Storage_Folder_Mbox',
                           'maildir'        => 'Zend_Mail_Storage_Maildir',
                           'maildir-folder' => 'Zend_Mail_Storage_Folder_Maildir',
                           'pop3'           => 'Zend_Mail_Storage_Pop3',
                           'imap'           => 'Zend_Mail_Storage_Imap');

        if (isset($classname[$this->type])) {
            Zend_Loader::loadClass($classname[$this->type]);
        }

        Zend_Loader::loadClass('Zend_Mail_Storage');
    }

    /**
     * Init variables
     *
     * @return void
     */
    function initVars()
    {
        $this->type        = isset($_GET['type'])   ? $_GET['type']   : null;
        $this->param       = isset($_GET['param'])  ? $_GET['param']  : null;
        $this->folder      = isset($_GET['folder']) ? $_GET['folder'] : null;
        $this->messageNum  = isset($_GET['message']) && is_numeric($_GET['message']) ? $_GET['message'] : null;
        $this->queryString = http_build_query(array('type'   => $this->type,
                                                    'param'  => $this->param,
                                                    'folder' => $this->folder));
    }

    /**
     * Send http auth headers, for username and password in pop3 and imap
     *
     * @return void
     */
    function needAuth()
    {
        header("WWW-Authenticate: Basic realm='{$this->type} credentials'");
        header('HTTP/1.0 401 Please enter credentials');
        $this->noRun = true;
    }

    /**
     * Get data from mail storage and output html
     *
     * @return void
     */
    function run()
    {
        if ($this->noRun) {
            return;
        }

        if ($this->mail instanceof Zend_Mail_Storage_Folder_Interface && $this->folder) {
            // could also be done in constructor of $this->mail with parameter 'folder' => '...'
            $this->mail->selectFolder($this->folder);
        }

        $message = null;
        try {
            if ($this->messageNum) {
                $message = $this->mail->getMessage($this->messageNum);
            }
        } catch(Zend_Mail_Exception $e) {
            // ignored, $message is still null and we display the list
        }

        if (!$this->mail) {
            $this->showChooseType();
        } else if ($message) {
            $this->showMessage($message);
        } else {
            $this->showList();
        }
    }

    /**
     * Output html header
     *
     * @param  string $title page title
     * @return void
     */
    function showHeader($title)
    {
        echo "<html><head>
              <title>{$title}</title>
              <style>
              table {border: 1px solid black; border-collapse: collapse}
              td, th {border: 1px solid black; padding: 3px; text-align: left}
              th {text-align: right; background: #eee}
              tr.unread td {font-weight: bold}
              tr.flagged td {font-style: italic}
              tr.new td {color: #800}
              .message {white-space: pre; font-family: monospace; padding: 0.5em}
              dl dt {font-style: italic; padding: 1em 0; border-top: 1px #888 dashed}
              dl dd {padding-bottom: 1em}
              dl dt:first-child {border: none; padding-top: 0}
              </style>
              </head><body><h1>{$title}</h1>";
    }

    /**
     * Output html footer
     *
     * @return void
     */
    function showFooter()
    {
        echo '</body></html>';
    }

    /**
     * Output type selection AKA "login-form"
     *
     * @return void
     */
    function showChooseType()
    {
        $this->showHeader('Choose Type');

        echo '<form><label>Mbox file</label><input name="param" value="mbox/INBOX"/>
              <input type="hidden" name="type" value="mbox"/><input type="submit"/></form>

              <form><label>Mbox folder</label><input name="param" value="mbox"/>
              <input type="hidden" name="type" value="mbox-folder"/><input type="submit"/></form>

              <form><label>Maildir file</label><input name="param" value="maildir"/>
              <input type="hidden" name="type" value="maildir"/><input type="submit"/></form>

              <form><label>Maildir folder</label><input name="param" value="maildir"/>
              <input type="hidden" name="type" value="maildir-folder"/><input type="submit"/></form>

              <form><label>Pop3 Host</label><input name="param" value="localhost"/>
              <input type="hidden" name="type" value="pop3"/><input type="submit"/></form>

              <form><label>IMAP Host</label><input name="param" value="localhost"/>
              <input type="hidden" name="type" value="imap"/><input type="submit"/></form>';

        $this->showFooter();
    }

    /**
     * Output mail message
     *
     * @return void
     */
    function showMessage($message)
    {
        try {
            $from = $message->from;
        } catch(Zend_Mail_Exception $e) {
            $from = '(unknown)';
        }

        try {
            $to = $message->to;
        } catch(Zend_Mail_Exception $e) {
            $to = '(unknown)';
        }

        try {
            $subject = $message->subject;
        } catch(Zend_Mail_Exception $e) {
            $subject = '(unknown)';
        }

        $this->showHeader($subject);

        echo "<table>
              <tr><th>From:</td><td>$from</td></tr>
              <tr><th>Subject:</td><td>$subject</td></tr>
              <tr><th>To:</td><td>$to</td></tr><tr><td colspan='2' class='message'>";

        if ($message->isMultipart()) {
            echo '<dl>';
            foreach (new RecursiveIteratorIterator($message) as $part) {
                echo "<dt>Part with type {$part->contentType}:</dt><dd>";
                echo htmlentities($part);
                echo '</dd>';
            }
            echo '</dl>';
        } else {
            echo htmlentities($message->getContent());
        }

        echo "</td></tr></table><a href='?{$this->queryString}'>back to list</a>";

        if ($this->messageNum > 1) {
            echo " - <a href=\"?{$this->queryString}&message=", $this->messageNum - 1, '">prev</a>';
        }

        if ($this->messageNum < $this->mail->countMessages()) {
            echo " - <a href=\"?{$this->queryString}&message=", $this->messageNum + 1, '">next</a>';
        }

        $this->showFooter();
    }

    /**
     * Output message list
     *
     * @return void
     */
    function showList()
    {
        $this->showHeader('Overview');

        echo '<table><tr><td></td><th>From</th><th>To</th><th>Subject</th></tr>';

        foreach ($this->mail as $num => $message) {
            if ($this->mail->hasFlags) {
                $class = array();

                if ($message->hasFlag(Zend_Mail_Storage::FLAG_RECENT)) {
                    $class['unread'] = 'unread';
                    $class['new']    = 'new';
                }
                if (!$message->hasFlag(Zend_Mail_Storage::FLAG_SEEN)) {
                    $class['unread'] = 'unread';
                }
                if ($message->hasFlag(Zend_Mail_Storage::FLAG_FLAGGED)) {
                    $class['flagged'] = 'flagged';
                }

                $class = implode(' ', $class);
                echo "<tr class='$class'>";
            } else {
                echo '<tr>';
            }

            echo "<td><a href='?{$this->queryString}&message=$num'>read</a></td>";

            try {
                echo "<td>{$message->from}</td><td>{$message->to}</td><td>{$message->subject}</td>";
            } catch(Zend_Mail_Exception $e){
                echo '<td><em>error</em></td>';
            }

            echo '</tr>';
        }

        echo '</table>';

        if ($this->mail instanceof Zend_Mail_Storage_Folder_Interface) {
            $this->showFolders();
        }

        $this->showFooter();
    }

    /**
     * Output folder list
     *
     * @return void
     */
    function showFolders()
    {
        echo "<br><form method='get' action='?{$this->queryString}'><label>Change folder:</label>
              <select name='folder'>";

        $folders = new RecursiveIteratorIterator($this->mail->getFolders(), RecursiveIteratorIterator::SELF_FIRST);

        foreach ($folders as $localName => $folder) {
            echo '<option ';
            if (!$folder->isSelectable()) {
                echo 'disabled="disabled" ';
            }
            $localName = str_pad('', $folders->getDepth() * 12, '&nbsp;', STR_PAD_LEFT) . $localName;
            echo "value='$folder'>$localName</option>";
        }

        echo "</select><input type='submit' value='change'><input type='hidden' name='param' value='{$this->param}'>
              <input type='hidden' name='type' value='{$this->type}'></form>";
    }
}

// init and run mailer
$SimpleMailer = new Demo_Zend_Mail_SimpleMailer();
$SimpleMailer->run();
