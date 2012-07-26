<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Feed
 */

namespace Zend\Feed\PubSubHubbub\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\TableGatewayInterface;

/**
 * @category   Zend
 * @package    Zend_Feed_Pubsubhubbub
 */
class AbstractModel
{
    /**
     * Zend\Db\TableGateway\TableGatewayInterface instance to host database methods
     *
     * @var TableGatewayInterface
     */
    protected $db = null;

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
            $this->db = new TableGateway($table, null);
        } else {
            $this->db = $tableGateway;
        }
    }
}
