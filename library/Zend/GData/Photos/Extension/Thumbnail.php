<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\Photos\Extension;

/**
 * Represents the gphoto:thumbnail element used by the API.
 * This class represents the URI for a thumbnail image.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Photos
 */
class Thumbnail extends \Zend\GData\Extension
{

    protected $_rootNamespace = 'gphoto';
    protected $_rootElement = 'thumbnail';

    /**
     * Constructs a new Zend_Gdata_Photos_Extension_Thumbnail object.
     *
     * @param string $text (optional) The thumbnail URI to represent.
     */
    public function __construct($text = null)
    {
        $this->registerAllNamespaces(\Zend\GData\Photos::$namespaces);
        parent::__construct();
        $this->setText($text);
    }

}
