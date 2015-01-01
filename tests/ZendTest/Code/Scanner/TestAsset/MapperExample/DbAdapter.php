<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Scanner\TestAsset\MapperExample;

class DbAdapter
{
    protected $username = null;
    protected $password = null;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function __toString()
    {
        return 'I am ' . get_class($this) . ' object (hash ' . spl_object_hash($this) . '), with these parameters (username = ' . $this->username . ', password = ' . $this->password . ')';
    }

}
