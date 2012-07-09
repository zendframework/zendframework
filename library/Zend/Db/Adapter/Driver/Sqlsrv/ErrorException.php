<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @package   Zend_Db
 */

namespace Zend\Db\Adapter\Driver\Sqlsrv;

use Zend\Db\Adapter\Exception\ErrorException as BaseErrorException;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 */
class ErrorException extends BaseErrorException
{

    /**
     * Errors
     * 
     * @var array
     */
    protected $errors = array();

    /**
     * Construct
     * 
     * @param boolean $errors 
     */
    public function __construct($errors = false)
    {
        $this->errors = ($errors === false) ? sqlsrv_errors() : $errors;
    }

}
