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
class Zend_Service_DeveloperGarden_Response_ConferenceCall_CreateConferenceTemplateResponseType
    extends Zend_Service_DeveloperGarden_Response_BaseType
{
    /**
     * the template Id
     *
     * @var string
     */
    public $templateId = null;

    /**
     * return the template id
     *
     * @return string
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }
}
