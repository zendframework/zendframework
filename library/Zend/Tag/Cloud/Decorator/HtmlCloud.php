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
 * @package    Zend_Tag
 * @subpackage Cloud
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Tag\Cloud\Decorator;

/**
 * Simple HTML decorator for clouds
 *
 * @category  Zend
 * @package   Zend_Tag
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class HtmlCloud extends AbstractCloud
{
    /**
     * @var string Encoding to use
     */
    protected $_encoding = 'UTF-8';

    /**
     * List of HTML tags
     *
     * @var array
     */
    protected $_htmlTags = array(
        'ul' => array('class' => 'Zend\Tag\Cloud')
    );

    /**
     * Separator for the single tags
     *
     * @var string
     */
    protected $_separator = ' ';

    /**
     * Get encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->_encoding;
    }

    /**
     * Set encoding
     *
     * @param string
     * @return HTMLCloud
     */
    public function setEncoding($value)
    {
        $this->_encoding = (string) $value;
        return $this;
    }

    /**
     * Set the HTML tags surrounding all tags
     *
     * @param  array $htmlTags
     * @return HTMLCloud
     */
    public function setHTMLTags(array $htmlTags)
    {
        $this->_htmlTags = $htmlTags;
        return $this;
    }

    /**
     * Retrieve HTML tag map
     *
     * @return array
     */
    public function getHTMLTags()
    {
        return $this->_htmlTags;
    }

    /**
     * Set the separator between the single tags
     *
     * @param  string
     * @return HTMLCloud
     */
    public function setSeparator($separator)
    {
        $this->_separator = $separator;
        return $this;
    }

    /**
     * Get tag separator
     *
     * @return string
     */
    public function getSeparator()
    {
        return $this->_separator;
    }

    /**
     * Defined by Zend\Tag\Cloud\Decorator\Cloud
     *
     * @param  array $tags
     * @throws Exception\InvalidArgumentException
     * @return string
     */
    public function render($tags)
    {
        if (!is_array($tags)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'HtmlCloud::render() expects an array argument; received "%s"',
                (is_object($tags) ? get_class($tags) : gettype($tags))
            ));
        }
        $cloudHTML = implode($this->getSeparator(), $tags);

        $enc = $this->getEncoding();
        foreach ($this->getHTMLTags() as $key => $data) {
            if (is_array($data)) {
                $htmlTag    = $key;
                $attributes = '';

                foreach ($data as $param => $value) {
                    $attributes .= ' ' . $param . '="' . htmlspecialchars($value, ENT_COMPAT, $enc) . '"';
                }
            } else {
                $htmlTag    = $data;
                $attributes = '';
            }

            $cloudHTML = sprintf('<%1$s%3$s>%2$s</%1$s>', $htmlTag, $cloudHTML, $attributes);
        }

        return $cloudHTML;
    }
}
