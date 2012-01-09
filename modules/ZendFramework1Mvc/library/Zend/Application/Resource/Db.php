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
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Application\Resource;

/**
 * Resource for creating database adapter
 *
 * @uses       \Zend\Application\Resource\AbstractResource
 * @uses       \Zend\Db\Db
 * @uses       \Zend\Db\Table\Table
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Db extends AbstractResource
{
    /**
     * Adapter to use
     *
     * @var string
     */
    protected $_adapter = null;

    /**
     * @var Zend_Db_Adapter_Interface
     */
    protected $_db;

    /**
     * Parameters to use
     *
     * @var array
     */
    protected $_params = array();

    /**
     * Wether to register the created adapter as default table adapter
     *
     * @var boolean
     */
    protected $_isDefaultTableAdapter = true;

    /**
     * Set the adapter
     *
     * @param  $adapter string
     * @return \Zend\Application\Resource\Db
     */
    public function setAdapter($adapter)
    {
        $this->_adapter = $adapter;
        return $this;
    }

    /**
     * Adapter type to use
     *
     * @return string
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }

    /**
     * Set the adapter params
     *
     * @param  $adapter string
     * @return \Zend\Application\Resource\Db
     */
    public function setParams(array $params)
    {
        $this->_params = $params;
        return $this;
    }

    /**
     * Adapter parameters
     *
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * Set whether to use this as default table adapter
     *
     * @param  boolean $defaultTableAdapter
     * @return \Zend\Application\Resource\Db
     */
    public function setIsDefaultTableAdapter($isDefaultTableAdapter)
    {
        $this->_isDefaultTableAdapter = $isDefaultTableAdapter;
        return $this;
    }

    /**
     * Is this adapter the default table adapter?
     *
     * @return void
     */
    public function isDefaultTableAdapter()
    {
        return $this->_isDefaultTableAdapter;
    }

    /**
     * Retrieve initialized DB connection
     *
     * @return null|Zend_Db_Adapter_Interface
     */
    public function getDbAdapter()
    {
        if ((null === $this->_db)
            && (null !== ($adapter = $this->getAdapter()))
        ) {
            $this->_db = \Zend\Db\Db::factory($adapter, $this->getParams());
        }
        return $this->_db;
    }

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return \Zend\Db\Adapter\AbstractAdapter|null
     */
    public function init()
    {
        if (null !== ($db = $this->getDbAdapter())) {
            if ($this->isDefaultTableAdapter()) {
                \Zend\Db\Table\Table::setDefaultAdapter($db);
            }
            return $db;
        }
    }

    /**
     * Set the default metadata cache
     * 
     * @param string|Zend_Cache_Core $cache
     * @return Zend_Application_Resource_Db
     */
    public function setDefaultMetadataCache($cache)
    {
        $metadataCache = null;

        if (is_string($cache)) {
            $bootstrap = $this->getBootstrap();
            if ($bootstrap instanceof \Zend\Application\ResourceBootstrapper
                && $bootstrap->getBroker()->hasPlugin('CacheManager')
            ) {
                $cacheManager = $bootstrap->bootstrap('CacheManager')
                    ->getResource('CacheManager');
                if (null !== $cacheManager && $cacheManager->hasCache($cache)) {
                    $metadataCache = $cacheManager->getCache($cache);
                }
            }
        } else if ($cache instanceof \Zend\Cache\Frontend) {
            $metadataCache = $cache;
        }

        if ($metadataCache instanceof \Zend\Cache\Frontend) {
            \Zend\Db\Table\AbstractTable::setDefaultMetadataCache($metadataCache);
        }

        return $this;
    }
}
