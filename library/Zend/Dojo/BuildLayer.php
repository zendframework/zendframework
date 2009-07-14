<?php
class Zend_Dojo_BuildLayer
{
    protected $_consumeJavascript = false;

    protected $_consumeOnLoad = false;

    protected $_dojo;

    protected $_layerName;

    protected $_layerScriptPath;

    protected $_profileOptions = array(
        'action'        => 'release',
        'optimize'      => 'shrinksafe',
        'layerOptimize' => 'shrinksafe',
        'copyTests'     => false,
        'loader'        => 'default',
        'cssOptimize'   => 'comments',
    );

    protected $_profilePath;

    protected $_profilePrefixes = array();

    protected $_view;

    public function __construct($options = null)
    {
        if (null !== $options) {
            if ($options instanceof Zend_Config) {
                $options = $options->toArray();
            } elseif (!is_array($options)) {
                require_once 'Zend/Dojo/Exception.php';
                throw new Zend_Dojo_Exception('Invalid options provided to constructor');
            }
            $this->setOptions($options);
        }
    }

    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    public function setView(Zend_View_Interface $view)
    {
        $this->_view = $view;
        return $this;
    }

    public function getView()
    {
        return $this->_view;
    }

    public function setDojoHelper(Zend_Dojo_View_Helper_Dojo_Container $helper)
    {
        $this->_dojo = $helper;
        return $this;
    }

    public function getDojoHelper()
    {
        if (null === $this->_dojo) {
            if (null === ($view = $this->getView())) {
                require_once 'Zend/Dojo/Exception.php';
                throw new Zend_Dojo_Exception('View object not registered; cannot retrieve dojo helper');
            }
            $helper = $view->getHelper('dojo');
            $this->setDojoHelper($view->dojo());
        }
        return $this->_dojo;
    }

    public function setLayerName($name)
    {
        if (!preg_match('/^[a-z][a-z0-9_]*(\.[a-z][a-z0-9_]*)+$/i', $name)) {
            require_once 'Zend/Dojo/Exception.php';
            throw new Zend_Dojo_Exception('Invalid layer name provided; must be of form[a-z][a-z0-9_](\.[a-z][a-z0-9_])+');
        }
        $this->_layerName = $name;
        return $this;
    }

    public function getLayerName()
    {
        return $this->_layerName;
    }

    public function setLayerScriptPath($path)
    {
        $this->_layerScriptPath = (string) $path;
        return $this;
    }

    public function getLayerScriptPath()
    {
        return $this->_layerScriptPath;
    }

    public function setProfilePath($path)
    {
        $this->_profilePath = (string) $path;
        return $this;
    }

    public function getProfilePath()
    {
        return $this->_profilePath;
    }

    public function setConsumeJavascript($flag)
    {
        $this->_consumeJavascript = (bool) $flag;
        return $this;
    }

    public function consumeJavascript()
    {
        return $this->_consumeJavascript;
    }

    public function setConsumeOnLoad($flag)
    {
        $this->_consumeOnLoad = (bool) $flag;
        return $this;
    }

    public function consumeOnLoad()
    {
        return $this->_consumeOnLoad;
    }

    public function setProfileOptions(array $options)
    {
        $this->_profileOptions += $options;
        return $this;
    }

    public function addProfileOptions(array $options)
    {
        $this->_profileOptions = $this->_profileOptions + $options;
        return $this;
    }

    public function addProfileOption($key, $value)
    {
        $this->_profileOptions[(string) $key] = $value;
        return $this;
    }

    public function hasProfileOption($key)
    {
        return array_key_exists((string) $key, $this->_profileOptions);
    }

    public function getProfileOption($key)
    {
        if ($this->hasProfileOption($key)) {
            return $this->_profileOptions[(string) $key];
        }
        return null;
    }

    public function getProfileOptions()
    {
        return $this->_profileOptions;
    }

    public function removeProfileOption($name)
    {
        if ($this->hasProfileOption($name)) {
            unset($this->_profileOptions[(string) $name]);
        }
        return $this;
    }

    public function clearProfileOptions()
    {
        $this->_profileOptions = array();
        return $this;
    }

    public function addProfilePrefix($prefix, $path = null)
    {
        if (null === $path) {
            $path = '../' . $prefix;
        }
        $this->_profilePrefixes[$prefix] = array($prefix, $path);
        return $this;
    }

    public function setProfilePrefixes(array $prefixes)
    {
        foreach ($prefixes as $prefix => $path) {
            $this->addProfilePrefix($prefix, $path);
        }
        return $this;
    }

    public function getProfilePrefixes()
    {
        $layerName = $this->getLayerName();
        if (null !== $layerName) {
            $prefix    = $this->_getPrefix($layerName);
            if (!array_key_exists($prefix, $this->_profilePrefixes)) {
                $this->addProfilePrefix($prefix);
            }
        }
        $view = $this->getView();
        if (!empty($view)) {
            $helper = $this->getDojoHelper();
            if ($helper) {
                $modules = $helper->getModules();
                foreach ($modules as $module) {
                    $prefix = $this->_getPrefix($module);
                    if (!array_key_exists($prefix, $this->_profilePrefixes)) {
                        $this->addProfilePrefix($prefix);
                    }
                }
            }
        }
        return $this->_profilePrefixes;
    }

    public function generateLayerScript()
    {
        $helper        = $this->getDojoHelper();
        $layerName     = $this->getLayerName();
        $modulePaths   = $helper->getModulePaths();
        $modules       = $helper->getModules();
        $onLoadActions = $helper->getOnLoadActions();
        $javascript    = $helper->getJavascript();

        $content = 'dojo.provide("' . $layerName . '");' . "\n\n(function(){\n";

        foreach ($modulePaths as $module => $path) {
            $content .= sprintf("dojo.registerModulePath(\"%s\", \"%s\");\n", $module, $path);
        }
        foreach ($modules as $module) {
            $content .= sprintf("dojo.require(\"%s\");\n", $module);
        }

        if ($this->consumeOnLoad()) {
            foreach ($helper->getOnLoadActions() as $callback) {
                $content .= sprintf("dojo.addOnLoad(%s);\n", $callback);
            }
        }
        if ($this->consumeJavascript()) {
            $javascript = implode("\n", $helper->getJavascript());
            if (!empty($javascript)) {
                $content .= "\n" . $javascript . "\n";
            }
        }

        $content .= "})();";

        return $content;
    }

    public function generateBuildProfile()
    {
        $profileOptions  = $this->getProfileOptions();
        $layerName       = $this->getLayerName();
        $layerScriptPath = $this->getLayerScriptPath();
        $profilePrefixes = $this->getProfilePrefixes();

        if (!array_key_exists('releaseName', $profileOptions)) {
            $profileOptions['releaseName'] = substr($layerName, 0, strpos($layerName, '.'));
        }

        $profile = $profileOptions;
        $profile['layers'] = array(array(
            'name' => $layerScriptPath,
            'layerDependencies' => array(),
            'dependencies' => array($layerName),
        ));
        $profile['prefixes'] = array_values($profilePrefixes);

        return 'dependencies = ' . $this->_filterJsonProfileToJavascript($profile) . ';';
    }

    protected function _getPrefix($module)
    {
        $segments  = explode('.', $module, 2);
        return $segments[0];
    }

    protected function _filterJsonProfileToJavascript($profile)
    {
        require_once 'Zend/Json.php';
        $profile = Zend_Json::encode($profile);
        $profile = preg_replace('/"([^"]*)":/', '$1:', $profile);
        $profile = preg_replace('/' . preg_quote('\\') . '/', '', $profile);
        return $profile;
    }
}
