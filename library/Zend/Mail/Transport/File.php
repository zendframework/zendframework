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
 * @subpackage Transport
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Mail\Transport;

use Zend\Mail\Message,
    Zend\Mail\Transport;

/**
 * File transport
 *
 * Class for saving outgoing emails in filesystem
 *
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Transport
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class File implements Transport
{
    /**
     * @var FileOptions
     */
    protected $options;

    /**
     * Last file written to
     * 
     * @var string
     */
    protected $lastFile;

    /**
     * Constructor
     *
     * @param  null|FileOptions $options OPTIONAL (Default: null)
     * @return void
     */
    public function __construct(FileOptions $options = null)
    {
        if (!$options instanceof FileOptions) {
            $options = new FileOptions();
        }
        $this->setOptions($options);
    }

    /**
     * Sets options
     *
     * @param  FileOptions $options
     * @return void
     */
    public function setOptions(FileOptions $options)
    {
        $this->options = $options;
    }

    /**
     * Saves e-mail message to a file
     *
     * @return void
     * @throws \Zend\Mail\Transport\Exception on not writable target directory
     * @throws \Zend\Mail\Transport\Exception on file_put_contents() failure
     */
    public function send(Message $message)
    {
        $options  = $this->options;
        $filename = call_user_func($options->getCallback(), $this);
        $file     = $options->getPath() . DIRECTORY_SEPARATOR . $filename;
        $email    = $message->toString();

        if (false === file_put_contents($file, $email)) {
            throw new Exception\RuntimeException(sprintf(
                'Unable to write mail to file (directory "%s")',
                $options->getPath()
            ));
        }

        $this->lastFile = $file;
    }

    /**
     * Get the name of the last file written to
     * 
     * @return null|string
     */
    public function getLastFile()
    {
        return $this->lastFile;
    }
}
