<?php
namespace ZendBin;

/**
 * XSLT php:function()
 */
class RstConvert
{
    public static $links    = array();
    public static $footnote = array();

    private static $indentation = 0;

    public static function addIndent($indent)
    {
        self::$indentation += $indent;
    }

    public static function removeIndent($indent)
    {
        self::$indentation -= $indent;
    }

    /**
     * Wrap the text to fit with paper width
     *
     * @param  string  $text
     * @return string
     */
    public static function wrap($text)
    {
        $output = '';
        foreach (explode("\n", $text) as $line) {
            if (substr($line, 0, 1) != ' ') {
                $output .= wordwrap($line, 115 - self::$indentation);
            } else {
                $output .= $line;
            }
            $output .= "\n";
        }

        return substr($output, 0, -1);
    }

    /**
     * Indent the text 3 spaces by default
     *
     * @param  string $text
     * @return string
     */
    public static function indent($text)
    {
        $rows   = explode("\n", $text);
        $output = '';
        foreach ($rows as $row) {
            if ($row === '' || preg_match('/^\s+$/u', $row)) {
                $output .= "\n";
            } else {
                $output .= "   $row\n";
            }
        }
        return substr($output, 0, -1);
    }

    /**
     * Indent the text 2 spaces
     *
     * @param  string $text
     * @return string
     */
    public static function indent2($text)
    {
        $rows   = explode("\n", $text);
        $output = '';
        foreach ($rows as $row) {
            if ($row === '' || preg_match('/^\s+$/', $row)) {
                $output .= "\n";
            } else {
                $output .= "  $row\n";
            }
        }
        return substr($output, 0, -1);
    }

    /**
     * Indent the text 7 spaces
     *
     * @param  string $text
     * @return string
     */
    public static function indent7($text)
    {
        $rows   = explode("\n", $text);
        $output = '';
        foreach ($rows as $row) {
            if ($row === '' || preg_match('/^\s+$/', $row)) {
                $output .= "\n";
            } else {
                $output .= "       $row\n";
            }
        }
        return substr($output, 0, -1);
    }

    /**
     * Convert all the section/title, except for the first
     *
     * @param  string $text
     * @param  string $sign decorator character by default paragraph character
     * @param  bool   $top  put decorator line on top too
     * @return string
     */
    public static function title($text, $sign = '-', $top = false)
    {
        $text   = trim(self::formatText($text));
        $line   = str_repeat($sign, mb_strlen($text, INPUT_ENCODING));
        $output = $text . "\n" . $line . "\n";
        if ($top) {
            $output = $line . "\n" . $output;
        }
        return $output . "\n";
    }

    /**
     * Format the string removing \n, multiple white spaces and \ in \\
     *
     * @param  string         $text
     * @param  \DOMDocument[] $preceding previously sibling
     * @param  \DOMDocument[] $following following sibling
     * @return string
     */
    public static function formatText($text, $preceding = false, $following = false)
    {
        $hasPreceding = !empty($preceding);
        $hasFollowing = !empty($following);
        $escaped = self::escapeChars(trim(preg_replace('/\s+/mu', ' ', $text)));

        if ($hasPreceding && !in_array($preceding[0]->localName,
                                       array('variablelist', 'example', 'table', 'programlisting', 'note',
                                             'itemizedlist', 'orderedlist'))
        ) {
            if (!in_array($escaped[0],
                          array('-', '.', ',', ':', ';', '!', '?', '\\', '/', "'", '"', ')', ']', '}', '>', ' '))
            ) {
                $escaped = ' ' . $escaped;
                if (preg_match('/[^\s]/', $text[0])) {
                    $escaped = '\\' . $escaped;
                }
            }
        } else {
            // Escape characters in the bullet list or format character
            if (preg_match('/^([-\+•‣⁃]($|\s)|[_`\*\|])/', $escaped)) {
                $escaped = '\\' . $escaped;
            }
        }

        if ($hasFollowing) {
            if ($following[0]->localName == 'superscript') {
                $escaped .= '\ ';
            } elseif (!in_array(substr($escaped, -1), array('-', '/', "'", '"', '(', '[', '{', '<', ' '))) {
                // Omitted  ':' in the list
                $escaped .= ' ';
            }
        }
        return $escaped;
    }

    /**
     * Escape chars
     *
     * @param  string $text
     * @return string
     */
    public static function escapeChars($text)
    {
        // Exclude special character if preceded by any valid preceded character
        return preg_replace('/((([-:\/\'"\(\[\{<\s])([_`\*\|][^\s]))|([_][-\.,:;!?\/\\\'"\)\]\}>\s]))/S', '$3\\\$4$5',
                            str_replace('\\', '\\\\', $text));
    }

