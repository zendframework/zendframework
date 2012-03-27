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
 * @package    Zend_EventManager
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\EventManager;

use Zend\Stdlib\CallbackHandler;

/**
 * Interface for intercepting filter chains
 *
 * @category   Zend
 * @package    Zend_EventManager
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Filter
{
    /**
     * Execute the filter chain
     * 
     * @param  string|object $context 
     * @param  array $params 
     * @return mixed
     */
    public function run($context, array $params = array());

    /**
     * Attach an intercepting filter
     * 
     * @param  callback $callback 
     * @return CallbackHandler
     */
    public function attach($callback);

    /**
     * Detach an intercepting filter
     * 
     * @param  CallbackHandler $filter 
     * @return bool
     */
    public function detach(CallbackHandler $filter);

    /**
     * Get all intercepting filters
     * 
     * @return array
     */
    public function getFilters();

    /**
     * Clear all filters
     * 
     * @return void
     */
    public function clearFilters();

    /**
     * Get all filter responses
     * 
     * @return ResponseCollection
     */
    public function getResponses();
}
