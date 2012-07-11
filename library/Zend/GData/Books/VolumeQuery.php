<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\Books;

use Zend\GData\Books;

/**
 * Assists in constructing queries for Books volumes
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Books
 */
class VolumeQuery extends \Zend\GData\Query
{

    /**
     * Create Gdata_Books_VolumeQuery object
     *
     * @param string|null $url If non-null, pre-initializes the instance to
     *        use a given URL.
     */
    public function __construct($url = null)
    {
        parent::__construct($url);
    }

    /**
     * Sets the minimum level of viewability of volumes to return in the search results
     *
     * @param string|null $value The minimum viewability - 'full' or 'partial'
     * @return \Zend\GData\Books\VolumeQuery Provides a fluent interface
     */
    public function setMinViewability($value = null)
    {
        switch ($value) {
            case 'full_view':
                $this->_params['min-viewability'] = 'full';
                break;
            case 'partial_view':
                $this->_params['min-viewability'] = 'partial';
                break;
            case null:
                unset($this->_params['min-viewability']);
                break;
        }
        return $this;
    }

    /**
     * Minimum viewability of volumes to include in search results
     *
     * @return string|null min-viewability
     */
    public function getMinViewability()
    {
        if (array_key_exists('min-viewability', $this->_params)) {
            return $this->_params['min-viewability'];
        } else {
            return null;
        }
    }

    /**
     * Returns the generated full query URL
     *
     * @return string The URL
     */
    public function getQueryUrl()
    {
        if (isset($this->_url)) {
            $url = $this->_url;
        } else {
            $url = Books::VOLUME_FEED_URI;
        }
        if ($this->getCategory() !== null) {
            $url .= '/-/' . $this->getCategory();
        }
        $url = $url . $this->getQueryString();
        return $url;
    }

}
