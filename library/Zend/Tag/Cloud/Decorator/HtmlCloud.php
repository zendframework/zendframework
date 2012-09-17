<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Tag
 */

namespace Zend\Tag\Cloud\Decorator;

use Zend\Escaper\Escaper;
use Zend\Tag\Exception;

/**
 * Simple HTML decorator for clouds
 *
 * @category  Zend
 * @package   Zend_Tag
 */
class HtmlCloud extends AbstractCloud
{
    /**
     * @var string Encoding to use
     */
    protected $encoding = 'UTF-8';

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * List of HTML tags
     *
     * @var array
     */
    protected $htmlTags = array(
        'ul' => array('class' => 'Zend\Tag\Cloud')
    );

    /**
     * Separator for the single tags
     *
     * @var string
     */
    protected $separator = ' ';

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
     * @param string
     * @return HTMLCloud
     */
    public function setEncoding($value)
    {
        $this->encoding = (string) $value;
        return $this;
    }

    /**
     * Set Escaper instance
     *
     * @param  Escaper $escaper
     * @return HtmlCloud
     */
    public function setEscaper($escaper)
    {
        $this->escaper = $escaper;
        return $this;
    }
    
    /**
     * Retrieve Escaper instance
     *
     * If none registered, instantiates and registers one using current encoding.
     *
     * @return Escaper
     */
    public function getEscaper()
    {
        if (null === $this->escaper) {
            $this->setEscaper(new Escaper($this->getEncoding()));
        }
        return $this->escaper;
    }

    /**
     * Set the HTML tags surrounding all tags
     *
     * @param  array $htmlTags
     * @return HTMLCloud
     */
    public function setHTMLTags(array $htmlTags)
    {
        $this->htmlTags = $htmlTags;
        return $this;
    }

    /**
     * Retrieve HTML tag map
     *
     * @return array
     */
    public function getHTMLTags()
    {
        return $this->htmlTags;
    }

    /**
     * Set the separator between the single tags
     *
     * @param  string
     * @return HTMLCloud
     */
    public function setSeparator($separator)
    {
        $this->separator = $separator;
        return $this;
    }

    /**
     * Get tag separator
     *
     * @return string
     */
    public function getSeparator()
    {
        return $this->separator;
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

        $escaper = $this->getEscaper();
        foreach ($this->getHTMLTags() as $key => $data) {
            if (is_array($data)) {
                $htmlTag    = $key;
                $this->validateElementName($htmlTag);
                $attributes = '';

                foreach ($data as $param => $value) {
                    $this->validateAttributeName($param);
                    $attributes .= ' ' . $param . '="' . $escaper->escapeHtmlAttr($value) . '"';
                }
            } else {
                $htmlTag    = $data;
                $this->validateElementName($htmlTag);
                $attributes = '';
            }

            $cloudHTML = sprintf('<%1$s%3$s>%2$s</%1$s>', $htmlTag, $cloudHTML, $attributes);
        }

        return $cloudHTML;
    }

    /**
     * Validate an HTML element name
     * 
     * @param  string $name 
     * @throws Exception\InvalidElementNameException
     */
    protected function validateElementName($name)
    {
        if (!preg_match('/^[a-z0-9]+$/i', $name)) {
            throw new Exception\InvalidElementNameException(sprintf(
                '%s: Invalid element name "%s" provided; please provide valid HTML element names',
                __METHOD__,
                $this->getEscaper()->escapeHtml($name)
            ));
        }
    }

    /**
     * Validate an HTML attribute name
     * 
     * @param  string $name 
     * @throws Exception\InvalidAttributeNameException
     */
    protected function validateAttributeName($name)
    {
        if (!preg_match('/^[a-z_:][-a-z0-9_:.]*$/i', $name)) {
            throw new Exception\InvalidAttributeNameException(sprintf(
                '%s: Invalid HTML attribute name "%s" provided; please provide valid HTML attribute names',
                __METHOD__,
                $this->getEscaper()->escapeHtml($name)
            ));
        }
    }
}
