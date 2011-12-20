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
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cache\Storage\Adapter;

use Memcached as MemcachedResource,
    Zend\Cache\Exception;

/**
 * These are options specific to the APC adapter
 *
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class MemcachedOptions extends AdapterOptions
{
    /**
     * Map of option keys to \Memcached options
     * 
     * @var array
     */
    private $optionsMap = array(
        'binary_protocol'      => MemcachedResource::OPT_BINARY_PROTOCOL,
        'buffer_writes'        => MemcachedResource::OPT_BUFFER_WRITES,
        'cache_lookups'        => MemcachedResource::OPT_CACHE_LOOKUPS,
        'compression'          => MemcachedResource::OPT_COMPRESSION,
        'connect_timeout'      => MemcachedResource::OPT_CONNECT_TIMEOUT,
        'distribution'         => MemcachedResource::OPT_DISTRIBUTION,
        'hash'                 => MemcachedResource::OPT_HASH,
        'libketama_compatible' => MemcachedResource::OPT_LIBKETAMA_COMPATIBLE,
        'no_block'             => MemcachedResource::OPT_NO_BLOCK,
        'poll_timeout'         => MemcachedResource::OPT_POLL_TIMEOUT,
        'prefix_key'           => MemcachedResource::OPT_PREFIX_KEY,
        'recv_timeout'         => MemcachedResource::OPT_RECV_TIMEOUT,
        'retry_timeout'        => MemcachedResource::OPT_RETRY_TIMEOUT,
        'send_timeout'         => MemcachedResource::OPT_SEND_TIMEOUT,
        'serializer'           => MemcachedResource::OPT_SERIALIZER,
        'server_failure_limit' => MemcachedResource::OPT_SERVER_FAILURE_LIMIT,
        'socket_recv_size'     => MemcachedResource::OPT_SOCKET_RECV_SIZE,
        'socket_send_size'     => MemcachedResource::OPT_SOCKET_SEND_SIZE,
        'tcp_nodelay'          => MemcachedResource::OPT_TCP_NODELAY,
    );

    /**
     * Memcached server address
     * 
     * @var string 
     */
    protected $server = 'localhost';
    
    /**
     * Memcached port
     * 
     * @var integer
     */
    protected $port = 11211;
    
    /**
     * Whether or not to enable binary protocol for communication with server
     * 
     * @var bool
     */
    protected $binaryProtocol = false;

    /**
     * Enable or disable buffered I/O
     * 
     * @var bool
     */
    protected $bufferWrites = false;

    /**
     * Whether or not to cache DNS lookups
     * 
     * @var bool
     */
    protected $cacheLookups = false;

    /**
     * Whether or not to use compression
     * 
     * @var bool
     */
    protected $compression = true;

    /**
     * Time at which to issue connection timeout, in ms
     * 
     * @var int
     */
    protected $connectTimeout = 1000;

    /**
     * Server distribution algorithm
     * 
     * @var int
     */
    protected $distribution = MemcachedResource::DISTRIBUTION_MODULA;

    /**
     * Hashing algorithm to use
     * 
     * @var int
     */
    protected $hash = MemcachedResource::HASH_DEFAULT;

    /**
     * Whether or not to enable compatibility with libketama-like behavior.
     * 
     * @var bool
     */
    protected $libketamaCompatible = false;

    /**
     * Namespace separator
     *
     * @var string
     */
    protected $namespaceSeparator = ':';

    /**
     * Whether or not to enable asynchronous I/O
     * 
     * @var bool
     */
    protected $noBlock = false;

    /**
     * Timeout for connection polling, in ms
     * 
     * @var int
     */
    protected $pollTimeout = 0;

    /**
     * Prefix to use with keys 
     * 
     * @var string
     */
    protected $prefixKey = '';

    /**
     * Maximum allowed time for a recv operation, in ms
     * 
     * @var int
     */
    protected $recvTimeout = 0;

    /**
     * Time to wait before retrying a connection, in seconds
     * 
     * @var int
     */
    protected $retryTimeout = 0;

    /**
     * Maximum allowed time for a send operation, in ms
     * 
     * @var int
     */
    protected $sendTimeout = 0;

    /**
     * Serializer to use
     * 
     * @var int
     */
    protected $serializer = MemcachedResource::SERIALIZER_PHP;

    /**
     * Maximum number of server connection errors
     * 
     * @var int
     */
    protected $serverFailureLimit = 0;

    /**
     * Maximum socket send buffer in bytes
     * 
     * @var int
     */
    protected $socketSendSize;

    /**
     * Maximum socket recv buffer in bytes
     * 
     * @var int
     */
    protected $socketRecvSize;

    /**
     * Whether or not to enable no-delay feature for connecting sockets
     * 
     * @var bool
     */
    protected $tcpNodelay = false;

    public function setServer($server)
    {
        $this->server= $server;
        return $this;
    }
    
    public function getServer()
    {
        return $this->server;
    }
    
    public function setPort($port)
    {
        if ((!is_int($port) && !is_numeric($port)) 
            || 0 > $port
        ) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a positive integer',
                __METHOD__
            ));
        }
        
        $this->port= $port;
        return $this;
    }
    
    public function getPort()
    {
        return $this->port;
    }
    
    /**
     * Set flag indicating whether or not to enable binary protocol for 
     * communication with server
     *
     * @param  bool $binaryProtocol
     * @return MemcachedOptions
     */
    public function setBinaryProtocol($binaryProtocol)
    {
        $this->binaryProtocol = (bool) $binaryProtocol;
        return $this;
    }
    
    /**
     * Whether or not to enable binary protocol for communication with server
     *
     * @return bool
     */
    public function getBinaryProtocol()
    {
        return $this->binaryProtocol;
    }

    /**
     * Set flag indicating whether or not buffered I/O is enabled
     *
     * @param  bool $bufferWrites
     * @return MemcachedOptions
     */
    public function setBufferWrites($bufferWrites)
    {
        $this->bufferWrites = (bool) $bufferWrites;
        return $this;
    }
    
    /**
     * Whether or not buffered I/O is enabled
     *
     * @return bool
     */
    public function getBufferWrites()
    {
        return $this->bufferWrites;
    }

    /**
     * Set flag indicating whether or not to cache DNS lookups
     *
     * @param  bool $cacheLookups
     * @return MemcachedOptions
     */
    public function setCacheLookups($cacheLookups)
    {
        $this->cacheLookups = (bool) $cacheLookups;
        return $this;
    }
    
    /**
     * Whether or not to cache DNS lookups
     *
     * @return bool
     */
    public function getCacheLookups()
    {
        return $this->cacheLookups;
    }

    /**
     * Set flag indicating whether or not to use compression
     *
     * @param  bool $compression
     * @return MemcachedOptions
     */
    public function setCompression($compression)
    {
        $this->compression = (bool) $compression;
        return $this;
    }
    
    /**
     * Whether or not compression is enabled
     *
     * @return bool
     */
    public function getCompression()
    {
        return $this->compression;
    }

    /**
     * Set interval for connection timeouts, in ms
     *
     * @param  int $connectTimeout
     * @return MemcachedOptions
     */
    public function setConnectTimeout($connectTimeout)
    {
        if ((!is_int($connectTimeout) && !is_numeric($connectTimeout)) 
            || 0 > $connectTimeout
        ) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a positive integer',
                __METHOD__
            ));
        }

        $this->connectTimeout = (int) $connectTimeout;
        return $this;
    }
    
    /**
     * Get connection timeout value
     *
     * @return int
     */
    public function getConnectTimeout()
    {
        return $this->connectTimeout;
    }

    /**
     * Set server distribution algorithm
     *
     * @param  int $distribution
     * @return MemcachedOptions
     */
    public function setDistribution($distribution)
    {
        if (!in_array($distribution, array(
            MemcachedResource::DISTRIBUTION_MODULA,
            MemcachedResource::DISTRIBUTION_CONSISTENT,
        ))) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects either Memcached::DISTRIBUTION_MODULA or Memcached::DISTRIBUTION_CONSISTENT',
                __METHOD__
            ));
        }

        $this->distribution = $distribution;
        return $this;
    }
    
    /**
     * Get server distribution algorithm
     *
     * @return int
     */
    public function getDistribution()
    {
        return $this->distribution;
    }

    /**
     * Set hashing algorithm
     *
     * @param  int $hash
     * @return MemcachedOptions
     */
    public function setHash($hash)
    {
        if (!in_array($hash, array(
            MemcachedResource::HASH_DEFAULT,
            MemcachedResource::HASH_MD5,
            MemcachedResource::HASH_CRC,
            MemcachedResource::HASH_FNV1_64,
            MemcachedResource::HASH_FNV1A_64,
            MemcachedResource::HASH_FNV1_32,
            MemcachedResource::HASH_FNV1A_32,
            MemcachedResource::HASH_HSIEH,
            MemcachedResource::HASH_MURMUR,
        ))) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects one of the Memcached::HASH_* constants',
                __METHOD__
            ));
        }

        $this->hash = $hash;
        return $this;
    }
    
    /**
     * Get hash algorithm
     *
     * @return int
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set flag indicating whether or not to enable libketama compatibility
     *
     * @param  bool $libketamaCompatible
     * @return MemcachedOptions
     */
    public function setLibketamaCompatible($libketamaCompatible)
    {
        $this->libketamaCompatible = (bool) $libketamaCompatible;
        return $this;
    }
    
    /**
     * Whether or not to enable libketama compatibility
     *
     * @return bool
     */
    public function getLibketamaCompatible()
    {
        return $this->libketamaCompatible;
    }

    /**
     * Set namespace separator
     *
     * @param  string $separator
     * @return MemcachedOptions
     */
    public function setNamespaceSeparator($separator)
    {
        $this->namespaceSeparator = (string) $separator;
        return $this;
    }

    /**
     * Get namespace separator
     *
     * @return string
     */
    public function getNamespaceSeparator()
    {
        return $this->namespaceSeparator;
    }

    /**
     * Set flag indicating whether or not to enable asynchronous I/O
     *
     * @param  bool $noBlock
     * @return MemcachedOptions
     */
    public function setNoBlock($noBlock)
    {
        $this->noBlock = (bool) $noBlock;
        return $this;
    }
    
    /**
     * Whether or not to enable asynchronous I/O
     *
     * @return bool
     */
    public function getNoBlock()
    {
        return $this->noBlock;
    }

    /**
     * Set interval for connection polling timeout, in ms
     *
     * @param  int $pollTimeout
     * @return MemcachedOptions
     */
    public function setPollTimeout($pollTimeout)
    {
        if ((!is_int($pollTimeout) && !is_numeric($pollTimeout)) 
            || 0 > $pollTimeout
        ) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a positive integer',
                __METHOD__
            ));
        }

        $this->pollTimeout = (int) $pollTimeout;
        return $this;
    }
    
    /**
     * Get connection polling timeout value
     *
     * @return int
     */
    public function getPollTimeout()
    {
        return $this->pollTimeout;
    }

    /**
     * Set prefix for keys
     *
     * @param  string $prefixKey
     * @return MemcachedOptions
     */
    public function setPrefixKey($prefixKey)
    {
        if (!is_string($prefixKey)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a string',
                __METHOD__
            ));
        }
        if (128 < strlen($prefixKey)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a prefix key of no longer than 128 characters',
                __METHOD__
            ));
        }

        $this->prefixKey = $prefixKey;
        return $this;
    }
    
    /**
     * Get prefix key
     *
     * @return string
     */
    public function getPrefixKey()
    {
        return $this->prefixKey;
    }

    /**
     * Set interval for recv timeout, in ms
     *
     * @param  int $recvTimeout
     * @return MemcachedOptions
     */
    public function setRecvTimeout($recvTimeout)
    {
        if ((!is_int($recvTimeout) && !is_numeric($recvTimeout)) 
            || 0 > $recvTimeout
        ) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a positive integer',
                __METHOD__
            ));
        }

        $this->recvTimeout = (int) $recvTimeout;
        return $this;
    }
    
    /**
     * Get recv timeout value
     *
     * @return int
     */
    public function getRecvTimeout()
    {
        return $this->recvTimeout;
    }

    /**
     * Set retry interval, in seconds
     *
     * @param  int $retryTimeout
     * @return MemcachedOptions
     */
    public function setRetryTimeout($retryTimeout)
    {
        if ((!is_int($retryTimeout) && !is_numeric($retryTimeout)) 
            || 0 > $retryTimeout
        ) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a positive integer',
                __METHOD__
            ));
        }

        $this->retryTimeout = (int) $retryTimeout;
        return $this;
    }
    
    /**
     * Get retry timeout value, in seconds
     *
     * @return int
     */
    public function getRetryTimeout()
    {
        return $this->retryTimeout;
    }

    /**
     * Set interval for send timeout, in ms
     *
     * @param  int $sendTimeout
     * @return MemcachedOptions
     */
    public function setSendTimeout($sendTimeout)
    {
        if ((!is_int($sendTimeout) && !is_numeric($sendTimeout)) 
            || 0 > $sendTimeout
        ) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a positive integer',
                __METHOD__
            ));
        }

        $this->sendTimeout = (int) $sendTimeout;
        return $this;
    }
    
    /**
     * Get send timeout value
     *
     * @return int
     */
    public function getSendTimeout()
    {
        return $this->sendTimeout;
    }

    /**
     * Set serializer
     *
     * @param  int $serializer
     * @return MemcachedOptions
     */
    public function setSerializer($serializer)
    {
        if (!in_array($serializer, array(
            MemcachedResource::SERIALIZER_PHP,
            MemcachedResource::SERIALIZER_IGBINARY,
            MemcachedResource::SERIALIZER_JSON,
        ))) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects one of the Memcached::SERIALIZER_* constants',
                __METHOD__
            ));
        }

        if ($serializer == MemcachedResource::SERIALIZER_IGBINARY) {
            if (!MemcachedResource::HAVE_IGBINARY) {
                throw new Exception\RuntimeException(sprintf(
                    '%s: cannot set to igbinary; not available',
                    __METHOD__
                ));
            }
        }

        if ($serializer == MemcachedResource::SERIALIZER_JSON) {
            if (!MemcachedResource::HAVE_JSON) {
                throw new Exception\RuntimeException(sprintf(
                    '%s: cannot set to json; not available',
                    __METHOD__
                ));
            }
        }

        $this->serializer = $serializer;
        return $this;
    }
    
    /**
     * Get serializer
     *
     * @return int
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * Set maximum number of server connection failures
     *
     * @param  int $serverFailureLimit
     * @return MemcachedOptions
     */
    public function setServerFailureLimit($serverFailureLimit)
    {
        if ((!is_int($serverFailureLimit) && !is_numeric($serverFailureLimit)) 
            || 0 > $serverFailureLimit
        ) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a positive integer',
                __METHOD__
            ));
        }

        $this->serverFailureLimit = (int) $serverFailureLimit;
        return $this;
    }
    
    /**
     * Get maximum server failures allowed
     *
     * @return int
     */
    public function getServerFailureLimit()
    {
        return $this->serverFailureLimit;
    }

    /**
     * Set maximum socket send buffer in bytes
     *
     * @param  int $socketSendSize
     * @return MemcachedOptions
     */
    public function setSocketSendSize($socketSendSize)
    {
        if ($socketSendSize === null) {
            return $this;
        }
        
        if ((!is_int($socketSendSize) && !is_numeric($socketSendSize)) 
            || 0 > $socketSendSize
        ) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a positive integer',
                __METHOD__
            ));
        }

        $this->socketSendSize = (int) $socketSendSize;
        return $this;
    }
    
    /**
     * Get maximum socket send buffer in bytes
     *
     * @return int
     */
    public function getSocketSendSize()
    {
        return $this->socketSendSize;
    }

    /**
     * Set maximum socket recv buffer in bytes
     *
     * @param  int $socketRecvSize
     * @return MemcachedOptions
     */
    public function setSocketRecvSize($socketRecvSize)
    {
        if ($socketRecvSize === null) {
            return $this;
        }
        
        if ((!is_int($socketRecvSize) && !is_numeric($socketRecvSize)) 
            || 0 > $socketRecvSize
        ) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a positive integer',
                __METHOD__
            ));
        }

        $this->socketRecvSize = (int) $socketRecvSize;
        return $this;
    }
    
    /**
     * Get maximum socket recv buffer in bytes
     *
     * @return int
     */
    public function getSocketRecvSize()
    {
        return $this->socketRecvSize;
    }

    /**
     * Set whether or not to enable no-delay feature when connecting sockets
     *
     * @param  bool $tcpNodelay
     * @return MemcachedOptions
     */
    public function setTcpNodelay($tcpNodelay)
    {
        $this->tcpNodelay = (bool) $tcpNodelay;
        return $this;
    }
    
    /**
     * Whether or not to enable no-delay feature when connecting sockets
     *
     * @return bool
     */
    public function getTcpNodelay()
    {
        return $this->tcpNodelay;
    }

    /**
     * Get map of option keys to \Memcached constants
     * 
     * @return array
     */
    public function getOptionsMap()
    {
        return $this->optionsMap;
    }
}
