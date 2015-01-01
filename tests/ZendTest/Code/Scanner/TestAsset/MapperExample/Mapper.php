<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Scanner\TestAsset\MapperExample;

class Mapper
{

    protected $dbAdapter = null;

    public function __construct()
    {
    }

    public function setDbAdapter(DbAdapter $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }

    public function __toString()
    {
        return 'I am a ' . get_class($this) . ' object (hash ' . spl_object_hash($this) . '), using this dbAdapter ' . PHP_EOL . '    ' . $this->dbAdapter;
    }

}
