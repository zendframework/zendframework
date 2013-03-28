<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\View\Helper;

use Zend\Mvc\Controller\Plugin\FlashMessenger as FlashMessengerPlugin;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\EscapeHtml;
use Zend\I18n\View\Helper\AbstractTranslatorHelper;

/**
 * Helper to proxy the plugin flash messenger
 */
class FlashMessenger extends AbstractTranslatorHelper implements ServiceLocatorAwareInterface
{
    /**
     * Default attributes for the open format tag
     *
     * @var array
     */
    protected $classMessages = array(
        FlashMessengerPlugin::NAMESPACE_INFO => 'info',
        FlashMessengerPlugin::NAMESPACE_ERROR => 'error',
        FlashMessengerPlugin::NAMESPACE_SUCCESS => 'success',
        FlashMessengerPlugin::NAMESPACE_DEFAULT => 'default',
    );

    /**
     * Templates for the open/close/separators for message tags
     *
     * @var string
     */
    protected $messageCloseString     = '</li></ul>';
    protected $messageOpenFormat      = '<ul%s><li>';
    protected $messageSeparatorString = '</li><li>';

    /**
     * Html escape helper
     *
     * @var EscapeHtml
     */
    protected $escapeHtmlHelper;

    /**
     * Flash messenger plugin
     *
     * @var FlashMessengerPlugin
     */
    protected $flashMessengerPlugin;

    /**
     * Service locator
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Returns the flash messenger plugin controller
     *
     * @param  string|null $namespace
     * @return FlashMessenger|FlashMessengerPlugin
     */
    public function __invoke($namespace = null)
    {
        if (null === $namespace) {
            return $this;
        }
        $flashMessenger = $this->getFlashMessengerPlugin();

        return $flashMessenger->getMessagesFromNamespace($namespace);
    }

    /**
     * Proxy the flash messenger plugin controller
     *
     * @param  string $method
     * @param  array  $argv
     * @return mixed
     */
    public function __call($method, $argv)
    {
        $flashMessenger = $this->getFlashMessengerPlugin();
        return call_user_func_array(array($flashMessenger, $method), $argv);
    }

    /**
     * Render Messages
     *
     * @param  string $namespace
     * @param  array  $classes
     * @return string
     */
    public function render($namespace = FlashMessengerPlugin::NAMESPACE_DEFAULT, array $classes = array())
    {
        $flashMessenger = $this->getFlashMessengerPlugin();
        $messages = $flashMessenger->getMessagesFromNamespace($namespace);

        // Prepare classes for opening tag
        if (empty($classes)) {
            $classes = isset($this->classMessages[$namespace]) ?
                $this->classMessages[$namespace] : $this->classMessages[FlashMessengerPlugin::NAMESPACE_DEFAULT];
            $classes = array($classes);
        }

        // Flatten message array
        $escapeHtml      = $this->getEscapeHtmlHelper();
        $messagesToPrint = array();

        $translator = $this->getTranslator();
        $translatorTextDomain = $this->getTranslatorTextDomain();

        array_walk_recursive($messages, function($item) use (&$messagesToPrint, $escapeHtml, $translator, $translatorTextDomain) {
            if ($translator !== null) {
                $item = $translator->translate(
                        $item, $translatorTextDomain
                );
            }
            $messagesToPrint[] = $escapeHtml($item);
        });

        if (empty($messagesToPrint)) {
            return '';
        }

        // Generate markup
        $markup  = sprintf($this->getMessageOpenFormat(), ' class="' . implode(' ', $classes) . '"');
        $markup .= implode(sprintf($this->getMessageSeparatorString(), ' class="' . implode(' ', $classes) . '"'), $messagesToPrint);
        $markup .= $this->getMessageCloseString();

        return $markup;
    }

    /**
     * Set the flash messenger plugin
     *
     * @param  FlashMessengerPlugin $flashMessengerPlugin
     * @return FlashMessenger
     */
    public function setFlashMessengerPlugin(FlashMessengerPlugin $flashMessengerPlugin)
    {
        $this->flashMessengerPlugin = $flashMessengerPlugin;
        return $this;
    }

    /**
     * Get the flash messenger plugin
     *
     * @return FlashMessengerPlugin
     */
    public function getFlashMessengerPlugin()
    {
        if (null === $this->flashMessengerPlugin) {
            $this->setFlashMessengerPlugin(new FlashMessengerPlugin());
        }

        return $this->flashMessengerPlugin;
    }

    /**
     * Set the string used to close message representation
     *
     * @param  string $messageCloseString
     * @return FlashMessenger
     */
    public function setMessageCloseString($messageCloseString)
    {
        $this->messageCloseString = (string) $messageCloseString;
        return $this;
    }

    /**
     * Get the string used to close message representation
     *
     * @return string
     */
    public function getMessageCloseString()
    {
        return $this->messageCloseString;
    }

    /**
     * Set the formatted string used to open message representation
     *
     * @param  string $messageOpenFormat
     * @return FlashMessenger
     */
    public function setMessageOpenFormat($messageOpenFormat)
    {
        $this->messageOpenFormat = (string) $messageOpenFormat;
        return $this;
    }

    /**
     * Get the formatted string used to open message representation
     *
     * @return string
     */
    public function getMessageOpenFormat()
    {
        return $this->messageOpenFormat;
    }

    /**
     * Set the string used to separate messages
     *
     * @param  string $messageSeparatorString
     * @return FlashMessenger
     */
    public function setMessageSeparatorString($messageSeparatorString)
    {
        $this->messageSeparatorString = (string) $messageSeparatorString;
        return $this;
    }

    /**
     * Get the string used to separate messages
     *
     * @return string
     */
    public function getMessageSeparatorString()
    {
        return $this->messageSeparatorString;
    }

    /**
     * Set the service locator.
     *
     * @param  ServiceLocatorInterface $serviceLocator
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
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Retrieve the escapeHtml helper
     *
     * @return EscapeHtml
     */
    protected function getEscapeHtmlHelper()
    {
        if ($this->escapeHtmlHelper) {
            return $this->escapeHtmlHelper;
        }

        if (method_exists($this->getView(), 'plugin')) {
            $this->escapeHtmlHelper = $this->view->plugin('escapehtml');
        }

        if (!$this->escapeHtmlHelper instanceof EscapeHtml) {
            $this->escapeHtmlHelper = new EscapeHtml();
        }

        return $this->escapeHtmlHelper;
    }
}
