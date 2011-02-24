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
 * @package    Zend_PDF
 * @subpackage FileParser
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Pdf\BinaryParser\Font\OpenType;
use Zend\Pdf\Exception;
use Zend\Pdf;

/**
 * Parses an OpenType font file containing TrueType outlines.
 *
 * @uses       \Zend\Pdf\Exception
 * @uses       \Zend\Pdf\BinaryParser\Font\OpenType\AbstractOpenType
 * @uses       \Zend\Pdf\Font
 * @package    Zend_PDF
 * @subpackage FileParser
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class TrueType extends AbstractOpenType
{
    /**** Public Interface ****/


    /* Concrete Class Implementation */

    /**
     * Verifies that the font file actually contains TrueType outlines.
     *
     * @throws \Zend\Pdf\Exception
     */
    public function screen()
    {
        if ($this->_isScreened) {
            return;
        }

        parent::screen();

        switch ($this->_readScalerType()) {
            case 0x00010000:    // version 1.0 - Windows TrueType signature
                break;

            case 0x74727565:    // 'true' - Macintosh TrueType signature
                break;

            default:
                throw new Exception\UnrecognizedFontException('Not a TrueType font file');
        }

        $this->fontType = Pdf\Font::TYPE_TRUETYPE;
        $this->_isScreened = true;
    }

    /**
     * Reads and parses the TrueType font data from the file on disk.
     */
    public function parse()
    {
        if ($this->_isParsed) {
            return;
        }

        parent::parse();

        /* There is nothing additional to parse for TrueType fonts at this time.
         */

        $this->_isParsed = true;
    }
}
