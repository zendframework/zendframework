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
 * @package    Zend_Gdata
 * @subpackage Demos
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Simple class to verify that the server that this is run on has a correct
 * installation of the Zend Framework Gdata component.
 */
class InstallationChecker {

    const CSS_WARNING = '.warning { color: #fff; background-color: #AF0007;}';
    const CSS_SUCCESS = '.success { color: #000; background-color: #69FF4F;}';
    const CSS_ERROR = '.error { color: #fff; background-color: #FF9FA3;}';
    const PHP_EXTENSION_ERRORS = 'PHP Extension Errors';
    const PHP_MANUAL_LINK_FRAGMENT = 'http://us.php.net/manual/en/book.';
    const PHP_REQUIREMENT_CHECKER_ID = 'PHP Requirement checker v0.1';
    const SSL_CAPABILITIES_ERRORS = 'SSL Capabilities Errors';
    const YOUTUBE_API_CONNECTIVITY_ERRORS = 'YouTube API Connectivity Errors';
    const ZEND_GDATA_INSTALL_ERRORS = 'Zend Framework Installation Errors';
    const ZEND_SUBVERSION_URI = 'http://framework.zend.com/download/subversion';

    private static $REQUIRED_EXTENSIONS = array(
        'ctype', 'dom', 'libxml', 'spl', 'standard', 'openssl');

    private $_allErrors = array(
        self::PHP_EXTENSION_ERRORS => array(
            'tested' => false, 'errors' => null),
        self::ZEND_GDATA_INSTALL_ERRORS => array(
            'tested' => false, 'errors' => null),
        self::SSL_CAPABILITIES_ERRORS => array(
            'tested' => false, 'errors' => null),
        self::YOUTUBE_API_CONNECTIVITY_ERRORS => array(
            'tested' => false, 'errors' => null)
            );

    private $_sapiModeCLI = null;

    /**
     * Create a new InstallationChecker object and run verifications.
     * @return void
     */
    public function __construct()
    {
        $this->determineIfInCLIMode();
        $this->runAllVerifications();
        $this->outputResults();
    }

    /**
     * Set the sapiModeCLI variable to true if we are running CLI mode.
     *
     * @return void
     */
    private function determineIfInCLIMode()
    {
        if (php_sapi_name() == 'cli') {
            $this->_sapiModeCLI = true;
        }
    }

