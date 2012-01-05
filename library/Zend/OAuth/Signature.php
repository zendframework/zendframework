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
 * @package    Zend_OAuth
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\OAuth;

interface Signature
{
    /**
     * Constructor
     * 
     * @param  string $consumerSecret 
     * @param  null|string $tokenSecret 
     * @param  null|string $hashAlgo 
     * @return void
     */
    public function __construct($consumerSecret, $tokenSecret = null, $hashAlgo = null);

    /**
     * Sign a request
     * 
     * @param  array $params 
     * @param  null|string $method 
     * @param  null|string $url 
     * @return string
     */
    public function sign(array $params, $method = null, $url = null);
}
