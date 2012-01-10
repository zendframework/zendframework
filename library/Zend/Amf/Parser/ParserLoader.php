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
 * @package    Zend_Amf
 * @subpackage Parser
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Amf\Parser;

use Zend\Loader\PluginClassLoader;

/**
 * Plugin Class Loader implementation for parsers
 *
 * @category   Zend
 * @package    Zend_Amf
 * @subpackage Parser
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
