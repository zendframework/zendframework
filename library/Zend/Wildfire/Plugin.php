<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Wildfire
 */

namespace Zend\Wildfire;

/**
 * @category   Zend
 * @package    Zend_Wildfire
 * @subpackage Plugin
 */
interface Plugin
{

    /**
     * Flush any buffered data.
     *
     * @param string $protocolUri The URI of the protocol that should be flushed to
     * @return void
     */
    public function flushMessages($protocolUri);

    /**
     * Get the unique indentifier for this plugin.
     *
     * @return string Returns the URI of the plugin.
     */
    public function getUri();

}
