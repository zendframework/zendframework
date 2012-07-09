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
 * Represents the gphoto:albumid element used by the API. This
 * class represents the ID of an album and is usually contained
 * within an instance of Zend_Gdata_Photos_AlbumEntry or CommentEntry.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Photos
 */
class AlbumId extends \Zend\GData\Extension
{

    protected $_rootNamespace = 'gphoto';
    protected $_rootElement = 'albumid';

    /**
     * Constructs a new Zend_Gdata_Photos_Extension_AlbumId object.
     *
     * @param string $text (optional) The value to use for the Album ID.
     */
    public function __construct($text = null)
    {
        $this->registerAllNamespaces(\Zend\GData\Photos::$namespaces);
        parent::__construct();
        $this->setText($text);
    }

}
