<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\Adapter\Driver;

use Zend\Db\Adapter\StatementContainerInterface;
use Zend\Db\Adapter\ParameterContainer;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 */
interface StatementInterface extends StatementContainerInterface
{

    /**
     * @return resource
     */
    public function getResource();

    /**
     * @abstract
     * @param string $sql
     */
    public function prepare($sql = null);

    /**
     * @abstract
     * @return bool
     */
    public function isPrepared();

    /**
     * @abstract
     * @param null $parameters
     * @return ResultInterface
     */
    public function execute($parameters = null);

}
