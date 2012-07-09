<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Queue
 */

namespace Zend\Queue\Adapter\Db;

/**
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage Adapter
 */
class Queue extends \Zend\Db\Table\AbstractTable
{
    /**
     * @var string
     */
    protected $_name = 'queue';

    /**
     * @var string
     */
    protected $_primary = 'queue_id';

    /**
     * @var mixed
     */
    protected $_sequence = true;
}