    /**
     * Escape an specific char
     *
     * @param  string $text
     * @param  string $char Char to escape
     * @return string
     */
    public static function escapeChar($text, $char)
    {
        return preg_replace(sprintf('/([^\s])(\\%s[^-\.,:;!?\/\\\'"\)\]\}>\s])/', $char), '$1\\\$2', $text);
    }

    /**
     * Convert the link tag
     *
     * @param  \DOMElement $node
     * @return string
     */
    public static function link($node)
    {
        $value = trim(self::formatText($node[0]->nodeValue));
        if ($node[0]->getAttribute('linkend')) {
            return ":ref:`$value <" . $node[0]->getAttribute('linkend') . ">`";
        } else {
            self::$links[$value] = trim($node[0]->getAttribute('xlink:href'));
            return "`$value`_";
        }
    }

    /**
     * Convert the footnote
     *
     * @param  \DOMElement $value
     * @return string
     */
    public static function footnote($value)
    {
        self::$footnote[] = '.. [#] ' . trim($value);
        return '[#]_';
    }

    /**
     * Get all the external links of the document
     *
     * @return string
     */
    public static function getLinks()
    {
        $output = '';
        foreach (self::$links as $key => $value) {
            $output .= ".. _`$key`: $value\n";
        }
        return $output;
    }

    /**
     * Convert the table tag
     *
     * @param  \DOMElement $node
     * @return string
     */
    public static function table($node)
    {
        // check if thead exists
        $head = (0 !== $node[0]->getElementsByTagName('thead')->length);

        $rows   = $node[0]->getElementsByTagName('row');
        $table  = array();
        $totRow = $rows->length;
        $j      = 0;
        foreach ($rows as $row) {
            $cols   = $row->getElementsByTagName('entry');
            $totCol = $cols->length;
            if (!isset($widthCol)) {
                $widthCol = array_fill(0, $totCol, 0);
            }
            $i = 0;
            foreach ($cols as $col) {
                $table[$j][$i] = self::formatText($col->nodeValue);
                $length        = mb_strlen($table[$j][$i], INPUT_ENCODING);
                if ($length > $widthCol[$i]) {
                    $widthCol[$i] = $length;
                }
                $i++;
            }
            $j++;
        }
        $tableText = new Table\Table(array(
                                          'columnWidths' => $widthCol,
                                          'decorator'    => 'ascii'
                                     ));
        for ($j = 0; $j < $totRow; $j++) {
            $row = new Table\Row();
            for ($i = 0; $i < $totCol; $i++) {
                $row->appendColumn(new Table\Column($table[$j][$i]));
            }
            $tableText->appendRow($row);
        }
        $output = $tableText->render();
        // if thead exists change the table style with head (= instead of -)
        if ($head) {
            $table     = explode("\n", $output);
            $newOutput = '';
            $i         = 0;
            foreach ($table as $row) {
                if ('+-' === substr($row, 0, 2)) {
                    $i++;
                }
                if (2 === $i) {
                    $row = str_replace('-', '=', $row);
                }
                $newOutput .= "$row\n";
            }
            return $newOutput;
        }
        return $output . "\n";
    }

    /**
     * Convert an XML file name to the RST ZF2 standard naming convention
     * For instance, Zend_Config-XmlIntro.xml become zend.config.xml-intro.rst
     *
     * @param  string $name
     * @return string
     */
    public static function xmlFileNameToRst($name)
    {
        if ('.xml' === strtolower(substr($name, -4))) {
            $name = substr($name, 0, strlen($name) - 4);
        }
        $tot    = strlen($name);
        $output = '';
        $word   = false;
        for ($i = 0; $i < $tot; $i++) {

            if (preg_match('/[A-Z]/', $name[$i])) {
                if ($word) {
                    $output .= '-';
                }
                $output .= strtolower($name[$i]);
            } elseif ('_' === $name[$i] || '-' === $name[$i]) {
                $output .= '.';
                $word = false;
            } else {
                $output .= $name[$i];
                $word = true;
            }
        }
        return $output . '.rst';
    }

    /**
     * Convert an XML file name to the RST ZF2 standard naming convention
     * For instance, Zend_Config-XmlIntro.xml become zend.config.xml-intro.rst
     *
     * @param  string $href
     * @return string
     */
    public static function imageFileName($href)
    {
        return '../images/' . basename($href);
    }
}