    /**
     * Getter for sapiModeCLI variable.
     *
     * @return boolean True if we are running in CLI mode.
     */
    public function runningInCLIMode()
    {
        if ($this->_sapiModeCLI) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Run verifications, stopping at each step if there is a failure.
     *
     * @return void
     */
    public function runAllVerifications()
    {
        if (!$this->validatePHPExtensions()) {
            return;
        }
        if (!$this->validateZendFrameworkInstallation()) {
            return;
        }
        if (!$this->testSSLCapabilities()) {
            return;
        }
        if (!$this->validateYouTubeAPIConnectivity()) {
            return;
        }
    }

    /**
     * Validate that the required PHP Extensions are installed and available.
     *
     * @return boolean False if there were errors.
     */
    private function validatePHPExtensions()
    {
        $phpExtensionErrors = array();
        foreach (self::$REQUIRED_EXTENSIONS as $requiredExtension) {
            if (!extension_loaded($requiredExtension)) {
                $requiredExtensionError = $requiredExtension .
                    ' extension missing';
                $documentationLink = null;
                if ($requiredExtension != 'standard') {
                    $documentationLink = self::PHP_MANUAL_LINK_FRAGMENT .
                        $requiredExtension . '.php';
                        $documentationLink =
                            $this->checkAndAddHTMLLink($documentationLink);
                } else {
                    $documentationLink = self::PHP_MANUAL_LINK_FRAGMENT .
                        'spl.php';
                    $documentationLink =
                        $this->checkAndAddHTMLLink($documentationLink);
                }

                if ($documentationLink) {
                    $phpExtensionErrors[] = $requiredExtensionError .
                        ' - refer to ' . $documentationLink;
                }
            }
        }
        $this->_allErrors[self::PHP_EXTENSION_ERRORS]['tested'] = true;
        if (count($phpExtensionErrors) > 0) {
            $this->_allErrors[self::PHP_EXTENSION_ERRORS]['errors'] =
                $phpExtensionErrors;
            return false;
        }
        return true;
    }

    /**
     * Validate that the Gdata component of Zend Framework is installed
     * properly. Also checks that the required YouTube API helper methods are
     * found.
     *
     * @return boolean False if there were errors.
     */
    private function validateZendFrameworkInstallation()
    {
        $zendFrameworkInstallationErrors = array();
        $zendLoaderPresent = false;
        try {
            $zendLoaderPresent = @fopen('Zend/Loader.php', 'r', true);
        } catch (Exception $e) {
            $zendFrameworkInstallationErrors[] = 'Exception thrown trying to ' .
                'access Zend/Loader.php using \'use_include_path\' = true ' .
                'Make sure you include the Zend Framework in your ' .
                'include_path which currently contains: "' .
                ini_get('include_path') . '"';
        }

        if ($zendLoaderPresent) {
            @fclose($zendLoaderPresent);
            require_once('Zend/Loader.php');
            require_once('Zend/Version.php');
            Zend_Loader::loadClass('Zend_Gdata_YouTube');
            Zend_Loader::loadClass('Zend_Gdata_YouTube_VideoEntry');
            $yt = new Zend_Gdata_YouTube();
            $videoEntry = $yt->newVideoEntry();
            if (!method_exists($videoEntry, 'setVideoTitle')) {
                $zendFrameworkMessage = 'Your version of the ' .
                    'Zend Framework ' . Zend_Version::VERSION . ' is too old' .
                    ' to run the YouTube demo application and does not' .
                    ' contain the new helper methods. Please check out a' .
                    ' newer version from Zend\'s repository: ' .
                    checkAndAddHTMLLink(self::ZEND_SUBVERSION_URI);
                $zendFrameworkInstallationErrors[] = $zendFrameworkMessage;
            }
        } else {
            if (count($zendFrameworkInstallationErrors) < 1) {
                $zendFrameworkInstallationErrors[] = 'Exception thrown trying' .
                    ' to access Zend/Loader.php using \'use_include_path\' =' .
                    ' true. Make sure you include Zend Framework in your' .
                    ' include_path which currently contains: ' .
                    ini_get('include_path');
            }
        }

        $this->_allErrors[self::ZEND_GDATA_INSTALL_ERRORS]['tested'] = true;

        if (count($zendFrameworkInstallationErrors) > 0) {
            $this->_allErrors[self::ZEND_GDATA_INSTALL_ERRORS]['errors'] =
                $zendFrameworkInstallationErrors;
            return false;
        }
        return true;
    }

    /**
     * Create HTML link from an input string if not in CLI mode.
     *
     * @param string The error message to be converted to a link.
     * @return string Either the original error message or an HTML version.
     */
    private function checkAndAddHTMLLink($inputString) {
        if (!$this->runningInCLIMode()) {
            return $this->makeHTMLLink($inputString);
        } else {
            return $inputString;
        }
    }

    /**
     * Create an HTML link from a string.
     *
     * @param string The string to be made into link text and anchor target.
     * @return string HTML link.
     */
    private function makeHTMLLink($inputString)
    {
        return '<a href="'. $inputString . '" target="_blank">' .
            $inputString . '</a>';
    }

    /**
     * Validate that SSL Capabilities are available.
     *
     * @return boolean False if there were errors.
     */
    private function testSSLCapabilities()
    {
        $sslCapabilitiesErrors = array();
        require_once 'Zend/Loader.php';
        Zend_Loader::loadClass('Zend_Http_Client');

        $httpClient = new Zend_Http_Client(
            'https://www.google.com/accounts/AuthSubRequest');
        $response = $httpClient->request();
        $this->_allErrors[self::SSL_CAPABILITIES_ERRORS]['tested'] = true;

        if ($response->isError()) {
            $sslCapabilitiesErrors[] = 'Response from trying to access' .
                ' \'https://www.google.com/accounts/AuthSubRequest\' ' .
                $response->getStatus() . ' - ' . $response->getMessage();
        }

        if (count($sslCapabilitiesErrors) > 0) {
            $this->_allErrors[self::SSL_CAPABILITIES_ERRORS]['errors'] =
                $sslCapabilitiesErrors;
            return false;
        }
        return true;
    }

    /**
     * Validate that we can connect to the YouTube API.
     *
     * @return boolean False if there were errors.
     */
    private function validateYouTubeAPIConnectivity()
    {
        $connectivityErrors = array();
        require_once 'Zend/Loader.php';
        Zend_Loader::loadClass('Zend_Gdata_YouTube');
        $yt = new Zend_Gdata_YouTube();
        $topRatedFeed = $yt->getTopRatedVideoFeed();
        if ($topRatedFeed instanceof Zend_Gdata_YouTube_VideoFeed) {
            if ($topRatedFeed->getTotalResults()->getText() < 1) {
                $connectivityErrors[] = 'There was less than 1 video entry' .
                    ' in the \'Top Rated Video Feed\'';
            }
        } else {
            $connectivityErrors[] = 'The call to \'getTopRatedVideoFeed()\' ' .
                'did not result in a Zend_Gdata_YouTube_VideoFeed object';
        }

        $this->_allErrors[self::YOUTUBE_API_CONNECTIVITY_ERRORS]['tested'] =
            true;
        if (count($connectivityErrors) > 0) {
            $this->_allErrors[self::YOUTUBE_API_CONNECTIVITY_ERRORS]['tested'] =
                $connectivityErrors;
            return false;
        }
        return true;
    }

    /**
     * Dispatch a call to outputResultsInHTML or outputResultsInText pending
     * the current SAPI mode.
     *
     * @return void
     */
    public function outputResults()
    {
        if ($this->_sapiModeCLI) {
          print $this->getResultsInText();
        } else {
          print $this->getResultsInHTML();
        }
    }


    /**
     * Return a string representing the results of the verifications.
     *
     * @return string A string representing the results.
     */
    private function getResultsInText()
    {
        $output = "== Ran PHP Installation Checker using CLI ==\n";

        $error_count = 0;
        foreach($this->_allErrors as $key => $value) {
            $output .= $key . ' -- ';
            if (($value['tested'] == true) && (count($value['errors']) == 0)) {
                $output .= "No errors found\n";
            } elseif ($value['tested'] == true) {
                $output .= "Tested\n";
                $error_count = 0;
                foreach ($value['errors'] as $error) {
                    $output .= "Error number: " . $error_count . "\n--" .
                        $error . "\n";
                }
            } else {
                $output .= "Not tested\n";
            }
            $error_count++;
        }
        return $output;
    }

    /**
     * Return an HTML table representing the results of the verifications.
     *
     * @return string An HTML string representing the results.
     */
    private function getResultsInHTML()
    {
        $html = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" " .
            "\"http://www.w3.org/TR/html4/strict.dtd\">\n".
            "<html><head>\n<title>PHP Installation Checker</title>\n" .
            "<style type=\"text/css\">\n" .
            self::CSS_WARNING . "\n" .
            self::CSS_SUCCESS . "\n" .
            self::CSS_ERROR . "\n" .
            "</style></head>\n" .
            "<body>\n<table class=\"verification_table\">" .
            "<caption>Ran PHP Installation Checker on " .
            gmdate('c') . "</caption>\n";

        $error_count = 0;
        foreach($this->_allErrors as $key => $value) {
            $html .= "<tr><td class=\"verification_type\">" . $key . "</td>";
            if (($value['tested'] == true) && (count($value['errors']) == 0)) {
                $html .= "<td class=\"success\">Tested</td></tr>\n" .
                    "<tr><td colspan=\"2\">No errors found</td></tr>\n";
            } elseif ($value['tested'] == true) {
                $html .= "<td class=\"warning\">Tested</td></tr>\n";
                $error_count = 0;
                foreach ($value['errors'] as $error) {
                    $html .= "<tr><td class=\"error\">" . $error_count . "</td>" .
                        "<td class=\"error\">" . $error . "</td></tr>\n";
                }
            } else {
                $html .= "<td class=\"warning\">Not tested</td></tr>\n";
            }
            $error_count++;
        }
        $html .= "</body></html>";
        return $html;
    }
}

$installationChecker = new InstallationChecker();
