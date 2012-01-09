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
 * @package    Zend_Db
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Db\Table;

use Zend\Config\Config;

/**
 * Class for SQL table interface.
 *
 * @uses       \Zend\Db\Table\Table
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Definition
{

    /**
     * @var array
     */
    protected $_tableConfigs = array();

    /**
     * __construct()
     *
     * @param array|\Zend\Config\Config $options
     */
    public function __construct($options = null)
    {
        if ($options instanceof Config) {
            $this->setConfig($options);
        } elseif (is_array($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * setConfig()
     *
     * @param \Zend\Config\Config $config
     * @return \Zend\Db\Table\Definition
     */
    public function setConfig(Config $config)
    {
        $this->setOptions($config->toArray());
        return $this;
    }

    /**
     * setOptions()
     *
     * @param array $options
     * @return \Zend\Db\Table\Definition
     */
    public function setOptions(Array $options)
    {
        foreach ($options as $optionName => $optionValue) {
            $this->setTableConfig($optionName, $optionValue);
        }
        return $this;
    }

    /**
     * @param string $tableName
     * @param array  $tableConfig
     * @return \Zend\Db\Table\Definition
     */
    public function setTableConfig($tableName, array $tableConfig)
    {
        // @todo logic here
        $tableConfig[Table::DEFINITION_CONFIG_NAME] = $tableName;
        $tableConfig[Table::DEFINITION] = $this;

        if (!isset($tableConfig[Table::NAME])) {
            $tableConfig[Table::NAME] = $tableName;
        }

        $this->_tableConfigs[$tableName] = $tableConfig;
        return $this;
    }

    /**
     * getTableConfig()
     *
     * @param string $tableName
     * @return array
     */
    public function getTableConfig($tableName)
    {
        return $this->_tableConfigs[$tableName];
    }

    /**
     * removeTableConfig()
     *
     * @param string $tableName
     */
    public function removeTableConfig($tableName)
    {
        unset($this->_tableConfigs[$tableName]);
    }

    /**
     * hasTableConfig()
     *
     * @param string $tableName
     * @return bool
     */
    public function hasTableConfig($tableName)
    {
        return (isset($this->_tableConfigs[$tableName]));
    }

}
