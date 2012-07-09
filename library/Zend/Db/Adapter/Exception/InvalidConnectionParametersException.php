<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @package   Zend_Db
 */

namespace Zend\Db\Adapter\Exception;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 */
class InvalidConnectionParametersException extends \RuntimeException implements ExceptionInterface
{

    /**
     * @var int
     */
    protected $parameters;

    /**
     * @param string $message
     * @param int $parameters
     */
    public function __construct($message, $parameters)
    {
        parent::__construct($message);
        $this->parameters = $parameters;
    }

}
