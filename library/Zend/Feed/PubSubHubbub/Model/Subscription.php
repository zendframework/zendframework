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
 * @package    Zend_Feed_Pubsubhubbub
 * @subpackage Entity
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Feed\PubSubHubbub\Model;

use DateInterval;
use DateTime;
use Zend\Feed\PubSubHubbub;

/**
 * @category   Zend
 * @package    Zend_Feed_Pubsubhubbub
 * @subpackage Entity
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Subscription extends AbstractModel implements SubscriptionPersistenceInterface
{
    /**
     * Common DateTime object to assist with unit testing
     * 
     * @var DateTime
     */
    protected $now;
    
    /**
     * Save subscription to RDMBS
     *
     * @param array $data
     * @return bool
     * @throws PubSubHubbub\Exception\InvalidArgumentException
     */
    public function setSubscription(array $data)
    {
        if (!isset($data['id'])) {
            throw new PubSubHubbub\Exception\InvalidArgumentException(
                'ID must be set before attempting a save'
            );
        }
        $result = $this->_db->select(array('id' => $data['id']));
        if ($result && (0 < count($result))) {
            $data['created_time'] = $result->current()->created_time;
            $now = $this->getNow();
            if (array_key_exists('lease_seconds', $data) 
                && $data['lease_seconds']
            ) {
                $data['expiration_time'] = $now->add(new DateInterval('PT' . $data['lease_seconds'] . 'S'))
                    ->format('Y-m-d H:i:s');
            }
            $this->_db->update(
                $data,
                array('id' => $data['id'])
            );
            return false;
        }

        $this->_db->insert($data);
        return true;
    }
    
    /**
     * Get subscription by ID/key
     * 
     * @param  string $key 
     * @return array
     * @throws PubSubHubbub\Exception\InvalidArgumentException
     */
    public function getSubscription($key)
    {
        if (empty($key) || !is_string($key)) {
            throw new PubSubHubbub\Exception\InvalidArgumentException('Invalid parameter "key"'
                .' of "' . $key . '" must be a non-empty string');
        }
        $result = $this->_db->select(array('id' => $key));
        if (count($result)) {
            return $result->current()->getArrayCopy();
        }
        return false;
    }

    /**
     * Determine if a subscription matching the key exists
     * 
     * @param  string $key 
     * @return bool
     * @throws PubSubHubbub\Exception\InvalidArgumentException
     */
    public function hasSubscription($key)
    {
        if (empty($key) || !is_string($key)) {
            throw new PubSubHubbub\Exception\InvalidArgumentException('Invalid parameter "key"'
                .' of "' . $key . '" must be a non-empty string');
        }
        $result = $this->_db->select(array('id' => $key));
        if (count($result)) {
            return true;
        }
        return false;
    }

    /**
     * Delete a subscription
     *
     * @param string $key
     * @return bool
     */
    public function deleteSubscription($key)
    {
        $result = $this->_db->select(array('id' => $key));
        if (count($result)) {
            $this->_db->delete(
                array('id' => $key)
            );
            return true;
        }
        return false;
    }

    /**
     * Get a new DateTime or the one injected for testing
     * 
     * @return DateTime
     */
    public function getNow()
    {
        if (null === $this->now) {
            return new DateTime();
        }
        return $this->now;
    }

    /**
     * Set a DateTime instance for assisting with unit testing
     * 
     * @param DateTime $now
     * @return Subscription
     */
    public function setNow(DateTime $now)
    {
        $this->now = $now;
        return $this;
    }
}
