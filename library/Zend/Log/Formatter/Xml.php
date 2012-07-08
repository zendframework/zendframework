<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Formatter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Log\Formatter;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use DOMDocument;
use DOMElement;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Formatter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Xml implements FormatterInterface
{
    /**
     * @var string Name of root element
     */
    protected $rootElement;

    /**
     * @var array Relates XML elements to log data field keys.
     */
    protected $elementMap;

    /**
     * @var string Encoding to use in XML
     */
    protected $encoding;

    /**
     * Class constructor
     * (the default encoding is UTF-8)
     *
     * @param array|Traversable $options
     * @return Xml
     */
    public function __construct($options = array())
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (!is_array($options)) {
            $args = func_get_args();

            $options = array(
                'rootElement' => array_shift($args)
            );

            if (count($args)) {
                $options['elementMap'] = array_shift($args);
            }

            if (count($args)) {
                $options['encoding'] = array_shift($args);
            }
        }

        if (!array_key_exists('rootElement', $options)) {
            $options['rootElement'] = 'logEntry';
        }

        if (!array_key_exists('encoding', $options)) {
            $options['encoding'] = 'UTF-8';
        }

        $this->rootElement = $options['rootElement'];
        $this->setEncoding($options['encoding']);

        if (array_key_exists('elementMap', $options)) {
            $this->elementMap  = $options['elementMap'];
        }
    }

    /**
     * Get encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Set encoding
     *
     * @param string $value
     * @return Xml
     */
    public function setEncoding($value)
    {
        $this->encoding = (string) $value;
        return $this;
    }

    /**
     * Formats data into a single line to be written by the writer.
     *
     * @param array $event event data
     * @return string formatted line to write to the log
     */
    public function format($event)
    {
        if ($this->elementMap === null) {
            $dataToInsert = $event;
        } else {
            $dataToInsert = array();
            foreach ($this->elementMap as $elementName => $fieldKey) {
                $dataToInsert[$elementName] = $event[$fieldKey];
            }
        }

        $enc = $this->getEncoding();
        $dom = new DOMDocument('1.0', $enc);
        $elt = $dom->appendChild(new DOMElement($this->rootElement));

        foreach ($dataToInsert as $key => $value) {
            if (empty($value)
                || is_scalar($value)
                || (is_object($value) && method_exists($value,'__toString'))
            ) {
                if ($key == "message") {
                    $value = htmlspecialchars($value, ENT_COMPAT, $enc);
                }
                $elt->appendChild(new DOMElement($key, (string)$value));
            }
        }

        $xml = $dom->saveXML();
        $xml = preg_replace('/<\?xml version="1.0"( encoding="[^\"]*")?\?>\n/u', '', $xml);

        return $xml . PHP_EOL;
    }
}
