<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace Zend\Mvc\View\Console;

use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ModuleManager\ModuleManagerInterface;
use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Mvc\Exception\RuntimeException;
use Zend\Console\Response as ConsoleResponse;
use Zend\Console\Request as ConsoleRequest;
use Zend\Console\Adapter\AdapterInterface as ConsoleAdapter;
use Zend\Mvc\Router\RouteInterface;
use Zend\View\Model\ConsoleModel;
use Zend\Version\Version;
use Zend\Stdlib\ResponseInterface as Response;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage View
 */
class RouteNotFoundStrategy implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * The reason for a not-found condition
     *
     * @var boolean|string
     */
    protected $reason = false;

    /**
     * Attach the aggregate to the specified event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'handleRouteNotFoundError'));
    }

    /**
     * Detach aggregate listeners from the specified event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Detect if an error is a route not found condition
     *
     * If a "controller not found" or "invalid controller" error type is
     * encountered, sets the response status code to 404.
     *
     * @param  MvcEvent $e
     * @throws RuntimeException
     * @throws ServiceNotFoundException
     * @return void
     */
    public function handleRouteNotFoundError(MvcEvent $e)
    {
        $error = $e->getError();
        if (empty($error)) {
            return;
        }

        $response = $e->getResponse();
        $request = $e->getRequest();

        switch ($error) {
            case Application::ERROR_CONTROLLER_NOT_FOUND:
            case Application::ERROR_CONTROLLER_INVALID:
            case Application::ERROR_ROUTER_NO_MATCH:
                $this->reason = $error;
                if (!$response) {
                    $response = new ConsoleResponse();
                    $e->setResponse($response);
                }
                $response->setMetadata('error',$error);
                break;
            default:
                return;
        }

        $result = $e->getResult();
        if ($result instanceof Response) {
            // Already have a response as the result
            return;
        }

        // Prepare Console View Model
        $model = new ConsoleModel();
        $model->setErrorLevel(1);

        // Fetch service manager
        $sm = $e->getApplication()->getServiceManager();

        // Try to fetch module manager
        try{
            $mm = $sm->get('ModuleManager');
        } catch (ServiceNotFoundException $e) {
            // The application does not have or use module manager, so we cannot use it
            $mm = null;
        }

        // Try to fetch current console adapter
        try{
            $console = $sm->get('console');
            if (!$console instanceof ConsoleAdapter) {
                throw new ServiceNotFoundException();
            }
        } catch (ServiceNotFoundException $e) {
            // The application does not have console adapter
            throw new RuntimeException('Cannot access Console adapter - is it defined in ServiceManager?');
        }

        // Try to fetch router
        try{
            $router = $sm->get('Router');
        } catch (ServiceNotFoundException $e) {
            // The application does not have a router
            $router = null;
        }

        // Retrieve the script's name (entry point)
        if ($request instanceof ConsoleRequest) {
            $scriptName = basename($request->getScriptName());
        } else {
            $scriptName = '';
        }

        // Get application banner
        $banner = $this->getConsoleBanner($console, $mm);

        // Get application usage information
        $usage = $this->getConsoleUsage($console, $scriptName, $mm, $router);

        // Inject the text into view model
        $result = $banner ? rtrim($banner,"\r\n") : '';
        $result .= $usage ? "\n\n" . trim($usage,"\r\n") : '';
        $model->setResult($result);

        // Inject the result into MvcEvent
        $e->setResult($model);
    }

    /**
     * Build Console application banner text by querying currently loaded
     * modules.
     *
     * @param ModuleManagerInterface $moduleManager
     * @param ConsoleAdapter         $console
     * @return string
     */
    protected function getConsoleBanner(ConsoleAdapter $console, ModuleManagerInterface $moduleManager = null)
    {
        /**
         * Loop through all loaded modules and collect banners
         */
        $banners = array();
        if ($moduleManager !== null) {
            foreach ($moduleManager->getLoadedModules(false) as $module) {
                if (!$module instanceof ConsoleBannerProviderInterface) {
                    continue; // this module does not provide a banner
                }

                /* @var $module ConsoleBannerProviderInterface */
                $banners[] = $module->getConsoleBanner($console);
            }
        }

        /**
         * Handle an application with no defined banners
         */
        if (!count($banners)) {
            return "Zend Framework ".Version::VERSION." application.\nUsage:\n";
        }

        /**
         * Join the banners by a newline character
         */
        return join("\n",$banners);
    }

    /**
     * Build Console usage information by querying currently loaded modules.
     *
     * @param ConsoleAdapter         $console
     * @param string                 $scriptName
     * @param ModuleManagerInterface $moduleManager
     * @return string
     * @throws RuntimeException
     */
    protected function getConsoleUsage(
        ConsoleAdapter $console,
        $scriptName,
        ModuleManagerInterface $moduleManager = null
    ) {
        /**
         * Loop through all loaded modules and collect usage info
         */
        $usageInfo = array();

        if ($moduleManager !== null) {
            foreach ($moduleManager->getLoadedModules(false) as $name => $module) {
                if (!$module instanceof ConsoleUsageProviderInterface) {
                    continue; // this module does not provide usage info
                }

                /* @var $module ConsoleUsageProviderInterface */
                $usage = $module->getConsoleUsage($console);

                // Normalize what we got from the module or discard
                if (is_array($usage))
                    $usageInfo[$name] = $usage;
                elseif (is_string($usage))
                    $usageInfo[$name] = array($usage);
            }
        }

        /**
         * Handle an application with no usage information
         */
        if (!count($usageInfo)) {
            // TODO: implement fetching available console routes from router
            return '';
        }

        /**
         * Transform arrays in usage info into columns, otherwise join everything together
         */
        $result = '';
        $table = false;
        $tableCols = 0;
        $tableType = 0;
        foreach ($usageInfo as $moduleName => $usage) {
            if (is_string($usage)) {
                // It's a plain string - output as is
                $result .= $usage."\n";
            } elseif (is_array($usage)) {
                // It's an array, analyze it
                foreach ($usage as $a => $b) {
                    if (is_string($a) && is_string($b)) {
                        /**
                         *    'ivocation method' => 'explanation'
                         */
                        if (($tableCols !== 2 || $tableType != 1) && $table !== false) {
                            // render last table
                            $result .= $this->renderTable($table, $tableCols,$console->getWidth());
                            $table = false;

                             // add extra newline for clarity
                            $result .= "\n";
                        }

                        $tableCols = 2;
                        $tableType = 1;
                        $table[] = array($scriptName . ' ' . $a, $b);
                    } elseif (is_array($b)) {
                        /**
                         *  array( '--param', '--explanation' )
                         */
                        if ((count($b) != $tableCols || $tableType != 2) && $table !== false) {
                            // render last table
                            $result .= $this->renderTable($table, $tableCols, $console->getWidth());
                            $table = false;

                             // add extra newline for clarity
                            $result .= "\n";
                        }

                        $tableCols = count($b);
                        $tableType = 2;
                        $table[] = $b;
                    } else {
                        /**
                         *    'A single line of text'
                         */
                        if ($table !== false) {
                            // render last table
                            $result .= $this->renderTable($table, $tableCols, $console->getWidth());
                            $table = false;

                            // add extra newline for clarity
                            $result .= "\n";
                        }

                        $tableType = 0;
                        $result .= $b."\n";
                    }
                }
            } else {
                throw new RuntimeException('Cannot understand usage info for module '.$moduleName);
            }
        }

        // Finish last table
        if ($table !== false) {
            $result .= $this->renderTable($table, $tableCols,$console->getWidth());
        }

        return $result;
    }

    /**
     * Render a text table containing the data provided, that will fit inside console window's width.
     *
     * @param $data
     * @param $cols
     * @param $consoleWidth
     * @return string
     */
    protected function renderTable($data, $cols, $consoleWidth)
    {
        $result = '';
        $padding = 2;

        // If there is only 1 column, just concatenate it
        if ($cols == 1) {
            foreach ($data as $row) {
                $result .= $row[0]."\n";
            }
            return $result;
        }

        // Determine max width for each column
        $maxW = array();
        for ($x=1;$x<=$cols;$x++) {
            $maxW[$x] = 0;
            foreach ($data as $row) {
                $maxW[$x] = max($maxW[$x], mb_strlen($row[$x-1],'utf-8') + $padding*2);
            }
        }

        /**
         * Check if the sum of x-1 columns fit inside console window width - 10 chars. If columns do not fit inside
         * console window, then we'll just concatenate them and output as is.
         */
        $width = 0;
        for ($x=1;$x<$cols;$x++) {
            $width += $maxW[$x];
        }
        if ($width >= $consoleWidth - 10) {
            foreach ($data as $row) {
                $result .= join("    ",$row)."\n";
            }
            return $result;
        }

        /**
         * Use Zend\Text\Table to render the table.
         * The last column will use the remaining space in console window (minus 1 character to prevent double
         * wrapping at the edge of the screen).
         */
        $maxW[$cols] = $consoleWidth - $width -1;
        $table = new \Zend\Text\Table\Table();
        $table->setColumnWidths($maxW);
        $table->setDecorator(new \Zend\Text\Table\Decorator\Blank());
        $table->setPadding(2);

        foreach ($data as $row) {
            $table->appendRow($row);
        }

        return $table->render();
    }
}
