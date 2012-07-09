<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @package   Zend_Config
 */

namespace Zend\Config\Writer;

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage Writer
 */
class PhpArray extends AbstractWriter
{
    /**
     * processConfig(): defined by AbstractWriter.
     *
     * @param  array $config
     * @return string
     */
    public function processConfig(array $config)
    {
        $arrayString = "<?php\n"
                     . "return " . var_export($config, true) . ";\n";

        return $arrayString;
    }
}
