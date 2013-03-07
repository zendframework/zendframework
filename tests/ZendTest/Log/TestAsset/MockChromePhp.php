<?php
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

    public function log($line)
    {
        $this->calls['log'][] = $line;
    }

    public function warn($line)
    {
        $this->calls['warn'][] = $line;
    }

    public function error($line)
    {
        $this->calls['error'][] = $line;
    }

    public function info($line)
    {
        $this->calls['info'][] = $line;
    }

    public function group($line)
    {
        $this->calls['group'][] = $line;
    }

    public function groupCollapsed($line)
    {
        $this->calls['groupCollapsed'][] = $line;
    }

    public function groupEnd($line)
    {
        $this->calls['groupEnd'][] = $line;
    }
}
