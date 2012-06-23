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
 * @package    Zend_Barcode
 * @subpackage Object
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Barcode\Object;

/**
 * Class for generate Barcode
 *
 * @category   Zend
 * @package    Zend_Barcode
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Error extends AbstractObject
{
    /**
     * All texts are accepted
     * @param string $value
     * @return boolean
     */
    public function validateText($value)
    {
        return true;
    }

    /**
     * Height is forced
     * @return integer
     */
    public function getHeight($recalculate = false)
    {
        return 40;
    }

    /**
     * Width is forced
     * @return integer
     */
    public function getWidth($recalculate = false)
    {
        return 400;
    }

    /**
     * Reset precedent instructions
     * and draw the error message
     * @return array
     */
    public function draw()
    {
        $this->instructions = array();
        $this->addText('ERROR:', 10, array(5 , 18), $this->font, 0, 'left');
        $this->addText($this->text, 10, array(5 , 32), $this->font, 0, 'left');
        return $this->instructions;
    }

    /**
     * For compatibility reason
     * @return void
     */
    protected function prepareBarcode()
    {
    }

    /**
     * For compatibility reason
     * @return void
     */
    protected function checkSpecificParams()
    {
    }

    /**
     * For compatibility reason
     * @return void
     */
    protected function calculateBarcodeWidth()
    {
    }
}
