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
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Db\TestUtil;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PdoMysql extends Mysqli
{
    protected $_enabledConstantName = 'TESTS_ZEND_DB_ADAPTER_PDO_MYSQL_ENABLED';
    
    protected function _rawQuery($sql)
    {
        $conn = $this->_db->getConnection();
        $retval = $conn->exec($sql);
        if ($retval === false) {
            $e = $conn->error;
            throw new \Zend\Db\Exception("SQL error for \"$sql\": $e");
        }
    }

    public function getParams(array $constants = array())
    {
        $constants = parent::getParams($constants);

        if (!isset($constants['driver_options'])) {
            $constants['driver_options'] = array();
        }

        if (!isset($constants['driver_options'][\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY])) {
            $constants['driver_options'][\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY] = true;
        }

        return $constants;
    }
}

