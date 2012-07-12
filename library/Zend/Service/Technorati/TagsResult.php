<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace Zend\Service\Technorati;

use DomElement;

/**
 * Represents a single Technorati TopTags or BlogPostTags query result object.
 * It is never returned as a standalone object,
 * but it always belongs to a valid TagsResultSet object.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 */
class TagsResult extends AbstractResult
{
    /**
     * Name of the tag.
     *
     * @var     string
     * @access  protected
     */
    protected $tag;

    /**
     * Number of posts containing this tag.
     *
     * @var     int
     * @access  protected
     */
    protected $posts;


    /**
     * Constructs a new object object from DOM Document.
     *
     * @param   DomElement $dom the ReST fragment for this object
     */
    public function __construct(DomElement $dom)
    {
        $this->fields = array( 'tag'   => 'tag',
                               'posts' => 'posts');
        parent::__construct($dom);

        // filter fields
        $this->tag   = (string) $this->tag;
        $this->posts = (int) $this->posts;
    }

    /**
     * Returns the tag name.
     *
     * @return  string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Returns the number of posts.
     *
     * @return  int
     */
    public function getPosts()
    {
        return $this->posts;
    }
}
