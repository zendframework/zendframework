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
 * Represents the gphoto:access element used by the API.
 * This determines the visibility for an album, and can be either
 * the strings 'private' or 'public'.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Photos
 */
class Access extends \Zend\GData\Extension
{

    protected $_rootNamespace = 'gphoto';
    protected $_rootElement = 'access';

    /**
     * Constructs a new Zend_Gdata_Photos_Extension_Access object.
     *
     * @param string $text (optional) The value to represent.
     */
    public function __construct($text = null)
    {
        $this->registerAllNamespaces(\Zend\GData\Photos::$namespaces);
        parent::__construct();
        $this->setText($text);
    }

}
