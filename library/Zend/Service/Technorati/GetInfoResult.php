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
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Service\Technorati;

use DomDocument,
    DOMXPath;

/**
 * Represents a single Technorati GetInfo query result object.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
