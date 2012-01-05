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
 * @package    Zend_Gdata
 * @subpackage Calendar
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\GData\Calendar;

use Zend\GData\Calendar;

/**
 * Data model for a Google Calendar feed of events
 *
 * @uses       \Zend\GData\Calendar
 * @uses       Zend_Gdata_Extension_Timezone
 * @uses       \Zend\GData\Feed
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Calendar
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class EventFeed extends \Zend\GData\Feed
{

    protected $_timezone = null;

    /**
     * The classname for individual feed elements.
     *
     * @var string
     */
    protected $_entryClassName = 'Zend\GData\Calendar\EventEntry';

    /**
     * The classname for the feed.
     *
     * @var string
     */
    protected $_feedClassName = 'Zend\GData\Calendar\EventFeed';

    public function __construct($element = null)
    {
        $this->registerAllNamespaces(Calendar::$namespaces);
        parent::__construct($element);
    }

    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        if ($this->_timezone != null) {
            $element->appendChild($this->_timezone->getDOM($element->ownerDocument));
        }

        return $element;
    }

    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;

        switch ($absoluteNodeName) {
            case $this->lookupNamespace('gCal') . ':' . 'timezone';
                $timezone = new Extension\Timezone();
                $timezone->transferFromDOM($child);
                $this->_timezone = $timezone;
                break;

            default:
                parent::takeChildFromDOM($child);
                break;
        }
    }

    public function getTimezone()
    {
        return $this->_timezone;
    }

    public function setTimezone($value)
    {
        $this->_timezone = $value;
        return $this;
    }

}
