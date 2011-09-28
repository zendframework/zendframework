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
 * @package    Zend\Service
 * @subpackage GoGrid
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Service\GoGrid;

use Zend\Service\GoGrid\GoGrid as GoGridAbstract,
    Zend\Service\GoGrid\Object as GoGridObject,
    Zend\Service\GoGrid\ObjectList as GoGridObjectList;

class Server extends GoGridAbstract
{
    const API_GRID_SERVER_LIST   = 'grid/server/list';
    const API_GRID_SERVER_GET    = 'grid/server/get';
    const API_GRID_SERVER_ADD    = 'grid/server/add';
    const API_GRID_SERVER_EDIT   = 'grid/server/edit';
    const API_GRID_SERVER_DELETE = 'grid/server/delete';
    const API_GRID_SERVER_POWER  = 'grid/server/power';
    const API_POWER_START        = 'start';
    const API_POWER_STOP         = 'stop';
    const API_POWER_RESTART      = 'restart';
    /**
     * Get Server List
     * 
     * This call will list all the servers in the system.
     *
     * @param array $options
     * @return Zend\Service\GoGrid\ObjectList
     */
    public function getList($options=array()) {
        $result = parent::_call(self::API_GRID_SERVER_LIST, $options);
        return new GoGridObjectList($result);
    }
    /**
     * Get Server
     * 
     * This call will retrieve one or many server objects from your list of servers
     *
     * @param string|array $server
     * @return Zend\Service\GoGrid\ObjectList
     */
    public function get($server)
    {
        if (empty($server)) {
            throw new Exception\InvalidArgumentException("The server.get API needs a id/name server parameter");
        }
        $options=array();
        $options['server']= $server;
        $result= $this->_call(self::API_GRID_SERVER_GET, $options);
        return new GoGridObjectList($result);
    }
    /**
     * Add Server
     * 
     * This call will add a single server object to your grid.
     * To create an image sandbox pass the optional isSandbox parameter to true.
     * If isSandbox is set to true, the request parameter server.ram is ignored and non-mandatory.
     *
     * @param string $name
     * @param string $image
     * @param string $ram
     * @param string $ip
     * @return Zend\Service\GoGrid\ObjectList
     */
    public function add($name,$image,$ram,$ip, $options=array()) {
        if (empty($name) || strlen($name)>20) {
            throw new Exception\InvalidArgumentException("You must specify the name of the server in a string of 20 character max.");
        }
        if (empty($image)) {
            throw new Exception\InvalidArgumentException("You must specify the server image's ID or name");
        }
        if (empty($ram) && (empty($options) || !$options['isSandbox'])) {
            throw new Exception\InvalidArgumentException("You must specify the ID or name of the desired RAM option");
        }
        if (empty($ip)) {
            throw new Exception\InvalidArgumentException("You must specify the IP address of the server");
        }
        $options['name']= $name;
        $options['image']= $image;
        if (!empty($ram)) {
            $options['server.ram']= $ram;
        }
        $options['ip']= $ip;
        $result= $this->_call(self::API_GRID_SERVER_ADD, $options);
        return new GoGridObjectList($result);
    }
    /**
     * Edit Server
     * 
     * This call will edit a single server object in your grid.
     * You can use this call to edit a server's:
     * RAM (Upgrade RAM)
     * Server Type (Change between Web/App Server and Database Server)
     * Description (Change freeform text description) 
     * 
     * @param string|array $server
     * @return GoGridObjectList 
     */
    public function edit($server,$options=array())
    {
        if (empty($server)) {
            throw new Exception\InvalidArgumentException("The server.edit API needs a id/name server parameters");
        }
        $options['server']= $server;
        $result= $this->_call(self::API_GRID_SERVER_EDIT, $options);
        return new GoGridObjectList($result);
    }
    /**
     * Power Server
     * 
     * This call will issue a power command to a server object in your grid.
     * Supported power commands are: start, stop, and restart
     *
     * @param string $server
     * @param string $power
     * @return GoGridObjectList
     */
    public function power($server,$power) {
        if (empty($server)) {
            throw new Exception\InvalidArgumentException("The server.power API needs a id/name server parameter");
        }
        $power=strtolower($power);
        if (empty($power) || !in_array($power,array(self::API_POWER_START,self::API_POWER_STOP,self::API_POWER_RESTART))) {
            throw new Exception\InvalidArgumentException("The server.power API needs the power parameter (start,stop, or restart)");
        }
        $options=array();
        $options['server']= $server;
        $options['power']= $power;
        $result= $this->_call(self::API_GRID_SERVER_POWER, $options);
        return new GoGridObjectList($result);
    }
    /**
     * Start a server
     *
     * @param string $server
     * @return GoGridObjectList
     */
    public function start($server) {
       return $this->power($server,self::API_POWER_START);
    }
    /**
     * Stop a server
     *
     * @param string $server
     * @return GoGridObjectList
     */
    public function stop($server) {
       return $this->power($server,self::API_POWER_STOP);
    }
    /**
     * Restart a server
     *
     * @param string $server
     * @return GoGridObjectList
     */
    public function restart($server) {
       return $this->power($server,self::API_POWER_RESTART);
    }
    /**
     * Delete a server
     * 
     * @param string $server 
     * @return GoGridObjectList
     */
    public function delete($server) {
        if (empty($server)) {
            throw new Exception\InvalidArgumentException("The server.delete API needs an id/name server parameter");
        }
        $options=array();
        $options['server']= $server;
        $result= $this->_call(self::API_GRID_SERVER_DELETE, $options);
        return new GoGridObjectList($result);
    }
}
