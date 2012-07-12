<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_OAuth
 */

namespace Zend\OAuth\Token;

use Zend\OAuth\Http;

/**
 * @category   Zend
 * @package    Zend_OAuth
 */
class AuthorizedRequest extends AbstractToken
{
    /**
     * @var array
     */
    protected $_data = array();

    /**
     * Constructor
     *
     * @param  null|array $data
     * @param  null|\Zend\OAuth\Http\Utility $utility
     * @return void
     */
    public function __construct(array $data = null, Http\Utility $utility = null)
    {
        if ($data !== null) {
            $this->_data = $data;
            $params = $this->_parseData();
            if (count($params) > 0) {
                $this->setParams($params);
            }
        }
        if ($utility !== null) {
            $this->_httpUtility = $utility;
        } else {
            $this->_httpUtility = new Http\Utility;
        }
    }

    /**
     * Retrieve token data
     *
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Indicate if token is valid
     *
     * @return bool
     */
    public function isValid()
    {
        if (isset($this->_params[self::TOKEN_PARAM_KEY])
            && !empty($this->_params[self::TOKEN_PARAM_KEY])
        ) {
            return true;
        }
        return false;
    }

    /**
     * Parse string data into array
     *
     * @return array
     */
    protected function _parseData()
    {
        $params = array();
        if (empty($this->_data)) {
            return;
        }
        foreach ($this->_data as $key => $value) {
            $params[rawurldecode($key)] = rawurldecode($value);
        }
        return $params;
    }
}
