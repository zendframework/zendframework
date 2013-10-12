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
     * @var bool
     */
    protected $useBracketArraySyntax = false;

    /**
     * processConfig(): defined by AbstractWriter.
     *
     * @param  array $config
     * @return string
     */
    public function processConfig(array $config)
    {
        $array = array(
            'open' => $this->useBracketArraySyntax ? '[' : 'array(',
            'close' => $this->useBracketArraySyntax ? ']' : ')'
        );

        return "<?php\n\n" .
               "return " . $array['open'] . "\n" . $this->processIndented($config, $array) . $array['close'] . ";\n";
    }

    /**
     * Sets whether or not to use the PHP 5.4+ "[]" array syntax.
     *
     * @param bool $value
     */
    public function setUseBracketArraySyntax($value)
    {
        $this->useBracketArraySyntax = $value;
    }

    protected function processIndented(array $config, array $array, &$indentLevel = 1)
    {
        $arrayString = "";

        foreach ($config as $key => $value) {
            $arrayString .= str_repeat('    ', $indentLevel);
            $arrayString .= (is_int($key) ? $key : "'" . addslashes($key) . "'") . ' => ';

            if (is_array($value)) {
                if ($value === array()) {
                    $arrayString .= $array['open'] . $array['close'] . ",\n";
                } else {
                    $indentLevel++;
                    $arrayString .= $array['open'] . "\n"
                                  . $this->processIndented($value, $array, $indentLevel)
                                  . str_repeat('    ', --$indentLevel) . $array['close'] . ",\n";
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
