<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Log\TestAsset;

use Zend\Log\Writer\ChromePhp\ChromePhpInterface;

class MockChromePhp implements ChromePhpInterface
{
    public $calls = array();

    protected $enabled;

    public function __construct($enabled = true)
    {
        $this->enabled = $enabled;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function error($line)
    {
        $this->calls['error'][] = $line;
    }

    public function warn($line)
    {
        $this->calls['warn'][] = $line;
    }

    public function info($line)
    {
        $this->calls['info'][] = $line;
    }

    public function trace($line)
    {
        $this->calls['trace'][] = $line;
    }

    public function log($line)
    {
        $this->calls['log'][] = $line;
    }
}
