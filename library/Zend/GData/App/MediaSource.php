<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\App;

/**
 * Interface for defining data that can be encoded and sent over the network.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage App
 */
interface MediaSource
{
    /**
     * Return a byte stream representation of this object.
     *
     * @return string
     */
    public function encode();

    /**
     * Set the content type for the file attached (example image/png)
     *
     * @param string $value The content type
     * @return \Zend\GData\App\MediaFileSource Provides a fluent interface
     */
    public function setContentType($value);

    /**
     * The content type for the file attached (example image/png)
     *
     * @return string The content type
     */
    public function getContentType();

    /**
     * Sets the Slug header value.  Used by some services to determine the
     * title for the uploaded file.  A null value indicates no slug header.
     *
     * @var string The slug value
     * @return \Zend\GData\App\MediaSource Provides a fluent interface
     */
    public function setSlug($value);

    /**
     * Returns the Slug header value.  Used by some services to determine the
     * title for the uploaded file.  Returns null if no slug should be used.
     *
     * @return string The slug value
     */
    public function getSlug();
}
