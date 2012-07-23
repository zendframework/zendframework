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

use Zend\GData\App;

/**
 * Assists in constructing album queries for various entries.
 * Instances of this class can be provided in many places where a URL is
 * required.
 *
 * For information on submitting queries to a server, see the service
 * class, Zend_Gdata_Photos.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Photos
 */
class AlbumQuery extends UserQuery
{

    /**
     * The name of the album to query for. Mutually exclusive with AlbumId.
     *
     * @var string
     */
    protected $_albumName = null;

    /**
     * The ID of the album to query for. Mutually exclusive with AlbumName.
     *
     * @var string
     */
    protected $_albumId = null;

    /**
     * Set the album name to query for. When set, this album's photographs
     * be returned. If not set or null, the default user's feed will be
     * returned instead.
     *
     * NOTE: AlbumName and AlbumId are mutually exclusive. Setting one will
     *       automatically set the other to null.
     *
     * @param string $value The name of the album to retrieve, or null to
     *          clear.
     * @return \Zend\GData\Photos\AlbumQuery The query object.
     */
     public function setAlbumName($value)
     {
         $this->_albumId = null;
         $this->_albumName = $value;

         return $this;
     }

    /**
     * Get the album name which is to be returned.
     *
     * @see setAlbumName
     * @return string The name of the album to retrieve.
     */
    public function getAlbumName()
    {
        return $this->_albumName;
    }

    /**
     * Set the album ID to query for. When set, this album's photographs
     * be returned. If not set or null, the default user's feed will be
     * returned instead.
     *
     * NOTE: Album and AlbumId are mutually exclusive. Setting one will
     *       automatically set the other to null.
     *
     * @param string $value The ID of the album to retrieve, or null to
     *          clear.
     * @return \Zend\GData\Photos\AlbumQuery The query object.
     */
     public function setAlbumId($value)
     {
         $this->_albumName = null;
         $this->_albumId = $value;

         return $this;
     }

    /**
     * Get the album ID which is to be returned.
     *
     * @see setAlbum
     * @return string The ID of the album to retrieve.
     */
    public function getAlbumId()
    {
        return $this->_albumId;
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
        if ($this->getAlbumName() !== null && $this->getAlbumId() === null) {
            $uri .= '/album/' . $this->getAlbumName();
        } elseif ($this->getAlbumName() === null && $this->getAlbumId() !== null) {
            $uri .= '/albumid/' . $this->getAlbumId();
        } elseif ($this->getAlbumName() !== null && $this->getAlbumId() !== null) {
            throw new App\InvalidArgumentException(
                    'AlbumName and AlbumId cannot both be non-null');
        } else {
            throw new App\InvalidArgumentException(
                    'AlbumName and AlbumId cannot both be null');
        }
        $uri .= $incomingUri;
        return parent::getQueryUrl($uri);
    }

}
