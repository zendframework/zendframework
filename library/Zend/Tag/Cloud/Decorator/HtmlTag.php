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

/**
 * @namespace
 */
namespace Zend\Tag\Cloud\Decorator;

use Zend\Tag\Cloud\Decorator\Exception\InvalidArgumentException,
    Zend\Tag\ItemList;

/**
 * Simple HTML decorator for tags
 *
 * @uses      \Zend\Tag\Cloud\Decorator\Exception\InvalidArgumentException
 * @uses      \Zend\Tag\Cloud\Decorator\Tag
 * @category  Zend
 * @package   Zend_Tag
 * @uses      \Zend\Tag\Cloud\Decorator\Tag
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class HtmlTag extends Tag
{
    /**
     * List of tags which get assigned to the inner element instead of
     * font-sizes.
     *
     * @var array
     */
    protected $_classList = null;

    /**
     * @var string Encoding to utilize
     */
    protected $_encoding = 'UTF-8';

    /**
     * Unit for the fontsize
     *
     * @var string
     */
    protected $_fontSizeUnit = 'px';

    /**
     * Allowed fontsize units
     *
     * @var array
     */
    protected $_alloweFontSizeUnits = array('em', 'ex', 'px', 'in', 'cm', 'mm', 'pt', 'pc', '%');

    /**
     * List of HTML tags
     *
     * @var array
     */
    protected $_htmlTags = array(
        'li'
    );

    /**
     * Maximum fontsize
     *
     * @var integer
     */
    protected $_maxFontSize = 20;

    /**
     * Minimum fontsize
     *
     * @var integer
     */
    protected $_minFontSize = 10;

    /**
     * Set a list of classes to use instead of fontsizes
     *
     * @param  array $classList
     * @throws \Zend\Tag\Cloud\Decorator\Exception\InvalidArgumentException When the classlist is empty
     * @throws \Zend\Tag\Cloud\Decorator\Exception\InvalidArgumentException When the classlist contains an invalid classname
     * @return \Zend\Tag\Cloud\Decorator\HTMLTag
     */
    public function setClassList(array $classList = null)
    {
        if (is_array($classList)) {
            if (count($classList) === 0) {
                throw new InvalidArgumentException('Classlist is empty');
            }

            foreach ($classList as $class) {
                if (!is_string($class)) {
                    throw new InvalidArgumentException('Classlist contains an invalid classname');
                }
            }
        }

        $this->_classList = $classList;
        return $this;
    }

    /**
     * Get class list
     *
     * @return array
     */
    public function getClassList()
    {
        return $this->_classList;
    }

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
     * @param  string $value
     * @return \Zend\Tag\Cloud\Decorator\HTMLTag
     */
    public function setEncoding($value)
    {
        $this->_encoding = (string) $value;
        return $this;
    }

    /**
     * Set the font size unit
     *
     * Possible values are: em, ex, px, in, cm, mm, pt, pc and %
     *
     * @param  string $fontSizeUnit
     * @throws \Zend\Tag\Cloud\Decorator\Exception\InvalidArgumentException When an invalid fontsize unit is specified
     * @return \Zend\Tag\Cloud\Decorator\HTMLTag
     */
    public function setFontSizeUnit($fontSizeUnit)
    {
        if (!in_array($fontSizeUnit, $this->_alloweFontSizeUnits)) {
            throw new InvalidArgumentException('Invalid fontsize unit specified');
        }

        $this->_fontSizeUnit = (string) $fontSizeUnit;
        $this->setClassList(null);
        return $this;
    }

    /**
     * Retrieve font size unit
     *
     * @return string
     */
    public function getFontSizeUnit()
    {
        return $this->_fontSizeUnit;
    }
     /**
     * Set the HTML tags surrounding the <a> element
     *
     * @param  array $htmlTags
     * @return \Zend\Tag\Cloud\Decorator\HTMLTag
     */
    public function setHTMLTags(array $htmlTags)
    {
        $this->_htmlTags = $htmlTags;
        return $this;
    }

    /**
     * Get HTML tags map
     *
     * @return array
     */
    public function getHTMLTags()
    {
        return $this->_htmlTags;
    }

    /**
     * Set maximum font size
     *
     * @param  integer $maxFontSize
     * @throws \Zend\Tag\Cloud\Decorator\Exception\InvalidArgumentException When fontsize is not numeric
     * @return \Zend\Tag\Cloud\Decorator\HTMLTag
     */
    public function setMaxFontSize($maxFontSize)
    {
        if (!is_numeric($maxFontSize)) {
            throw new InvalidArgumentException('Fontsize must be numeric');
        }

        $this->_maxFontSize = (int) $maxFontSize;
        $this->setClassList(null);
        return $this;
    }

    /**
     * Retrieve maximum font size
     *
     * @return int
     */
    public function getMaxFontSize()
    {
        return $this->_maxFontSize;
    }

    /**
     * Set minimum font size
     *
     * @param  int $minFontSize
     * @throws \Zend\Tag\Cloud\Decorator\Exception\InvalidArgumentException When fontsize is not numeric
     * @return \Zend\Tag\Cloud\Decorator\HTMLTag
     */
    public function setMinFontSize($minFontSize)
    {
        if (!is_numeric($minFontSize)) {
            throw new InvalidArgumentException('Fontsize must be numeric');
        }

        $this->_minFontSize = (int) $minFontSize;
        $this->setClassList(null);
        return $this;
    }

    /**
     * Retrieve minimum font size
     *
     * @return int
     */
    public function getMinFontSize()
    {
        return $this->_minFontSize;
    }

    /**
     * Defined by Zend\Tag\Cloud\Decorator\Tag
     *
     * @param  \Zend\Tag\ItemList $tags
     * @return array
     */
    public function render($tags)
    {
        if (!$tags instanceof ItemList) {
            throw new Exception(sprintf(
                'HtmlTag::render() expects a Zend\Tag\ItemList argument; received "%s"',
                (is_object($tags) ? get_class($tags) : gettype($tags))
            ));
        }
        if (null === ($weightValues = $this->getClassList())) {
            $weightValues = range($this->getMinFontSize(), $this->getMaxFontSize());
        }

        $tags->spreadWeightValues($weightValues);

        $result = array();

        $enc = $this->getEncoding();
        foreach ($tags as $tag) {
            if (null === ($classList = $this->getClassList())) {
                $attribute = sprintf('style="font-size: %d%s;"', $tag->getParam('weightValue'), $this->getFontSizeUnit());
            } else {
                $attribute = sprintf('class="%s"', htmlspecialchars($tag->getParam('weightValue'), ENT_COMPAT, $enc));
            }

            $tagHTML = sprintf('<a href="%s" %s>%s</a>', htmlSpecialChars($tag->getParam('url'), ENT_COMPAT, $enc), $attribute, $tag->getTitle());

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

                $tagHTML = sprintf('<%1$s%3$s>%2$s</%1$s>', $htmlTag, $tagHTML, $attributes);
            }

            $result[] = $tagHTML;
        }

        return $result;
    }
}
