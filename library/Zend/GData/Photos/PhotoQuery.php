<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\Photos;

/**
 * Assists in constructing queries for comment/tag entries.
 * Instances of this class can be provided in many places where a URL is
 * required.
 *
 * For information on submitting queries to a server, see the
 * service class, Zend_Gdata_Photos.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Photos
 */
class PhotoQuery extends AlbumQuery
{

    /**
     * The ID of the photo to query for.
     *
     * @var string
     */
    protected $_photoId = null;

    /**
     * Set the photo ID to query for. When set, this photo's comments/tags
     * will be returned. If not set or null, the default user's feed will be
     * returned instead.
     *
     * @param string $value The ID of the photo to retrieve, or null to
     *          clear.
     */
     public function setPhotoId($value)
     {
         $this->_photoId = $value;
     }

    /**
     * Get the photo ID which is to be returned.
     *
     * @see setPhoto
     * @return string The ID of the photo to retrieve.
     */
    public function getPhotoId()
    {
        return $this->_photoId;
    }

    /**
     * Returns the URL generated for this query, based on it's current
     * parameters.
     *
     * @return string A URL generated based on the state of this query.
     * @throws \Zend\GData\App\InvalidArgumentException
     */
    public function getQueryUrl($incomingUri = '')
    {
        $uri = '';
        if ($this->getPhotoId() !== null) {
            $uri .= '/photoid/' . $this->getPhotoId();
        } else {
            throw new \Zend\GData\App\InvalidArgumentException(
                    'PhotoId cannot be null');
        }
        $uri .= $incomingUri;
        return parent::getQueryUrl($uri);
    }

}
