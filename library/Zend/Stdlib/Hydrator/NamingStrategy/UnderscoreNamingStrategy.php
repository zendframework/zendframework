<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib\Hydrator\NamingStrategy;

/**
 * Class UnderscoreNamingStrategy
 * @package Zend\Stdlib\Hydrator\NamingStrategy
 */
class UnderscoreNamingStrategy implements NamingStrategyInterface
{
    /**
     * Remove underscores and capitalize letters
     *
     * @param  string $name
     * @return string
     */
    public function hydrate($name)
    {
        return preg_replace_callback('/(_[a-z])/i', function($letters) {
            $letter = substr(array_shift($letters), 1, 1);

            return ucfirst($letter);
        }, $name);
    }

    /**
     * Remove capitalized letters and prepend underscores.
     *
     * @param  string $name
     * @return string
     */
    public function extract($name)
    {
        return preg_replace_callback('/([A-Z])/', function($letters) {
            $letter = array_shift($letters);

            return '_' . strtolower($letter);
        }, $name);
    }
}