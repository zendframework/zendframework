<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Dom\Document;

use Zend\Dom\DOMXPath;
use Zend\Dom\Document;

/**
 * Query object executable in a Zend\Dom\Document
 */
class Query
{
    /**#@+
     * Query types
     */
    const TYPE_XPATH  = 'TYPE_XPATH';
    const TYPE_CSS    = 'TYPE_CSS';
    /**#@-*/

    /**
     * Perform the query on Document
     *
     * @param  string    $expression CSS selector or XPath query
     * @param  Document  $document   Document to query
     * @param  string    $type       The type of $expression
     * @return NodeList
     */
    public static function execute($expression, Document $document, $type = self::TYPE_XPATH)
    {
        // Expression check
        if ($type === static::TYPE_CSS) {
            $expression = static::cssToXpath($expression);
        }
        $xpath = new DOMXPath($document->getDomDocument());

        $xpathNamespaces = $document->getXpathNamespaces();
        foreach ($xpathNamespaces as $prefix => $namespaceUri) {
            $xpath->registerNamespace($prefix, $namespaceUri);
        }

        if ($xpathPhpfunctions = $document->getXpathPhpFunctions()) {
            $xpath->registerNamespace('php', 'http://php.net/xpath');
            ($xpathPhpfunctions === true) ? $xpath->registerPHPFunctions() : $xpath->registerPHPFunctions($xpathPhpfunctions);
        }

        $nodeList = $xpath->queryWithErrorException($expression);
        return new NodeList($nodeList);
    }

    /**
     * Transform CSS expression to XPath
     *
     * @param  string $path
     * @return string
     */
    public static function cssToXpath($path)
    {
        $path = (string) $path;
        if (strstr($path, ',')) {
            $paths       = explode(',', $path);
            $expressions = array();
            foreach ($paths as $path) {
                $xpath = static::cssToXpath(trim($path));
                if (is_string($xpath)) {
                    $expressions[] = $xpath;
                } elseif (is_array($xpath)) {
                    $expressions = array_merge($expressions, $xpath);
                }
            }
            return implode('|', $expressions);
        }

        $paths    = array('//');
        $path     = preg_replace('|\s+>\s+|', '>', $path);
        $segments = preg_split('/\s+/', $path);
        foreach ($segments as $key => $segment) {
            $pathSegment = static::_tokenize($segment);
            if (0 == $key) {
                if (0 === strpos($pathSegment, '[contains(')) {
                    $paths[0] .= '*' . ltrim($pathSegment, '*');
                } else {
                    $paths[0] .= $pathSegment;
                }
                continue;
            }
            if (0 === strpos($pathSegment, '[contains(')) {
                foreach ($paths as $pathKey => $xpath) {
                    $paths[$pathKey] .= '//*' . ltrim($pathSegment, '*');
                    $paths[]      = $xpath . $pathSegment;
                }
            } else {
                foreach ($paths as $pathKey => $xpath) {
                    $paths[$pathKey] .= '//' . $pathSegment;
                }
            }
        }

        if (1 == count($paths)) {
            return $paths[0];
        }
        return implode('|', $paths);
    }

    /**
     * Tokenize CSS expressions to XPath
     *
     * @param  string $expression
     * @return string
     */
    protected static function _tokenize($expression)
    {
        // Child selectors
        $expression = str_replace('>', '/', $expression);

        // IDs
        $expression = preg_replace('|#([a-z][a-z0-9_-]*)|i', '[@id=\'$1\']', $expression);
        $expression = preg_replace('|(?<![a-z0-9_-])(\[@id=)|i', '*$1', $expression);

        // arbitrary attribute strict equality
        $expression = preg_replace_callback(
            '|\[@?([a-z0-9_-]+)=[\'"]([^\'"]+)[\'"]\]|i',
            function ($matches) {
                return '[@' . strtolower($matches[1]) . "='" . $matches[2] . "']";
            },
            $expression
        );

        // arbitrary attribute contains full word
        $expression = preg_replace_callback(
            '|\[([a-z0-9_-]+)~=[\'"]([^\'"]+)[\'"]\]|i',
            function ($matches) {
                return "[contains(concat(' ', normalize-space(@" . strtolower($matches[1]) . "), ' '), ' "
                     . $matches[2] . " ')]";
            },
            $expression
        );

        // arbitrary attribute contains specified content
        $expression = preg_replace_callback(
            '|\[([a-z0-9_-]+)\*=[\'"]([^\'"]+)[\'"]\]|i',
            function ($matches) {
                return "[contains(@" . strtolower($matches[1]) . ", '"
                     . $matches[2] . "')]";
            },
            $expression
        );

        // Classes
        if(false === strpos($expression, "[@")) {
            $expression = preg_replace(
                '|\.([a-z][a-z0-9_-]*)|i',
                "[contains(concat(' ', normalize-space(@class), ' '), ' \$1 ')]",
                $expression
            );
        }

        /** ZF-9764 -- remove double asterisk */
        $expression = str_replace('**', '*', $expression);

        return $expression;
    }
}
