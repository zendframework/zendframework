<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Amf
 */

namespace Zend\Amf\Parser\Resource;

/**
 * This class will convert mysql result resource to array suitable for passing
 * to the external entities.
 *
 * @package    Zend_Amf
 * @subpackage Parser
 */
class MysqlResult
{
    /**
     * @var array List of Mysql types with PHP counterparts
     *
     * Key => Value is Mysql type (exact string) => PHP type
     */
    static public $fieldTypes = array(
        "int"       => "int",
        "timestamp" => "int",
        "year"      => "int",
        "real"      => "float",
    );
    /**
     * Parse resource into array
     *
     * @param resource $resource
     * @return array
     */
    public function parse($resource)
    {
        $result   = array();
        $fieldcnt = mysql_num_fields($resource);

        $fields_transform = array();
        for ($i=0; $i < $fieldcnt; $i++) {
            $type = mysql_field_type($resource, $i);
            if (isset(self::$fieldTypes[$type])) {
                $fields_transform[mysql_field_name($resource, $i)] = self::$fieldTypes[$type];
            }
        }

        while ($row = mysql_fetch_object($resource)) {
            foreach($fields_transform as $fieldname => $fieldtype) {
               settype($row->$fieldname, $fieldtype);
            }
            $result[] = $row;
        }
        return $result;
    }
}
