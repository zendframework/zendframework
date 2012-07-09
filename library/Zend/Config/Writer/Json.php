<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @package   Zend_Config
 */

namespace Zend\Config\Writer;

use Zend\Json\Json as JsonFormat;

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage Writer
 */
class Json extends AbstractWriter
{
    /**
     * processConfig(): defined by AbstractWriter.
     *
     * @param  array $config
     * @return string
     */
    public function processConfig(array $config)
    {
        return JsonFormat::encode($config);
    }
}
