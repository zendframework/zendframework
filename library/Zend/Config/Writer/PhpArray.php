<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Config\Writer;

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
        return "<?php\n\n" .
               "return array(\n" . $this->processIndented($config) . ");\n";
    }

    protected function processIndented(array $config, &$indentLevel = 1)
    {
        $arrayString = "";

        foreach ($config as $key => $value) {
            $arrayString .= str_repeat('    ', $indentLevel);
            $arrayString .= (is_numeric($key) ? $key : "'" . addslashes($key) . "'") . ' => ';

            if (is_array($value)) {
                if ($value === array()) {
                    $arrayString .= "array(),\n";
                } else {
                    $indentLevel++;
                    $arrayString .= "array(\n" . $this->processIndented($value, $indentLevel)
                                  . str_repeat('    ', --$indentLevel) . "),\n";
                }
            } elseif (is_object($value)) {
                $arrayString .= var_export($value, true) . ",\n";
            } else {
                $arrayString .= "'" . addslashes($value) . "',\n";
            }
        }

        return $arrayString;
    }
}
