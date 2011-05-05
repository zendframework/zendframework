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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Mail\Transport;
use Zend\Config\Config,
    Zend\Mail\AbstractTransport;

/**
 * File transport
 *
 * Class for saving outgoing emails in filesystem
 *
 * @uses       \Zend\Mail\AbstractTransport
 * @uses       \Zend\Mail\Transport\Exception
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Transport
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class File extends AbstractTransport
{
    /**
     * Target directory for saving sent email messages
     *
     * @var string
     */
    protected $path;

    /**
     * Callback function generating a file name
     *
     * @var string|array|Closure
     */
    protected $callback;

    /**
     * Constructor
     *
     * @param  array|\Zend\Config\Config $options OPTIONAL (Default: null)
     * @return void
     */
    public function __construct($options = null)
    {
        if ($options instanceof Config) {
            $options = $options->toArray();
        } elseif (!is_array($options)) {
            $options = array();
        }

        // Making sure we have some defaults to work with
        if (!isset($options['path'])) {
            $options['path'] = sys_get_temp_dir();
        }
        if (!isset($options['callback'])) {
            $options['callback'] = $this->getDefaultCallback();
        }

        $this->setOptions($options);
    }

    /**
     * Sets options
     *
     * @param  array $options
     * @return void
     */
    public function setOptions(array $options)
    {
        if (isset($options['path'])) {
            $this->path = $options['path'];
        }
        if (isset($options['callback'])) {
            $this->callback = $options['callback'];
        }
    }

    /**
     * Saves e-mail message to a file
     *
     * @return void
     * @throws \Zend\Mail\Transport\Exception on not writable target directory
     * @throws \Zend\Mail\Transport\Exception on file_put_contents() failure
     */
    protected function _sendMail()
    {
        $file = $this->getPath() . DIRECTORY_SEPARATOR . call_user_func($this->getCallback(), $this);

        if (!is_writable(dirname($file))) {
            throw new Exception\RuntimeException(sprintf(
                'Target directory "%s" does not exist or is not writable',
                dirname($file)
            ));
        }

        $email = $this->header . $this->EOL . $this->body;

        if (!file_put_contents($file, $email)) {
            throw new Exception\RuntimeException('Unable to send mail');
        }
    }

    /**
     * Returns the default callback for generating file names
     *
     * @return callback
     */
    public function getDefaultCallback()
    {
        return function($transport) {
            return 'ZendMail_' . time() . '_' . mt_rand() . '.tmp';
        };
    }

    /**
     * Retrieve registered path
     * 
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get the registered callback for generating file names
     * 
     * @return callback
     */
    public function getCallback()
    {
        return $this->callback;
    }
}
