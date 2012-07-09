<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\EXIF\Extension;

/**
 * Represents the exif:fStop element used by the Gdata Exif extensions.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Exif
 */
class FStop extends \Zend\GData\Extension
{

    protected $_rootNamespace = 'exif';
    protected $_rootElement = 'fstop';

    /**
     * Constructs a new Zend_Gdata_Exif_Extension_FStop object.
     *
     * @param string $text (optional) The value to use for this element.
     */
    public function __construct($text = null)
    {
        $this->registerAllNamespaces(\Zend\GData\EXIF::$namespaces);
        parent::__construct();
        $this->setText($text);
    }

}
