<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\Adapter;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 */
interface StatementContainerInterface
{
    /**
     * @abstract
     * @param $sql
     * @return mixed
     */
    public function setSql($sql);

    /**
     * @abstract
     * @return mixed
     */
    public function getSql();

    /**
     * @abstract
     * @return mixed
     */
    public function setParameterContainer(ParameterContainer $parameterContainer);

    /**
     * @abstract
     * @return mixed
     */
    public function getParameterContainer();
}
