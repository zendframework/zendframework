<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace ZendTest\Form\TestAsset\Entity;

class Orphan
{
    /**
     * Name
     * @var string
     */
    public $name;

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function exchangeArray($data = array())
    {
        $this->name = $data['name'];
    }
}
