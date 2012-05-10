<?php
namespace ZendTest\Log\TestAsset;

use Zend\Log\Writer\Syslog as SyslogWriter;

class CustomSyslogWriter extends SyslogWriter
{
    public function getFacility()
    {
        return $this->facility;
    }
}
