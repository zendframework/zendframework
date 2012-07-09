<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage DeveloperGarden
 * @author     Marco Kaiser
 */
class Zend_Service_DeveloperGarden_Response_VoiceButler_CallStatus2Response
    extends Zend_Service_DeveloperGarden_Response_VoiceButler_CallStatusResponse
{
    /**
     * returns the phone number of the second participant, who was called.
     *
     * @return string
     */
    public function getBe164()
    {
        return $this->getBNumber();
    }

    /**
     * returns the phone number of the second participant, who was called.
     *
     * @return string
     */
    public function getBNumber()
    {
        if (isset($this->return->be164)) {
            return $this->return->be164;
        }
        return null;
    }

    /**
     * Index of the phone number of the second participant (B), who was called. The value 0 means
     * the first B party phone number which was called, 1 means the second B party phone number
     * which was called etc.
     *
     * @return integer
     */
    public function getBNumberIndex()
    {
        return $this->getBIndex();
    }

    /**
     * Index of the phone number of the second participant (B), who was called. The value 0 means
     * the first B party phone number which was called, 1 means the second B party phone number
     * which was called etc.
     *
     * @return integer
     */
    public function getBIndex()
    {
        if (isset($this->return->bindex)) {
            return $this->return->bindex;
        }
        return null;
    }
}
