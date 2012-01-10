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

use Zend\Db\Adapter,
    Zend\Application\ResourceException;

/**
 * Database resource for multiple database setups
 *
 * Example configuration:
 * <pre>
 *   resources.multidb.defaultMetadataCache = "database"
 *
 *   resources.multidb.db1.adapter = "pdo_mysql"
 *   resources.multidb.db1.host = "localhost"
 *   resources.multidb.db1.username = "webuser"
 *   resources.multidb.db1.password = "XXXX"
 *   resources.multidb.db1.dbname = "db1"
 *   resources.multidb.db1.default = true
 *
 *   resources.multidb.db2.adapter = "pdo_pgsql"
 *   resources.multidb.db2.host = "example.com"
 *   resources.multidb.db2.username = "dba"
 *   resources.multidb.db2.password = "notthatpublic"
 *   resources.multidb.db2.dbname = "db2"
 * </pre>
 *
 * @uses       \Zend\Application\ResourceException
 * @uses       \Zend\Application\Resource\AbstractResource
 * @uses       \Zend\Db\Db
 * @uses       \Zend\Db\Table\Table
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class MultiDb extends AbstractResource
{
    /**
     * Associative array containing all configured db's
     *
     * @var array
     */
    protected $_dbs = array();

    /**
     * An instance of the default db, if set
     * 
     * @var null|\Zend\Db\Adapter\AbstractAdapter
     */
    protected $_defaultDb;

    /**
     * Initialize the Database Connections (instances of Zend_Db_Table_Abstract)
     *
     * @return \Zend\Application\Resource\Multidb
     */    
    public function init() 
    {
        $options = $this->getOptions();

        if (isset($options['defaultMetadataCache'])) {
            $this->_setDefaultMetadataCache($options['defaultMetadataCache']);
            unset($options['defaultMetadataCache']);
        }

        foreach ($options as $id => $params) {
        	$adapter = $params['adapter'];
            $default = (int) (
                isset($params['isDefaultTableAdapter']) && $params['isDefaultTableAdapter']
                || isset($params['default']) && $params['default']
            );
            unset(
                $params['adapter'],
                $params['default'],
                $params['isDefaultTableAdapter']
            );

            $this->_dbs[$id] = \Zend\Db\Db::factory($adapter, $params);

            if ($default) {
                $this->_setDefault($this->_dbs[$id]);
            }
        }

        return $this;
    }

    /**
     * Determine if the given db(identifier) is the default db.
     *
     * @param  string|\Zend\Db\Adapter\AbstractAdapter $db The db to determine whether it's set as default
     * @return boolean True if the given parameter is configured as default. False otherwise
     */
    public function isDefault($db)
    {
        if(!$db instanceof Adapter\AbstractAdapter) {
            $db = $this->getDb($db);
        }

        return $db === $this->_defaultDb;
    }

    /**
     * Retrieve the specified database connection
     * 
     * @param  null|string|\Zend\Db\Adapter\AbstractAdapter $db The adapter to retrieve.
     *                                               Null to retrieve the default connection
     * @return \Zend\Db\Adapter\AbstractAdapter
     * @throws \Zend\Application\ResourceException if the given parameter could not be found
     */
    public function getDb($db = null)
    {
        if ($db === null) {
            return $this->getDefaultDb();
        }

        if (isset($this->_dbs[$db])) {
            return $this->_dbs[$db];
        }
        
        throw new Exception\InitializationException(
            'A DB adapter was tried to retrieve, but was not configured'
        );
    }

    /**
     * Get the default db connection
     *
     * @param  boolean $justPickOne If true, a random (the first one in the stack)
     *                           connection is returned if no default was set.
     *                           If false, null is returned if no default was set.
     * @return null|\Zend\Db\Adapter\AbstractAdapter
     */
    public function getDefaultDb($justPickOne = true)
    {
        if ($this->_defaultDb !== null) {
            return $this->_defaultDb;
        }

        if ($justPickOne) {
            return reset($this->_dbs); // Return first db in db pool
        }

        return null;
    }

    /**
     * Set the default db adapter
     * 
     * @var \Zend\Db\Adapter\AbstractAdapter $adapter Adapter to set as default
     */
    protected function _setDefault(Adapter\AbstractAdapter $adapter) 
    {
        \Zend\Db\Table\Table::setDefaultAdapter($adapter);
        $this->_defaultDb = $adapter;
    }

   /**
     * Set the default metadata cache
     * 
     * @param string|Zend_Cache_Core $cache
     * @return Zend_Application_Resource_Multidb
     */
    protected function _setDefaultMetadataCache($cache)
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
