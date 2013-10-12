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
        $arrayString = "<?php\n\n"
                     . "return";
        $indentLevel = 0;

        foreach (explode("\n", var_export($config, true)) as $line) {
            $line = trim($line);

            if ($line === '),' || $line === ')') {
                $indentLevel--;
            } else if (preg_match('/^\s*array \(/', $line)) {
                $line = 'array(';
                $indentLevel++;
                $arrayString .= ' ' . $line;
                continue;
            }

            $arrayString .= "\n" . str_repeat('    ', $indentLevel) . $line;
        }

        return $arrayString . ";\n";
    }
}
