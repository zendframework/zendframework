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

use DomDocument;
use DOMXPath;

/**
 * Represents a single Technorati GetInfo query result object.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 */
class GetInfoResult
{
    /**
     * Technorati author
     *
     * @var     Author
     * @access  protected
     */
    protected $author;

    /**
     * A list of weblogs claimed by this author
     *
     * @var     array
     * @access  protected
     */
    protected $weblogs = array();


    /**
     * Constructs a new object object from DOM Document.
     *
     * @param   DomDocument $dom the ReST fragment for this object
     */
    public function __construct(DomDocument $dom)
    {
        $xpath = new DOMXPath($dom);

        $result = $xpath->query('//result');
        if ($result->length == 1) {
            $this->author = new Author($result->item(0));
        }

        $result = $xpath->query('//item/weblog');
        if ($result->length >= 1) {
            foreach ($result as $weblog) {
                $this->weblogs[] = new Weblog($weblog);
            }
        }
    }


    /**
     * Returns the author associated with queried username.
     *
     * @return  Author
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Returns the collection of weblogs authored by queried username.
     *
     * @return  array of Weblog
     */
    public function getWeblogs()
    {
        return $this->weblogs;
    }

}
