<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace Zend\Service\Amazon\SimpleDb;

/**
 * The Custom Exception class that allows you to have access to the AWS Error Code.
 *
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage SimpleDb
 */
class Page
{
    /** @var string Page data */
    protected $_data;

    /** @var string|null Token identifying page */
    protected $_token;

    /**
     * Constructor
     *
     * @param  string $data
     * @param  string|null $token
     * @return void
     */
    public function __construct($data, $token = null)
    {
        $this->_data  = $data;
        $this->_token = $token;
    }

    /**
     * Retrieve page data
     *
     * @return string
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Retrieve token
     *
     * @return string|null
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * Determine whether this is the last page of data
     *
     * @return void
     */
    public function isLast()
    {
        return (null === $this->_token);
    }

    /**
     * Cast to string
     *
     * @return string
     */
    public function __toString()
    {
        return "Page with token: " . $this->_token
             . "\n and data: " . $this->_data;
    }
}
