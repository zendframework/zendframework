<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Amf
 */

namespace Zend\Amf\Parser;

use Zend\Loader\PluginClassLoader;

/**
 * Plugin Class Loader implementation for parsers
 *
 * @category   Zend
 * @package    Zend_Amf
 * @subpackage Parser
 */
class ParserLoader extends PluginClassLoader
{
    /**
     * @var array Pre-aliased parsers
     */
    protected $plugins = array(
        'mysqlresult'   => 'Zend\Amf\Parser\Resource\MysqlResult',
        'mysql_result'  => 'Zend\Amf\Parser\Resource\MysqlResult',
        'mysqliresult'  => 'Zend\Amf\Parser\Resource\MysqliResult',
        'mysqli_result' => 'Zend\Amf\Parser\Resource\MysqliResult',
        'stream'        => 'Zend\Amf\Parser\Resource\Stream',
    );
}
