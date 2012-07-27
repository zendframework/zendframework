<?php
namespace ZendTest\Log\TestAsset;

use Zend\Log\Writer\FirePhp\FirePhpInterface;

class MockFirePhp implements FirePhpInterface
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
