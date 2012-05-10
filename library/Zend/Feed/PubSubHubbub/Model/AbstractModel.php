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
 * @package    Zend_Feed_Pubsubhubbub
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Feed\PubSubHubbub\Model;

use \Zend\Db\TableGateway;

/**
 * @category   Zend
 * @package    Zend_Feed_Pubsubhubbub
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class AbstractModel
{
    /**
     * Zend_Db_Table instance to host database methods
     *
     * @var \Zend\Db\Table\Table
     */
    protected $_db = null;
 
    /**
     * Constructor
     * 
     * @param  array $data 
     * @param  \Zend\Db\Table\AbstractTable $tableGateway 
     * @return void
     */
    public function __construct(TableGateway\TableGatewayInterface $tableGateway = null)
    {
        if ($tableGateway === null) {
            $parts = explode('\\', get_class($this));
            $table = strtolower(array_pop($parts));
            $this->_db = new TableGateway\TableGateway($table);
        } else {
            $this->_db = $tableGateway;
        }
    }

}
