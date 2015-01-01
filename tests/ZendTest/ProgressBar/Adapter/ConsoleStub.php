<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\ProgressBar\Adapter;

use Zend\ProgressBar\Adapter;

class ConsoleStub extends Adapter\Console
{
    protected $lastOutput = null;

    public function getLastOutput()
    {
        return $this->lastOutput;
    }

    protected function _outputData($data)
    {
        $this->lastOutput = $data;
    }

    public function getCharset()
    {
        return $this->charset;
    }
}
