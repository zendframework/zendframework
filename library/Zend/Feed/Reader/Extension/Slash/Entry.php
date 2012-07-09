<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Feed
 */

namespace Zend\Feed\Reader\Extension\Slash;
use Zend\Feed\Reader\Extension;

/**
* @category Zend
* @package Zend_Feed_Reader
*/
class Entry extends Extension\AbstractEntry
{
    /**
     * Get the entry section
     *
     * @return string|null
     */
    public function getSection()
    {
        return $this->_getData('section');
    }

    /**
     * Get the entry department
     *
     * @return string|null
     */
    public function getDepartment()
    {
        return $this->_getData('department');
    }

    /**
     * Get the entry hit_parade
     *
     * @return array
     */
    public function getHitParade()
    {
        $name = 'hit_parade';

        if (isset($this->_data[$name])) {
            return $this->_data[$name];
        }

        $stringParade = $this->_getData($name);
        $hitParade    = array();

        if (!empty($stringParade)) {
            $stringParade = explode(',', $stringParade);

            foreach ($stringParade as $hit)
                $hitParade[] = $hit + 0; //cast to integer
        }

        $this->_data[$name] = $hitParade;
        return $hitParade;
    }

    /**
     * Get the entry comments
     *
     * @return int
     */
    public function getCommentCount()
    {
        $name = 'comments';

        if (isset($this->_data[$name])) {
            return $this->_data[$name];
        }

        $comments = $this->_getData($name, 'string');

        if (!$comments) {
            $this->_data[$name] = null;
            return $this->_data[$name];
        }

        return $comments;
    }

    /**
     * Get the entry data specified by name
     * @param string $name
     * @param string $type
     *
     * @return mixed|null
     */
    protected function _getData($name, $type = 'string')
    {
        if (array_key_exists($name, $this->_data)) {
            return $this->_data[$name];
        }

        $data = $this->_xpath->evaluate($type . '(' . $this->getXpathPrefix() . '/slash10:' . $name . ')');

        if (!$data) {
            $data = null;
        }

        $this->_data[$name] = $data;

        return $data;
    }

    /**
     * Register Slash namespaces
     *
     * @return void
     */
    protected function _registerNamespaces()
    {
        $this->_xpath->registerNamespace('slash10', 'http://purl.org/rss/1.0/modules/slash/');
    }
}
