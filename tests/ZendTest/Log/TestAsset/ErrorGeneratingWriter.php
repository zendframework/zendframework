<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */

namespace ZendTest\Log\TestAsset;

use Zend\Log\Writer\AbstractWriter;

class ErrorGeneratingWriter extends AbstractWriter
{
    protected function doWrite(array $event)
    {
        $stream = fopen("php://memory", "r");
        fclose($stream);
        fwrite($stream, "test");
    }
}
