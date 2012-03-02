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
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Db\Adapter\Driver;

use Zend\Db\Adapter\ParameterContainerInterface;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface StatementInterface
{
    /**
     * @return resource
     */
    public function getResource();

    /**
     * @abstract
     * @param string $sql
     */
    public function setSql($sql);

    /**
     * @abstract
     * @return string
     */
    public function getSql();

    /**
     * @abstract
     * @param ParameterContainerInterface $parameterContainer
     */
    public function setParameterContainer(ParameterContainerInterface $parameterContainer);

    /**
     * @abstract
     * @return ParameterContainerInterface
     */
    public function getParameterContainer();

    /**
     * @abstract
     * @return bool
     */
    // public function isQuery();

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
