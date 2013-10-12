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
     * @var string
     */
    const INDENT_STRING = '    ';

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
        $arraySyntax = array(
            'open' => $this->useBracketArraySyntax ? '[' : 'array(',
            'close' => $this->useBracketArraySyntax ? ']' : ')'
        );

        return "<?php\n\n" .
               "return " . $arraySyntax['open'] . "\n" . $this->processIndented($config, $arraySyntax) .
               $arraySyntax['close'] . ";\n";
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

    /**
     * Recursively processes a PHP config array structure into a readable format.
     *
     * @param array $config
     * @param array $arraySyntax
     * @param int $indentLevel
     * @return string
     */
    protected function processIndented(array $config, array $arraySyntax, &$indentLevel = 1)
    {
        $arrayString = "";

        foreach ($config as $key => $value) {
            $arrayString .= str_repeat(self::INDENT_STRING, $indentLevel);
            $arrayString .= (is_int($key) ? $key : "'" . addslashes($key) . "'") . ' => ';

            if (is_array($value)) {
                if ($value === array()) {
                    $arrayString .= $arraySyntax['open'] . $arraySyntax['close'] . ",\n";
                } else {
                    $indentLevel++;
                    $arrayString .= $arraySyntax['open'] . "\n"
                                  . $this->processIndented($value, $arraySyntax, $indentLevel)
                                  . str_repeat(self::INDENT_STRING, --$indentLevel) . $arraySyntax['close'] . ",\n";
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
