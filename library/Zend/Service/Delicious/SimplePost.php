<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace Zend\Service\Delicious;

/**
 * Represents a publicly available post
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Delicious
 */
class SimplePost
{
    /**
     * @var string Post url
     */
    protected $_url;

    /**
     * @var string Post title
     */
    protected $_title;

    /**
     * @var string Post notes
     */
    protected $_notes;

    /**
     * @var array Post tags
     */
    protected $_tags = array();

    /**
     * Constructor
     *
     * @param   array $post Post data
     * @return  void
     * @throws  Zend_Service_Delicious_Exception
     */
    public function __construct(array $post)
    {
        if (!isset($post['u']) || !isset($post['d'])) {
            throw new Exception('Title and URL not set.');
        }

        $this->_url   = $post['u'];
        $this->_title = $post['d'];

        if (isset($post['t'])) {
            $this->_tags = $post['t'];
        }
        if (isset($post['n'])) {
            $this->_notes = $post['n'];
        }
    }

    /**
     * Getter for URL
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * Getter for title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Getter for notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->_notes;
    }

    /**
     * Getter for tags
     *
     * @return array
     */
    public function getTags()
    {
        return $this->_tags;
    }
}
