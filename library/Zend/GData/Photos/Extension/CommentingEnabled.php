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
 * Represents the gphoto:commentingEnabled element used by the API.
 * This class represents whether commenting is enabled for a given
 * entry.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Photos
 */
class CommentingEnabled extends \Zend\GData\Extension
{

    protected $_rootNamespace = 'gphoto';
    protected $_rootElement = 'commentingEnabled';

    /**
     * Constructs a new Zend_Gdata_Photos_Extension_CommentingEnabled object.
     *
     * @param string $text (optional) Whether commenting should be enabled
     *          or not.
     */
    public function __construct($text = null)
    {
        $this->registerAllNamespaces(\Zend\GData\Photos::$namespaces);
        parent::__construct();
        $this->setText($text);
    }

}
