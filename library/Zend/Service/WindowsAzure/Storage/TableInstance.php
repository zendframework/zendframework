<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service_WindowsAzure
 */

namespace Zend\Service\WindowsAzure\Storage;

use Zend\Service\WindowsAzure\Exception\UnknownPropertyException;

/**
 * @category                    Zend
 * @package                     Zend_Service_WindowsAzure
 * @subpackage                  Storage
 *
 * @property string  $Id        Id
 * @property string  $Name      Name
 * @property string  $Href      Href
 * @property string  $Updated   Updated
 */
class TableInstance
{
    /**
     * Data
     *
     * @var array
     */
    protected $_data = null;

    /**
     * Constructor
     *
     * @param string  $id              Id
     * @param string  $name            Name
     * @param string  $href            Href
     * @param string  $updated         Updated
     */
    public function __construct($id, $name, $href, $updated)
    {
        $this->_data = array(
            'id'               => $id,
            'name'             => $name,
            'href'             => $href,
            'updated'          => $updated
        );
    }

    /**
     * Magic overload for setting properties
     *
     * @param string $name     Name of the property
     * @param string $value    Value to set
     * @throws UnknownPropertyException
     * @return
     */
    public function __set($name, $value)
    {
        if (array_key_exists(strtolower($name), $this->_data)) {
            $this->_data[strtolower($name)] = $value;
            return;
        }

        throw new UnknownPropertyException('Unknown property: ' . $name);
    }

    /**
     * Magic overload for getting properties
     *
     * @param string $name     Name of the property
     * @throws UnknownPropertyException
     * @return
     */
    public function __get($name)
    {
        if (array_key_exists(strtolower($name), $this->_data)) {
            return $this->_data[strtolower($name)];
        }

        throw new UnknownPropertyException('Unknown property: ' . $name);
    }
}
