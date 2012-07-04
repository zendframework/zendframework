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
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\View\Helper\Navigation;

use RecursiveIteratorIterator;
use Zend\Acl;
use Zend\Navigation;
use Zend\Navigation\Page\AbstractPage;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\I18n\Translator\Translator;
use Zend\View;
use Zend\View\Exception;

/**
 * Base class for navigational helpers
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractHelper
    extends View\Helper\AbstractHtmlElement
    implements HelperInterface,
               ServiceLocatorAwareInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * AbstractContainer to operate on by default
     *
     * @var Navigation\AbstractContainer
     */
    protected $container;

    /**
     * The minimum depth a page must have to be included when rendering
     *
     * @var int
     */
    protected $minDepth;

    /**
     * The maximum depth a page can have to be included when rendering
     *
     * @var int
     */
    protected $maxDepth;

    /**
     * Indentation string
     *
     * @var string
     */
    protected $indent = '';

    /**
     * Translator
     *
     * @var Translator
     */
    protected $translator;

    /**
     * ACL to use when iterating pages
     *
     * @var Acl\Acl
     */
    protected $acl;

    /**
     * Wheter invisible items should be rendered by this helper
     *
     * @var bool
     */
    protected $renderInvisible = false;

    /**
     * ACL role to use when iterating pages
     *
     * @var string|Acl\Role
     */
    protected $role;

    /**
     * Whether translator should be used for page labels and titles
     *
     * @var bool
     */
    protected $useTranslator = true;

    /**
     * Whether ACL should be used for filtering out pages
     *
     * @var bool
     */
    protected $useAcl = true;

    /**
     * Default ACL to use when iterating pages if not explicitly set in the
     * instance by calling {@link setAcl()}
     *
     * @var Acl\Acl
     */
    protected static $defaultAcl;

    /**
     * Default ACL role to use when iterating pages if not explicitly set in the
     * instance by calling {@link setRole()}
     *
     * @var string|Acl\Role
     */
    protected static $defaultRole;

    /**
     * Set the service locator.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return AbstractHelper
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * Get the service locator.
     *
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Sets navigation container the helper operates on by default
     *
     * Implements {@link HelperInterface::setContainer()}.
     *
     * @param  string|Navigation\AbstractContainer $container [optional] container to operate on.  Default is null, meaning container will be reset.
     * @return AbstractHelper  fluent interface, returns self
     */
    public function setContainer($container = null)
    {
        $this->parseContainer($container);
        $this->container = $container;
        return $this;
    }

    /**
     * Returns the navigation container helper operates on by default
     *
     * Implements {@link HelperInterface::getContainer()}.
     *
     * If no container is set, a new container will be instantiated and
     * stored in the helper.
     *
     * @return Navigation\AbstractContainer  navigation container
     */
    public function getContainer()
    {
        if (null === $this->container) {
            $this->container = new \Zend\Navigation\Navigation();
        }

        return $this->container;
    }

    /**
     * Verifies container and eventually fetches it from service locator if it is a string
     *
     * @param \Zend\Navigation\AbstractContainer|string|null $container
     * @throws \Zend\View\Exception\InvalidArgumentException
     */
    protected function parseContainer(&$container = null)
    {
        if (null === $container) {
            return;
        }

        if (is_string($container)) {
            if (!$this->getServiceLocator()) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Attempted to set container with alias "%s" but no ServiceLocator was set',
                    $container
                ));
            }

            $container = $this->getServiceLocator()->get($container);
            return;
        }

        if (!$container instanceof Navigation\AbstractContainer) {
            throw new  Exception\InvalidArgumentException(
                'Container must be a string alias or an instance of ' .
                    'Zend\Navigation\AbstractContainer'
            );
        }
    }

    /**
     * Sets the minimum depth a page must have to be included when rendering
     *
     * @param  int $minDepth [optional] minimum depth. Default is null, which
     *                       sets no minimum depth.
     * @return AbstractHelper fluent interface, returns self
     */
    public function setMinDepth($minDepth = null)
    {
        if (null === $minDepth || is_int($minDepth)) {
            $this->minDepth = $minDepth;
        } else {
            $this->minDepth = (int) $minDepth;
        }
        return $this;
    }

    /**
     * Returns minimum depth a page must have to be included when rendering
     *
     * @return int|null  minimum depth or null
     */
    public function getMinDepth()
    {
        if (!is_int($this->minDepth) || $this->minDepth < 0) {
            return 0;
        }
        return $this->minDepth;
    }

    /**
     * Sets the maximum depth a page can have to be included when rendering
     *
     * @param  int $maxDepth [optional] maximum depth. Default is null, which
     *                       sets no maximum depth.
     * @return AbstractHelper fluent interface, returns self
     */
    public function setMaxDepth($maxDepth = null)
    {
        if (null === $maxDepth || is_int($maxDepth)) {
            $this->maxDepth = $maxDepth;
        } else {
            $this->maxDepth = (int) $maxDepth;
        }
        return $this;
    }

    /**
     * Returns maximum depth a page can have to be included when rendering
     *
     * @return int|null  maximum depth or null
     */
    public function getMaxDepth()
    {
        return $this->maxDepth;
    }

    /**
     * Set the indentation string for using in {@link render()}, optionally a
     * number of spaces to indent with
     *
     * @param  string|int $indent indentation string or number of spaces
     * @return AbstractHelper  fluent interface, returns self
     */
    public function setIndent($indent)
    {
        $this->indent = $this->getWhitespace($indent);
        return $this;
    }

    /**
     * Returns indentation
     *
     * @return string
     */
    public function getIndent()
    {
        return $this->indent;
    }

    /**
     * Sets translator to use in helper
     *
     * Implements {@link HelperInterface::setTranslator()}.
     *
     * @param  mixed $translator [optional] translator.  Expects an object of
     *                           type {@link Translator\Adapter\AbstractAdapter}
     *                           or {@link Translator\Translator}, or null.
     *                           Default is null, which sets no translator.
     * @return AbstractHelper  fluent interface, returns self
     */
    public function setTranslator(Translator $translator = null)
    {
        $this->translator = $translator;
        return $this;
    }

    /**
     * Returns translator used in helper
     *
     * Implements {@link HelperInterface::getTranslator()}.
     *
     * @return Translator|null  translator or null
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * Sets ACL to use when iterating pages
     *
     * Implements {@link HelperInterface::setAcl()}.
     *
     * @param  Acl\Acl $acl [optional] ACL object.  Default is null.
     * @return AbstractHelper  fluent interface, returns self
     */
    public function setAcl(Acl\Acl $acl = null)
    {
        $this->acl = $acl;
        return $this;
    }

    /**
     * Returns ACL or null if it isn't set using {@link setAcl()} or
     * {@link setDefaultAcl()}
     *
     * Implements {@link HelperInterface::getAcl()}.
     *
     * @return Acl\Acl|null  ACL object or null
     */
    public function getAcl()
    {
        if ($this->acl === null && self::$defaultAcl !== null) {
            return self::$defaultAcl;
        }

        return $this->acl;
    }

    /**
     * Sets ACL role(s) to use when iterating pages
     *
     * Implements {@link HelperInterface::setRole()}.
     *
     * @param  mixed $role [optional] role to set. Expects a string, an
     *                     instance of type {@link Acl\Role\RoleInterface}, or null. Default
     *                     is null, which will set no role.
     * @return AbstractHelper  fluent interface, returns self
     * @throws Exception\InvalidArgumentException if $role is invalid
     */
    public function setRole($role = null)
    {
        if (null === $role || is_string($role) ||
            $role instanceof Acl\Role\RoleInterface
        ) {
            $this->role = $role;
        } else {
            throw new Exception\InvalidArgumentException(sprintf(
                '$role must be a string, null, or an instance of '
                .  'Zend\Acl\Role\RoleInterface; %s given',
                (is_object($role) ? get_class($role) : gettype($role))
            ));
        }

        return $this;
    }

    /**
     * Returns ACL role to use when iterating pages, or null if it isn't set
     * using {@link setRole()} or {@link setDefaultRole()}
     *
     * Implements {@link HelperInterface::getRole()}.
     *
     * @return string|Acl\Role\RoleInterface|null  role or null
     */
    public function getRole()
    {
        if ($this->role === null && self::$defaultRole !== null) {
            return self::$defaultRole;
        }

        return $this->role;
    }

    /**
     * Sets whether ACL should be used
     *
     * Implements {@link HelperInterface::setUseAcl()}.
     *
     * @param  bool $useAcl [optional] whether ACL should be used.  Default is true.
     * @return AbstractHelper  fluent interface, returns self
     */
    public function setUseAcl($useAcl = true)
    {
        $this->useAcl = (bool) $useAcl;
        return $this;
    }

    /**
     * Returns whether ACL should be used
     *
     * Implements {@link HelperInterface::getUseAcl()}.
     *
     * @return bool  whether ACL should be used
     */
    public function getUseAcl()
    {
        return $this->useAcl;
    }

    /**
     * Return renderInvisible flag
     *
     * @return bool
     */
    public function getRenderInvisible()
    {
        return $this->renderInvisible;
    }

    /**
     * Render invisible items?
     *
     * @param  bool $renderInvisible [optional] boolean flag
     * @return AbstractHelper  fluent interface returns self
     */
    public function setRenderInvisible($renderInvisible = true)
    {
        $this->renderInvisible = (bool) $renderInvisible;
        return $this;
    }

    /**
     * Sets whether translator should be used
     *
     * Implements {@link HelperInterface::setUseTranslator()}.
     *
     * @param  bool $useTranslator [optional] whether translator should be used.
     *                             Default is true.
     * @return AbstractHelper  fluent interface, returns self
     */
    public function setUseTranslator($useTranslator = true)
    {
        $this->useTranslator = (bool) $useTranslator;
        return $this;
    }

    /**
     * Returns whether translator should be used
     *
     * Implements {@link HelperInterface::getUseTranslator()}.
     *
     * @return bool  whether translator should be used
     */
    public function getUseTranslator()
    {
        return $this->useTranslator;
    }

    // Magic overloads:

    /**
     * Magic overload: Proxy calls to the navigation container
     *
     * @param  string $method             method name in container
     * @param  array  $arguments          [optional] arguments to pass
     * @return mixed                      returns what the container returns
     * @throws Navigation\Exception\ExceptionInterface  if method does not exist in container
     */
    public function __call($method, array $arguments = array())
    {
        return call_user_func_array(
                array($this->getContainer(), $method),
                $arguments);
    }

    /**
     * Magic overload: Proxy to {@link render()}.
     *
     * This method will trigger an E_USER_ERROR if rendering the helper causes
     * an exception to be thrown.
     *
     * Implements {@link HelperInterface::__toString()}.
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->render();
        } catch (\Exception $e) {
            $msg = get_class($e) . ': ' . $e->getMessage();
            trigger_error($msg, E_USER_ERROR);
            return '';
        }
    }

    // Public methods:

    /**
     * Finds the deepest active page in the given container
     *
     * @param  Navigation\AbstractContainer $container  container to search
     * @param  int|null             $minDepth   [optional] minimum depth
     *                                          required for page to be
     *                                          valid. Default is to use
     *                                          {@link getMinDepth()}. A
     *                                          null value means no minimum
     *                                          depth required.
     * @param  int|null             $minDepth   [optional] maximum depth
     *                                          a page can have to be
     *                                          valid. Default is to use
     *                                          {@link getMaxDepth()}. A
     *                                          null value means no maximum
     *                                          depth required.
     * @return array                            an associative array with
     *                                          the values 'depth' and
     *                                          'page', or an empty array
     *                                          if not found
     */
    public function findActive($container, $minDepth = null, $maxDepth = -1)
    {
        $this->parseContainer($container);
        if (!is_int($minDepth)) {
            $minDepth = $this->getMinDepth();
        }
        if ((!is_int($maxDepth) || $maxDepth < 0) && null !== $maxDepth) {
            $maxDepth = $this->getMaxDepth();
        }

        $found  = null;
        $foundDepth = -1;
        $iterator = new RecursiveIteratorIterator($container, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($iterator as $page) {
            $currDepth = $iterator->getDepth();
            if ($currDepth < $minDepth || !$this->accept($page)) {
                // page is not accepted
                continue;
            }

            if ($page->isActive(false) && $currDepth > $foundDepth) {
                // found an active page at a deeper level than before
                $found = $page;
                $foundDepth = $currDepth;
            }
        }

        if (is_int($maxDepth) && $foundDepth > $maxDepth) {
            while ($foundDepth > $maxDepth) {
                if (--$foundDepth < $minDepth) {
                    $found = null;
                    break;
                }

                $found = $found->getParent();
                if (!$found instanceof AbstractPage) {
                    $found = null;
                    break;
                }
            }
        }

        if ($found) {
            return array('page' => $found, 'depth' => $foundDepth);
        } else {
            return array();
        }
    }

    /**
     * Checks if the helper has a container
     *
     * Implements {@link HelperInterface::hasContainer()}.
     *
     * @return bool  whether the helper has a container or not
     */
    public function hasContainer()
    {
        return null !== $this->container;
    }

    /**
     * Checks if the helper has an ACL instance
     *
     * Implements {@link HelperInterface::hasAcl()}.
     *
     * @return bool  whether the helper has a an ACL instance or not
     */
    public function hasAcl()
    {
        return null !== $this->acl;
    }

    /**
     * Checks if the helper has an ACL role
     *
     * Implements {@link HelperInterface::hasRole()}.
     *
     * @return bool  whether the helper has a an ACL role or not
     */
    public function hasRole()
    {
        return null !== $this->role;
    }

    /**
     * Checks if the helper has a translator
     *
     * Implements {@link HelperInterface::hasTranslator()}.
     *
     * @return bool  whether the helper has a translator or not
     */
    public function hasTranslator()
    {
        return null !== $this->translator;
    }

    /**
     * Returns an HTML string containing an 'a' element for the given page
     *
     * @param  AbstractPage $page  page to generate HTML for
     * @return string                      HTML string for the given page
     */
    public function htmlify(AbstractPage $page)
    {
        // get label and title for translating
        $label = $page->getLabel();
        $title = $page->getTitle();

        if ($this->getUseTranslator() && $t = $this->getTranslator()) {
            if (is_string($label) && !empty($label)) {
                $label = $t->translate($label);
            }
            if (is_string($title) && !empty($title)) {
                $title = $t->translate($title);
            }
        }

        // get attribs for anchor element
        $attribs = array(
            'id'     => $page->getId(),
            'title'  => $title,
            'class'  => $page->getClass(),
            'href'   => $page->getHref(),
            'target' => $page->getTarget()
        );

        $escaper = $this->view->plugin('escapeHtml');

        return '<a' . $this->_htmlAttribs($attribs) . '>'
             . $escaper($label)
             . '</a>';
    }

    // Iterator filter methods:

    /**
     * Determines whether a page should be accepted when iterating
     *
     * Rules:
     * - If a page is not visible it is not accepted, unless RenderInvisible has
     *   been set to true.
     * - If helper has no ACL, page is accepted
     * - If helper has ACL, but no role, page is not accepted
     * - If helper has ACL and role:
     *  - Page is accepted if it has no resource or privilege
     *  - Page is accepted if ACL allows page's resource or privilege
     * - If page is accepted by the rules above and $recursive is true, the page
     *   will not be accepted if it is the descendant of a non-accepted page.
     *
     * @param  AbstractPage $page      page to check
     * @param  bool         $recursive [optional] if true, page will not be
     *                                 accepted if it is the descendant of a
     *                                 page that is not accepted. Default is true.
     * @return bool                    whether page should be accepted
     */
    public function accept(AbstractPage $page, $recursive = true)
    {
        // accept by default
        $accept = true;

        if (!$page->isVisible(false) && !$this->getRenderInvisible()) {
            // don't accept invisible pages
            $accept = false;
        } elseif ($this->getUseAcl() && !$this->acceptAcl($page)) {
            // acl is not amused
            $accept = false;
        }

        if ($accept && $recursive) {
            $parent = $page->getParent();
            if ($parent instanceof AbstractPage) {
                $accept = $this->accept($parent, true);
            }
        }

        return $accept;
    }

    /**
     * Determines whether a page should be accepted by ACL when iterating
     *
     * Rules:
     * - If helper has no ACL, page is accepted
     * - If page has a resource or privilege defined, page is accepted
     *   if the ACL allows access to it using the helper's role
     * - If page has no resource or privilege, page is accepted
     *
     * @param  AbstractPage $page  page to check
     * @return bool                whether page is accepted by ACL
     */
    protected function acceptAcl(AbstractPage $page)
    {
        if (!$acl = $this->getAcl()) {
            // no acl registered means don't use acl
            return true;
        }

        $role = $this->getRole();
        $resource = $page->getResource();
        $privilege = $page->getPrivilege();

        if ($resource || $privilege) {
            // determine using helper role and page resource/privilege
            return $acl->hasResource($resource) && $acl->isAllowed($role, $resource, $privilege);
        }

        return true;
    }

    // Util methods:

    /**
     * Retrieve whitespace representation of $indent
     *
     * @param  int|string $indent
     * @return string
     */
    protected function getWhitespace($indent)
    {
        if (is_int($indent)) {
            $indent = str_repeat(' ', $indent);
        }

        return (string) $indent;
    }

    /**
     * Converts an associative array to a string of tag attributes.
     *
     * Overloads {@link View\Helper\AbstractHtmlElement::_htmlAttribs()}.
     *
     * @param  array $attribs  an array where each key-value pair is converted
     *                         to an attribute name and value
     * @return string          an attribute string
     */
    protected function _htmlAttribs($attribs)
    {
        // filter out null values and empty string values
        foreach ($attribs as $key => $value) {
            if ($value === null || (is_string($value) && !strlen($value))) {
                unset($attribs[$key]);
            }
        }

        return parent::_htmlAttribs($attribs);
    }

    /**
     * Normalize an ID
     *
     * Overrides {@link View\Helper\AbstractHtmlElement::_normalizeId()}.
     *
     * @param  string $value
     * @return string
     */
    protected function _normalizeId($value)
    {
        $prefix = get_class($this);
        $prefix = strtolower(trim(substr($prefix, strrpos($prefix, '\\')), '\\'));

        return $prefix . '-' . $value;
    }

    // Static methods:

    /**
     * Sets default ACL to use if another ACL is not explicitly set
     *
     * @param  Acl\Acl $acl [optional] ACL object. Default is null, which
     *                      sets no ACL object.
     * @return void
     */
    public static function setDefaultAcl(Acl\Acl $acl = null)
    {
        self::$defaultAcl = $acl;
    }

    /**
     * Sets default ACL role(s) to use when iterating pages if not explicitly
     * set later with {@link setRole()}
     *
     * @param  mixed $role [optional] role to set. Expects null, string, or an
     *                     instance of {@link Acl\Role\RoleInterface}. Default is null, which
     *                     sets no default role.
     * @return void
     * @throws Exception\InvalidArgumentException if role is invalid
     */
    public static function setDefaultRole($role = null)
    {
        if (null === $role
            || is_string($role)
            || $role instanceof Acl\Role\RoleInterface
        ) {
            self::$defaultRole = $role;
        } else {
            throw new Exception\InvalidArgumentException(sprintf(
                '$role must be null|string|Zend\Acl\Role\RoleInterface; received "%s"',
                (is_object($role) ? get_class($role) : gettype($role))
            ));
        }
    }
}
