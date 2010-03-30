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
 * @package    Zend_Cache
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Cache;

/**
 * @package    Zend_Cache
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Frontend
{
    /**
     * Set a frontend option
     * 
     * @param  string $name 
     * @param  mixed $value 
     * @return void
     */
    public function setOption($name, $value);

    /**
     * Retrieve an option value
     * 
     * @param  string $name 
     * @return mixed
     */
    public function getOption($name);

    /**
     * Set cache lifetime
     * 
     * @param  int $newLifetime 
     * @return void
     */
    public function setLifetime($newLifetime);

    /**
     * Set the cache backend
     * 
     * @param  Backend $backendObject 
     * @return void
     */
    public function setBackend(Backend $backendObject);

    /**
     * Retrieve the cache backend
     * 
     * @return Backend
     */
    public function getBackend();

    /**
     * Load a cached item
     * 
     * @param  string $id 
     * @param  bool $doNotTestCacheValidity 
     * @param  bool $doNotUnserialize 
     * @return mixed
     */
    public function load($id, $doNotTestCacheValidity = false, $doNotUnserialize = false);

    /**
     * Test if a cache exists for a given identifier
     * 
     * @param  string $id 
     * @return bool
     */
    public function test($id);

    /**
     * Remove a cached item
     * 
     * @param  string $id 
     * @return void
     */
    public function remove($id);

    /**
     * Clean the cache of multiple or all items
     * 
     * @param  string $mode 
     * @param  array $tags 
     * @return void
     */
    public function clean($mode = 'all', $tags = array());

    /**
     * Retrieve all cache identifiers matching ALL the given tags
     * 
     * @param  array $tags 
     * @return array
     */
    public function getIdsMatchingTags($tags = array());

    /**
     * Get cache identifiers matching NONE of the given tags
     * 
     * @param  array $tags 
     * @return array
     */
    public function getIdsNotMatchingTags($tags = array());

    /**
     * Get cache identifiers matching ANY of the given tags
     * 
     * @param  array $tags 
     * @return array
     */
    public function getIdsMatchingAnyTags($tags = array());

    /**
     * Get all cache identifiers
     * 
     * @return array
     */
    public function getIds();

    /**
     * Get all tags
     * 
     * @return array
     */
    public function getTags();

    /**
     * Retrieve the filling percentage of the backend storage
     * 
     * @return int
     */
    public function getFillingPercentage();

    /**
     * Retrieve all metadata for a given cache identifier
     * 
     * @param  string $id
     * @return array
     */
    public function getMetadatas($id);

    /**
     * Extend the lifetime of a given cache identifier
     * 
     * @param  string $id 
     * @param  int $extraLifetime 
     * @return bool
     */
    public function touch($id, $extraLifetime);
}
