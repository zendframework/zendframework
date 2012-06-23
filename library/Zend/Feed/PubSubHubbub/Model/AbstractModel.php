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

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\TableGatewayInterface;

/**
 * @category   Zend
 * @package    Zend_Feed_Pubsubhubbub
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class AbstractModel
{
    /**
     * Zend\Db\TableGateway\TableGatewayInterface instance to host database methods
     *
     * @var TableGatewayInterface
     */
    protected $_db = null;

    /**
     * Constructor
     *
     * @param null|TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway = null)
    {
        if ($tableGateway === null) {
            $parts = explode('\\', get_called_class());
            $table = strtolower(array_pop($parts));
            $this->_db = new TableGateway($table, null);
        } else {
            $this->_db = $tableGateway;
        }
    }
}
