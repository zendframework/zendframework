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
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Mail\Storage;

use Zend\Mail\AbstractStorage,
    Zend\Mail\Storage,
    Zend\Mail\Storage\Exception,
    Zend\Mail\Protocol;

/**
 * @uses       \Zend\Mail\Message\Message
 * @uses       \Zend\Mail\Protocol\Imap
 * @uses       \Zend\Mail\Storage
 * @uses       \Zend\Mail\AbstractStorage
 * @uses       \Zend\Mail\Storage\Exception
 * @uses       \Zend\Mail\Storage\Folder
 * @uses       \Zend\Mail\Storage\MailFolder
 * @uses       \Zend\Mail\Storage\Writable
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Imap extends AbstractStorage implements MailFolder, Writable
{
    // TODO: with an internal cache we could optimize this class, or create an extra class with
    // such optimizations. Especially the various fetch calls could be combined to one cache call

    /**
     * protocol handler
     * @var null|\Zend\Mail\Protocol\Imap
     */
    protected $_protocol;

    /**
     * name of current folder
     * @var string
     */
    protected $_currentFolder = '';

    /**
     * imap flags to constants translation
     * @var array
     */
    protected static $_knownFlags = array('\Passed'   => Storage::FLAG_PASSED,
                                          '\Answered' => Storage::FLAG_ANSWERED,
                                          '\Seen'     => Storage::FLAG_SEEN,
                                          '\Deleted'  => Storage::FLAG_DELETED,
                                          '\Draft'    => Storage::FLAG_DRAFT,
                                          '\Flagged'  => Storage::FLAG_FLAGGED);

    /**
     * map flags to search criterias
     * @var array
     */
    protected static $_searchFlags = array('\Recent'   => 'RECENT',
                                           '\Answered' => 'ANSWERED',
                                           '\Seen'     => 'SEEN',
                                           '\Deleted'  => 'DELETED',
                                           '\Draft'    => 'DRAFT',
                                           '\Flagged'  => 'FLAGGED');

    /**
     * Count messages all messages in current box
     *
     * @return int number of messages
     * @throws \Zend\Mail\Storage\Exception
     * @throws \Zend\Mail\Protocol\Exception
     */
    public function countMessages($flags = null)
    {
        if (!$this->_currentFolder) {
            throw new Exception\RuntimeException('No selected folder to count');
        }

        if ($flags === null) {
            return count($this->_protocol->search(array('ALL')));
        }

        $params = array();
        foreach ((array)$flags as $flag) {
            if (isset(self::$_searchFlags[$flag])) {
                $params[] = self::$_searchFlags[$flag];
            } else {
                $params[] = 'KEYWORD';
                $params[] = $this->_protocol->escapeString($flag);
            }
        }
        return count($this->_protocol->search($params));
    }

    /**
     * get a list of messages with number and size
     *
     * @param int $id number of message
     * @return int|array size of given message of list with all messages as array(num => size)
     * @throws \Zend\Mail\Protocol\Exception
     */
    public function getSize($id = 0)
    {
        if ($id) {
            return $this->_protocol->fetch('RFC822.SIZE', $id);
        }
        return $this->_protocol->fetch('RFC822.SIZE', 1, INF);
    }

    /**
     * Fetch a message
     *
     * @param int $id number of message
     * @return \Zend\Mail\Message\Message
     * @throws \Zend\Mail\Protocol\Exception
     */
    public function getMessage($id)
    {
        $data = $this->_protocol->fetch(array('FLAGS', 'RFC822.HEADER'), $id);
        $header = $data['RFC822.HEADER'];

        $flags = array();
        foreach ($data['FLAGS'] as $flag) {
            $flags[] = isset(self::$_knownFlags[$flag]) ? self::$_knownFlags[$flag] : $flag;
        }

        return new $this->_messageClass(array('handler' => $this, 'id' => $id, 'headers' => $header, 'flags' => $flags));
    }

    /*
     * Get raw header of message or part
     *
     * @param  int               $id       number of message
     * @param  null|array|string $part     path to part or null for messsage header
     * @param  int               $topLines include this many lines with header (after an empty line)
     * @param  int $topLines include this many lines with header (after an empty line)
     * @return string raw header
     * @throws \Zend\Mail\Protocol\Exception
     * @throws \Zend\Mail\Storage\Exception
     */
    public function getRawHeader($id, $part = null, $topLines = 0)
    {
        if ($part !== null) {
            // TODO: implement
            throw new Exception\RuntimeException('not implemented');
        }

        // TODO: toplines
        return $this->_protocol->fetch('RFC822.HEADER', $id);
    }

    /*
     * Get raw content of message or part
     *
     * @param  int               $id   number of message
     * @param  null|array|string $part path to part or null for messsage content
     * @return string raw content
     * @throws \Zend\Mail\Protocol\Exception
     * @throws \Zend\Mail\Storage\Exception
     */
    public function getRawContent($id, $part = null)
    {
        if ($part !== null) {
            // TODO: implement
            throw new Exception\RuntimeException('not implemented');
        }

        return $this->_protocol->fetch('RFC822.TEXT', $id);
    }

    /**
     * create instance with parameters
     * Supported paramters are
     *   - user username
     *   - host hostname or ip address of IMAP server [optional, default = 'localhost']
     *   - password password for user 'username' [optional, default = '']
     *   - port port for IMAP server [optional, default = 110]
     *   - ssl 'SSL' or 'TLS' for secure sockets
     *   - folder select this folder [optional, default = 'INBOX']
     *
     * @param  array $params mail reader specific parameters
     * @throws \Zend\Mail\Storage\Exception
     * @throws \Zend\Mail\Protocol\Exception
     */
    public function __construct($params)
    {
        if (is_array($params)) {
            $params = (object)$params;
        }

        $this->_has['flags'] = true;

        if ($params instanceof Protocol\Imap) {
            $this->_protocol = $params;
            try {
                $this->selectFolder('INBOX');
            } catch(Exception $e) {
                throw new Exception\RuntimeException('cannot select INBOX, is this a valid transport?', 0, $e);
            }
            return;
        }

        if (!isset($params->user)) {
            throw new Exception\InvalidArgumentException('need at least user in params');
        }

        $host     = isset($params->host)     ? $params->host     : 'localhost';
        $password = isset($params->password) ? $params->password : '';
        $port     = isset($params->port)     ? $params->port     : null;
        $ssl      = isset($params->ssl)      ? $params->ssl      : false;

        $this->_protocol = new Protocol\Imap();
        $this->_protocol->connect($host, $port, $ssl);
        if (!$this->_protocol->login($params->user, $password)) {
            throw new Exception\RuntimeException('cannot login, user or password wrong');
        }
        $this->selectFolder(isset($params->folder) ? $params->folder : 'INBOX');
    }

    /**
     * Close resource for mail lib. If you need to control, when the resource
     * is closed. Otherwise the destructor would call this.
     *
     * @return null
     */
    public function close()
    {
        $this->_currentFolder = '';
        $this->_protocol->logout();
    }

    /**
     * Keep the server busy.
     *
     * @return null
     * @throws \Zend\Mail\Storage\Exception
     */
    public function noop()
    {
        if (!$this->_protocol->noop()) {
            throw new Exception\RuntimeException('could not do nothing');
        }
    }

    /**
     * Remove a message from server. If you're doing that from a web enviroment
     * you should be careful and use a uniqueid as parameter if possible to
     * identify the message.
     *
     * @param   int $id number of message
     * @return  null
     * @throws  \Zend\Mail\Storage\Exception
     */
    public function removeMessage($id)
    {
        if (!$this->_protocol->store(array(Storage::FLAG_DELETED), $id, null, '+')) {
            throw new Exception\RuntimeException('cannot set deleted flag');
        }
        // TODO: expunge here or at close? we can handle an error here better and are more fail safe
        if (!$this->_protocol->expunge()) {
            throw new Exception\RuntimeException('message marked as deleted, but could not expunge');
        }
    }

    /**
     * get unique id for one or all messages
     *
     * if storage does not support unique ids it's the same as the message number
     *
     * @param int|null $id message number
     * @return array|string message number for given message or all messages as array
     * @throws \Zend\Mail\Storage\Exception
     */
    public function getUniqueId($id = null)
    {
        if ($id) {
            return $this->_protocol->fetch('UID', $id);
        }

        return $this->_protocol->fetch('UID', 1, INF);
    }

    /**
     * get a message number from a unique id
     *
     * I.e. if you have a webmailer that supports deleting messages you should use unique ids
     * as parameter and use this method to translate it to message number right before calling removeMessage()
     *
     * @param string $id unique id
     * @return int message number
     * @throws \Zend\Mail\Storage\Exception
     */
    public function getNumberByUniqueId($id)
    {
        // TODO: use search to find number directly
        $ids = $this->getUniqueId();
        foreach ($ids as $k => $v) {
            if ($v == $id) {
                return $k;
            }
        }

        throw new Exception\InvalidArgumentException('unique id not found');
    }


    /**
     * get root folder or given folder
     *
     * @param  string $rootFolder get folder structure for given folder, else root
     * @return \Zend\Mail\Storage\Folder root or wanted folder
     * @throws \Zend\Mail\Storage\Exception
     * @throws \Zend\Mail\Protocol\Exception
     */
    public function getFolders($rootFolder = null)
    {
        $folders = $this->_protocol->listMailbox((string)$rootFolder);
        if (!$folders) {
            throw new Exception\InvalidArgumentException('folder not found');
        }

        ksort($folders, SORT_STRING);
        $root = new Folder('/', '/', false);
        $stack = array(null);
        $folderStack = array(null);
        $parentFolder = $root;
        $parent = '';

        foreach ($folders as $globalName => $data) {
            do {
                if (!$parent || strpos($globalName, $parent) === 0) {
                    $pos = strrpos($globalName, $data['delim']);
                    if ($pos === false) {
                        $localName = $globalName;
                    } else {
                        $localName = substr($globalName, $pos + 1);
                    }
                    $selectable = !$data['flags'] || !in_array('\\Noselect', $data['flags']);

                    array_push($stack, $parent);
                    $parent = $globalName . $data['delim'];
                    $folder = new Folder($localName, $globalName, $selectable);
                    $parentFolder->$localName = $folder;
                    array_push($folderStack, $parentFolder);
                    $parentFolder = $folder;
                    break;
                } else if ($stack) {
                    $parent = array_pop($stack);
                    $parentFolder = array_pop($folderStack);
                }
            } while ($stack);
            if (!$stack) {
                throw new Exception\RuntimeException('error while constructing folder tree');
            }
        }

        return $root;
    }

    /**
     * select given folder
     *
     * folder must be selectable!
     *
     * @param  \Zend\Mail\Storage\Folder|string $globalName global name of folder or instance for subfolder
     * @return null
     * @throws \Zend\Mail\Storage\Exception
     * @throws \Zend\Mail\Protocol\Exception
     */
    public function selectFolder($globalName)
    {
        $this->_currentFolder = $globalName;
        if (!$this->_protocol->select($this->_currentFolder)) {
            $this->_currentFolder = '';
            throw new Exception\RuntimeException('cannot change folder, maybe it does not exist');
        }
    }


    /**
     * get \Zend\Mail\Storage\Folder instance for current folder
     *
     * @return \Zend\Mail\Storage\Folder instance of current folder
     * @throws \Zend\Mail\Storage\Exception
     */
    public function getCurrentFolder()
    {
        return $this->_currentFolder;
    }

    /**
     * create a new folder
     *
     * This method also creates parent folders if necessary. Some mail storages may restrict, which folder
     * may be used as parent or which chars may be used in the folder name
     *
     * @param  string                          $name         global name of folder, local name if $parentFolder is set
     * @param  string|\Zend\Mail\Storage\Folder $parentFolder parent folder for new folder, else root folder is parent
     * @return null
     * @throws \Zend\Mail\Storage\Exception
     */
    public function createFolder($name, $parentFolder = null)
    {
        // TODO: we assume / as the hierarchy delim - need to get that from the folder class!
        if ($parentFolder instanceof Folder) {
            $folder = $parentFolder->getGlobalName() . '/' . $name;
        } else if ($parentFolder != null) {
            $folder = $parentFolder . '/' . $name;
        } else {
            $folder = $name;
        }

        if (!$this->_protocol->create($folder)) {
            throw new Exception\RuntimeException('cannot create folder');
        }
    }

    /**
     * remove a folder
     *
     * @param  string|\Zend\Mail\Storage\Folder $name      name or instance of folder
     * @return null
     * @throws \Zend\Mail\Storage\Exception
     */
    public function removeFolder($name)
    {
        if ($name instanceof Folder) {
            $name = $name->getGlobalName();
        }

        if (!$this->_protocol->delete($name)) {
            throw new Exception\RuntimeException('cannot delete folder');
        }
    }

    /**
     * rename and/or move folder
     *
     * The new name has the same restrictions as in createFolder()
     *
     * @param  string|\Zend\Mail\Storage\Folder $oldName name or instance of folder
     * @param  string                          $newName new global name of folder
     * @return null
     * @throws \Zend\Mail\Storage\Exception
     */
    public function renameFolder($oldName, $newName)
    {
        if ($oldName instanceof Folder) {
            $oldName = $oldName->getGlobalName();
        }

        if (!$this->_protocol->rename($oldName, $newName)) {
            throw new Exception\RuntimeException('cannot rename folder');
        }
    }

    /**
     * append a new message to mail storage
     *
     * @param  string                                     $message message as string or instance of message class
     * @param  null|string|\Zend\Mail\Storage\Folder       $folder  folder for new message, else current folder is taken
     * @param  null|array                                 $flags   set flags for new message, else a default set is used
     * @throws \Zend\Mail\Storage\Exception
     */
     // not yet * @param string|\Zend\Mail\Message|\Zend\Mime\Message $message message as string or instance of message class
    public function appendMessage($message, $folder = null, $flags = null)
    {
        if ($folder === null) {
            $folder = $this->_currentFolder;
        }

        if ($flags === null) {
            $flags = array(Storage::FLAG_SEEN);
        }

        // TODO: handle class instances for $message
        if (!$this->_protocol->append($folder, $message, $flags)) {
            throw new Exception\RuntimeException('cannot create message, please check if the folder exists and your flags');
        }
    }

    /**
     * copy an existing message
     *
     * @param  int                             $id     number of message
     * @param  string|\Zend\Mail\Storage\Folder $folder name or instance of targer folder
     * @return null
     * @throws \Zend\Mail\Storage\Exception
     */
    public function copyMessage($id, $folder)
    {
        if (!$this->_protocol->copy($folder, $id)) {
            throw new Exception\RuntimeException('cannot copy message, does the folder exist?');
        }
    }

    /**
     * move an existing message
     *
     * NOTE: imap has no native move command, thus it's emulated with copy and delete
     *
     * @param  int                             $id     number of message
     * @param  string|\Zend\Mail\Storage\Folder $folder name or instance of targer folder
     * @return null
     * @throws \Zend\Mail\Storage\Exception
     */
    public function moveMessage($id, $folder) {
        $this->copyMessage($id, $folder);
        $this->removeMessage($id);
    }

    /**
     * set flags for message
     *
     * NOTE: this method can't set the recent flag.
     *
     * @param  int   $id    number of message
     * @param  array $flags new flags for message
     * @throws \Zend\Mail\Storage\Exception
     */
    public function setFlags($id, $flags)
    {
        if (!$this->_protocol->store($flags, $id)) {
            throw new Exception\RuntimeException('cannot set flags, have you tried to set the recent flag or special chars?');
        }
    }
}

