<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\YouTube\Extension;

/**
 * Represents the yt:noembed element used by the YouTube data API
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage YouTube
 */
class NoEmbed extends \Zend\GData\Extension
{

    protected $_rootNamespace = 'yt';
    protected $_rootElement = 'noembed';

    /**
     * Constructs a new Zend_Gdata_YouTube_Extension_VideoShare object.
     * @param bool $enabled(optional) The enabled value of the element.
     */
    public function __construct($enabled = null)
    {
        $this->registerAllNamespaces(\Zend\GData\YouTube::$namespaces);
        parent::__construct();
    }

}
