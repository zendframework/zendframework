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
 * Represents the yt:occupation element
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage YouTube
 */
class Occupation extends \Zend\GData\Extension
{

    protected $_rootElement = 'occupation';
    protected $_rootNamespace = 'yt';

    public function __construct($text = null)
    {
        $this->registerAllNamespaces(\Zend\GData\YouTube::$namespaces);
        parent::__construct();
        $this->_text = $text;
    }

}
