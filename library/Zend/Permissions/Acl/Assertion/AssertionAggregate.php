<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Zend\Permissions\Acl\Assertion;

use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Exception\InvalidArgumentException;
use Zend\Permissions\Acl\Exception\RuntimeException;

class AssertionAggregate implements AssertionInterface
{
    const MODE_ALL = 'all';

    const MODE_AT_LEAST_ONE = 'at_least_one';

    protected $assertions = array();

    /**
     *
     * @var $manager AssertionManager
     */
    protected $assertionManager;

    protected $mode = self::MODE_ALL;

    /**
     * Stacks an assertion in aggregate
     *
     * @param AssertionInterface|string $assertion
     *            if string, must match a AssertionManager declared service (checked later)
     *
     * @return self
     */
    public function addAssertion($assertion)
    {
        $this->assertions[] = $assertion;

        return $this;
    }

    public function addAssertions(array $assertions)
    {
        foreach ($assertions as $assertion) {
            $this->addAssertion($assertion);
        }

        return $this;
    }

    /**
     * Empties assertions stack
     *
     * @return self
     */
    public function clearAssertions()
    {
        $this->assertions = array();

        return $this;
    }

    /**
     *
     * @param AssertionManager $manager
     *
     * @return self
     */
    public function setAssertionManager(AssertionManager $manager)
    {
        $this->assertionManager = $manager;

        return $this;
    }

    public function getAssertionManager()
    {
        return $this->assertionManager;
    }

    /**
     * Set assertion chain behavior
     *
     * AssertionAggregate should assert to true when:
     *
     * - all assertions are true with MODE_ALL
     * - at least one assertion is true with MODE_AT_LEAST_ONE
     *
     * @param string $mode
     *            indicates how assertion chain result should interpreted (either 'all' or 'at_least_one')
     * @throws InvalidArgumentException
     *
     * @return self
     */
    public function setMode($mode)
    {
        if ($mode != self::MODE_ALL && $mode != self::MODE_AT_LEAST_ONE) {
            throw new InvalidArgumentException('invalid assertion aggregate mode');
        }

        $this->mode = $mode;

        return $this;
    }

    /**
     * Return current mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @see \Zend\Permissions\Acl\Assertion\AssertionInterface::assert()
     *
     * @throws RuntimeException
     * @return bool
     */
    public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null, $privilege = null)
    {
        // check if assertions are set
        if (! $this->assertions) {
            throw new RuntimeException('no assertion have been aggregated to this AssertionAggregate');
        }

        foreach ($this->assertions as $assertion) {
            // jit assertion mloading
            if (! $assertion instanceof AssertionInterface) {
                if (class_exists($assertion)) {
                    $assertion = new $assertion();
                } else {
                    if ($manager = $this->getAssertionManager()) {
                        try {
                            $assertion = $manager->get($assertion);
                        } catch (\Exception $e) {
                            throw new Exception\InvalidAssertionException('assertion "' . $assertion . '" is not defined in assertion manager');
                        }
                    } else {
                        throw new RuntimeException('no assertion manager is set - cannot look up for assertions');
                    }
                }
            }

            $result = (bool) $assertion->assert($acl, $role, $resource, $privilege);

            if ($this->getMode() == self::MODE_ALL && ! $result) {
                // on false is enough
                return false;
            }

            if ($this->getMode() == self::MODE_AT_LEAST_ONE && $result) {
                // one true is enough
                return true;
            }
        }

        if ($this->getMode() == self::MODE_ALL) {
            // none of the assertions returned false
            return true;
        } else {
            return false;
        }
    }
}
