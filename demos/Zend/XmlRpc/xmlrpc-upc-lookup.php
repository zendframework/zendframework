<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_XmlRpc
 */

use Zend\Loader\StandardAutoloader;
use Zend\XmlRpc\Client;

require_once dirname(dirname(dirname(__DIR__))) . '/library/Zend/Loader/StandardAutoloader.php';
$loader = new StandardAutoloader(array('autoregister_zf' => true));
$loader->register();

$server = new Client('http://www.upcdatabase.com/xmlrpc');

$client = $server->getProxy();

print_r($client->lookup(
            array(
                 'rpc_key' => '0000...0000',	// Set your rpc_key here
                 'upc' => '123456789012',
            )
        )
);
