<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Queue
 */

namespace Zend\Queue\Adapter;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use Zend\Queue\Adapter;
use Zend\Queue\Queue;
use Zend\Queue\Exception;

/**
 * Class for connecting to queues performing common operations.
 *
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage Adapter
 */
abstract class AbstractAdapter implements Adapter
{
    /**
     * Default timeout for createQueue() function
     */
    const CREATE_TIMEOUT_DEFAULT = 30;

    /**
     * Default timeout for receive() function
     */
    const RECEIVE_TIMEOUT_DEFAULT = 30;

    /**
     * User-provided options
     *
     * @var array
     */
    protected $_options = array();

    /**
     * Internal array of queues to save on lookups
     *
     * @var array
     */
    protected $_queues = array();

    /**
     * Contains the \Zend\Queue\Queue that this object
     *
     * @var \Zend\Queue\Queue
     */
    protected $_queue = null;

    /**
     * Constructor.
     *
     * $options is an array of key/value pairs or an instance of Zend_Config
     * containing configuration options.  These options are common to most adapters:
     *
     * See the Zend_Queue Adapter Notes documentation for example configurations.
     *
     * Some options are used on a case-by-case basis by adapters:
     *
     * access_key     => (string) Amazon AWS Access Key
     * secret_key     => (string) Amazon AWS Secret Key
     * dbname         => (string) The name of the database to user
     * username       => (string) Connect to the database as this username.
     * password       => (string) Password associated with the username.
     * host           => (string) What host to connect to, defaults to localhost
     * port           => (string) The port of the database
     *
     * @param  array|Traversable $options An array having configuration data
     * @param  \Zend\Queue\Queue The \Zend\Queue\Queue object that created this class
     * @return void
     * @throws \Zend\Queue\Exception
     */
    public function __construct($options, Queue $queue = null)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        /*
         * Verify that adapter parameters are in an array.
         */
        if (!is_array($options)) {
            throw new Exception\InvalidArgumentException('Adapter options must be an array or Zend_Config object');
        }

        // set the queue
        if ($queue !== null) {
            $this->setQueue($queue);
        }

        $adapterOptions = array();
        $driverOptions  = array();

        // Normalize the options and merge with the defaults
        if (array_key_exists('options', $options)) {
            if (!is_array($options['options'])) {
                throw new Exception\InvalidArgumentException("Configuration array 'options' must be an array");
            }

            // Can't use array_merge() because keys might be integers
            foreach ($options['options'] as $key => $value) {
                $adapterOptions[$key] = $value;
            }
        }
        if (array_key_exists('driverOptions', $options)) {
            // can't use array_merge() because keys might be integers
            foreach ((array)$options['driverOptions'] as $key => $value) {
                $driverOptions[$key] = $value;
            }
        }
        $this->_options = array_merge($this->_options, $options);
        $this->_options['options']       = $adapterOptions;
        $this->_options['driverOptions'] = $driverOptions;
    }

    /********************************************************************
    * Queue management functions
     *********************************************************************/
    /**
     * get the Zend_Queue class that is attached to this object
     *
     * @return \Zend\Queue\Queue|null
     */
    public function getQueue()
    {
        return $this->_queue;
    }

    /**
     * set the Zend_Queue class for this object
     *
     * @param  \Zend\Queue\Queue $queue
     * @return \Zend\Queue\Adapter
     */
    public function setQueue(Queue $queue)
    {
        $this->_queue = $queue;
        return $this;
    }

    /**
     * Returns the configuration options in this adapter.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Indicates if a function is supported or not.
     *
     * @param  string $name
     * @return boolean
     */
    public function isSupported($name)
    {
        $list = $this->getCapabilities();

        return (isset($list[$name]) && $list[$name]);
     }
}
