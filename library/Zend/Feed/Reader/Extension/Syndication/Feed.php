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
 * @package    Zend_Feed_Reader
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Feed\Reader\Extension\Syndication;

use Zend\Feed\Reader,
    Zend\Feed\Reader\Extension,
    Zend\Date;

/**
 * @category   Zend
 * @package    Zend_Feed_Reader
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Feed extends \Zend\Feed\Reader\Extension\AbstractFeed
{
    /**
     * Get update period
     *
     * @return string
     * @throws Reader\Exception\InvalidArgumentException
     */
    public function getUpdatePeriod()
    {
        $name = 'updatePeriod';
        $period = $this->_getData($name);

        if ($period === null) {
            $this->_data[$name] = 'daily';
            return 'daily'; //Default specified by spec
        }

        switch ($period)
        {
            case 'hourly':
            case 'daily':
            case 'weekly':
            case 'yearly':
                return $period;
            default:
                throw new Reader\Exception\InvalidArgumentException("Feed specified invalid update period: '$period'."
                    .  " Must be one of hourly, daily, weekly or yearly"
                );
        }
    }

    /**
     * Get update frequency
     * @return int
     */
    public function getUpdateFrequency()
    {
        $name = 'updateFrequency';
        $freq = $this->_getData($name, 'number');

        if (!$freq || $freq < 1) {
            $this->_data[$name] = 1;
            return 1;
        }

        return $freq;
    }

    /**
     * Get update frequency as ticks
     * @return int
     */
    public function getUpdateFrequencyAsTicks()
    {
        $name = 'updateFrequency';
        $freq = $this->_getData($name, 'number');

        if (!$freq || $freq < 1) {
            $this->_data[$name] = 1;
            $freq = 1;
        }

        $period = $this->getUpdatePeriod();
        $ticks = 1;

        switch ($period)
        {
            //intentional fall through
            case 'yearly':
                $ticks *= 52; //TODO: fix generalisation, how?
            case 'weekly':
                $ticks *= 7;
            case 'daily':
                $ticks *= 24;
            case 'hourly':
                $ticks *= 3600;
                break;
            default: //Never arrive here, exception thrown in getPeriod()
                break;
        }

        return $ticks / $freq;
    }

    /**
     * Get update base
     *
     * @return Date\Date|null
     */
    public function getUpdateBase()
    {
        $updateBase = $this->_getData('updateBase');
        $date = null;
        if ($updateBase) {
            $date = new Date\Date;
            $date->set($updateBase, Date\Date::W3C);
        }
        return $date;
    }

    /**
     * Get the entry data specified by name
     *
     * @param string $name
     * @param string $type
     * @return mixed|null
     */
    private function _getData($name, $type = 'string')
    {
        if (array_key_exists($name, $this->_data)) {
            return $this->_data[$name];
        }

        $data = $this->_xpath->evaluate($type . '(' . $this->getXpathPrefix() . '/syn10:' . $name . ')');

        if (!$data) {
            $data = null;
        }

        $this->_data[$name] = $data;

        return $data;
    }

    /**
     * Register Syndication namespaces
     *
     * @return void
     */
    protected function _registerNamespaces()
    {
        $this->_xpath->registerNamespace('syn10', 'http://purl.org/rss/1.0/modules/syndication/');
    }
}
