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
 * @category  Zend
 * @package   Zend_Text_Table
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Text\Table;
use Zend\Text;

/**
 * Column class for Zend_Text_Table_Row
 *
 * @uses      \Zend\Text\MultiByte
 * @uses      \Zend\Text\Table\Table
 * @uses      \Zend\Text\Table\Exception
 * @category  Zend
 * @package   Zend_Text_Table
 * @copyright Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Column
{
    /**
     * Aligns for columns
     */
    const ALIGN_LEFT   = 'left';
    const ALIGN_CENTER = 'center';
    const ALIGN_RIGHT  = 'right';

    /**
     * Content of the column
     *
     * @var string
     */
    protected $_content = '';

    /**
     * Align of the column
     *
     * @var string
     */
    protected $_align = self::ALIGN_LEFT;

    /**
     * Colspan of the column
     *
     * @var integer
     */
    protected $_colSpan = 1;

    /**
     * Allowed align parameters
     *
     * @var array
     */
    protected $_allowedAligns = array(self::ALIGN_LEFT, self::ALIGN_CENTER, self::ALIGN_RIGHT);

    /**
     * Create a column for a Zend_Text_Table_Row object.
     *
     * @param string  $content  The content of the column
     * @param string  $align    The align of the content
     * @param integer $colSpan  The colspan of the column
     * @param string  $charset  The encoding of the content
     */
    public function __construct($content = null, $align = null, $colSpan = null, $charset = null)
    {
        if ($content !== null) {
            $this->setContent($content, $charset);
        }

        if ($align !== null) {
            $this->setAlign($align);
        }

        if ($colSpan !== null) {
            $this->setColSpan($colSpan);
        }
    }

    /**
     * Set the content.
     *
     * If $charset is not defined, it is assumed that $content is encoded in
     * the charset defined via Zend_Text_Table::setInputCharset() (defaults
     * to utf-8).
     *
     * @param  string $content  Content of the column
     * @param  string $charset  The charset of the content
     * @throws \Zend\Text\Table\Exception\UnexpectedValueException When $content is not a string
     * @return \Zend\Text\Table\Column
     */
    public function setContent($content, $charset = null)
    {
        if (is_string($content) === false) {
            throw new Exception\UnexpectedValueException('$content must be a string');
        }

        if ($charset === null) {
            $inputCharset = Table::getInputCharset();
        } else {
            $inputCharset = strtolower($charset);
        }

        $outputCharset = Table::getOutputCharset();

        if ($inputCharset !== $outputCharset) {
            if (PHP_OS !== 'AIX') {
                // AIX does not understand these character sets
                $content = iconv($inputCharset, $outputCharset, $content);
            }

        }

        $this->_content = $content;

        return $this;
    }

    /**
     * Set the align
     *
     * @param  string $align Align of the column
     * @throws \Zend\Text\Table\Exception\OutOfBoundsException When supplied align is invalid
     * @return \Zend\Text\Table\Column
     */
    public function setAlign($align)
    {
        if (in_array($align, $this->_allowedAligns) === false) {
            throw new Exception\OutOfBoundsException('Invalid align supplied');
        }

        $this->_align = $align;

        return $this;
    }

    /**
     * Set the colspan
     *
     * @param  int $colSpan
     * @throws \Zend\Text\Table\Exception\InvalidArgumentException When $colSpan is smaller than 1
     * @return \Zend\Text\Table\Column
     */
    public function setColSpan($colSpan)
    {
        if (is_int($colSpan) === false or $colSpan < 1) {
            throw new Exception\InvalidArgumentException('$colSpan must be an integer and greater than 0');
        }

        $this->_colSpan = $colSpan;

        return $this;
    }

    /**
     * Get the colspan
     *
     * @return integer
     */
    public function getColSpan()
    {
        return $this->_colSpan;
    }

    /**
     * Render the column width the given column width
     *
     * @param  integer $columnWidth The width of the column
     * @param  integer $padding     The padding for the column
     * @throws \Zend\Text\Table\Exception\InvalidArgumentException When $columnWidth is lower than 1
     * @throws \Zend\Text\Table\Exception\OutOfBoundsException When padding is greater than columnWidth
     * @return string
     */
    public function render($columnWidth, $padding = 0)
    {
        if (is_int($columnWidth) === false or $columnWidth < 1) {
            throw new Exception\InvalidArgumentException('$columnWidth must be an integer and greater than 0');
        }

        $columnWidth -= ($padding * 2);

        if ($columnWidth < 1) {
            throw new Exception\OutOfBoundsException('Padding (' . $padding . ') is greater than column width');
        }

        switch ($this->_align) {
            case self::ALIGN_LEFT:
                $padMode = STR_PAD_RIGHT;
                break;

            case self::ALIGN_CENTER:
                $padMode = STR_PAD_BOTH;
                break;

            case self::ALIGN_RIGHT:
                $padMode = STR_PAD_LEFT;
                break;

            default:
                // This can never happen, but the CS tells I have to have it ...
                break;
        }

        $outputCharset = Table::getOutputCharset();
        $lines         = explode("\n", Text\MultiByte::wordWrap($this->_content, $columnWidth, "\n", true, $outputCharset));
        $paddedLines   = array();

        foreach ($lines AS $line) {
            $paddedLines[] = str_repeat(' ', $padding)
                           . Text\MultiByte::strPad($line, $columnWidth, ' ', $padMode, $outputCharset)
                           . str_repeat(' ', $padding);
        }

        $result = implode("\n", $paddedLines);

        return $result;
    }
}
