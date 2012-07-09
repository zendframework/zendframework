<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\GApps;

/**
 * Data model for a collection of Google Apps email list recipient entries,
 * usually provided by the Google Apps servers.
 *
 * For information on requesting this feed from a server, see the Google
 * Apps service class, Zend_Gdata_GApps.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage GApps
 */
class EmailListRecipientFeed extends \Zend\GData\Feed
{

    protected $_entryClassName = '\Zend\GData\GApps\EmailListRecipientEntry';
    protected $_feedClassName = '\Zend\GData\GApps\EmailListRecipientFeed';

}
