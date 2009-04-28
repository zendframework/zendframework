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
 * @package    Zend_Ldap
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * @category   Zend
 * @package    Zend_Ldap
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Ldap
{

    const ACCTNAME_FORM_DN        = 1;
    const ACCTNAME_FORM_USERNAME  = 2;
    const ACCTNAME_FORM_BACKSLASH = 3;
    const ACCTNAME_FORM_PRINCIPAL = 4;

    /**
     * String used with ldap_connect for error handling purposes.
     *
     * @var string
     */
    private $_connectString;

    /**
     * The raw LDAP extension resource.
     *
     * @var resource
     */
    protected $_resource = null;

    /**
     * @param  string $str The string to escape.
     * @return string The escaped string
     */
    public static function filterEscape($str)
    {
        $ret = '';
        $len = strlen($str);
        for ($si = 0; $si < $len; $si++) {
            $ch = $str[$si];
            $ord = ord($ch);
            if ($ord < 0x20 || $ord > 0x7e || strstr('*()\/', $ch)) {
                $ch = '\\' . dechex($ord);
            }
            $ret .= $ch;
        }
        return $ret;
    }

    /**
     * @param  string $dn   The DN to parse
     * @param  array  $keys An optional array to receive DN keys (e.g. CN, OU, DC, ...)
     * @param  array  $vals An optional array to receive DN values
     * @return bool   True if the DN was successfully parsed or false if the string is not a valid DN.
     */
    public static function explodeDn($dn, array &$keys = null, array &$vals = null)
    {
        /* This is a classic state machine parser. Each iteration of the
         * loop processes one character. State 1 collects the key. When equals (=)
         * is encountered the state changes to 2 where the value is collected
         * until a comma (,) or semicolon (;) is encountered after which we switch back
         * to state 1. If a backslash (\) is encountered, state 3 is used to collect the
         * following character without engaging the logic of other states.
         */
        $key = null;
        $slen = strlen($dn);
        $state = 1;
        $ko = $vo = 0;
        for ($di = 0; $di <= $slen; $di++) {
            $ch = $di == $slen ? 0 : $dn[$di];
            switch ($state) {
                case 1: // collect key
                    if ($ch === '=') {
                        $key = trim(substr($dn, $ko, $di - $ko));
                        if ($keys !== null) {
                            $keys[] = $key; 
                        }
                        $state = 2;
                        $vo = $di + 1;
                    } else if ($ch === ',' || $ch === ';') {
                        return false;
                    }
                    break;
                case 2: // collect value
                    if ($ch === '\\') {
                        $state = 3;
                    } else if ($ch === ',' || $ch === ';' || $ch === 0) {
                        if ($vals !== null) {
                            $vals[] = trim(substr($dn, $vo, $di - $vo));
                        }
                        $state = 1;
                        $ko = $di + 1;
                    } else if ($ch === '=') {
                        return false;
                    }
                    break;
                case 3: // escaped
                    $state = 2;
                    break;
            }
        }

        return $state === 1 && $ko > 0; 
    }

    /**
     * @param  array $options Options used in connecting, binding, etc.
     * @return void
     */
    public function __construct(array $options = array())
    {
        $this->setOptions($options);
    }

    /**
     * Sets the options used in connecting, binding, etc.
     *
     * Valid option keys:
     *  host
     *  port
     *  useSsl
     *  username
     *  password
     *  bindRequiresDn
     *  baseDn
     *  accountCanonicalForm
     *  accountDomainName
     *  accountDomainNameShort
     *  accountFilterFormat
     *  allowEmptyPassword
     *  useStartTls
     *  optRefferals
     *
     * @param  array $options Options used in connecting, binding, etc.
     * @return Zend_Ldap Provides a fluent interface
     * @throws Zend_Ldap_Exception
     */
    public function setOptions(array $options)
    {
        $permittedOptions = array(
            'host'                      => null,
            'port'                      => null,
            'useSsl'                    => null,
            'username'                  => null,
            'password'                  => null,
            'bindRequiresDn'            => null,
            'baseDn'                    => null,
            'accountCanonicalForm'      => null,
            'accountDomainName'         => null,
            'accountDomainNameShort'    => null,
            'accountFilterFormat'       => null,
            'allowEmptyPassword'        => null,
            'useStartTls'               => null,
            'optReferrals'              => null,
        );

        $diff = array_diff_key($options, $permittedOptions);
        if ($diff) {
            list($key, $val) = each($diff);
            require_once 'Zend/Ldap/Exception.php';
            throw new Zend_Ldap_Exception(null, "Unknown Zend_Ldap option: $key");
        }

        foreach ($permittedOptions as $key => $val) {
            if (!array_key_exists($key, $options)) {
                $options[$key] = null;
            } else {
                /* Enforce typing. This eliminates issues like Zend_Config_Ini
                 * returning '1' as a string (ZF-3163).
                 */
                switch ($key) {
                    case 'port':
                    case 'accountCanonicalForm':
                        $options[$key] = (int)$options[$key];
                        break;
                    case 'useSsl':
                    case 'bindRequiresDn':
                    case 'allowEmptyPassword':
                    case 'useStartTls':
                    case 'optReferrals':
                        $val = $options[$key];
                        $options[$key] = $val === true ||
                                $val === '1' ||
                                strcasecmp($val, 'true') == 0;
                        break;
                }
            }
        }

        $this->_options = $options;

        return $this;
    }

    /**
     * @return array The current options.
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * @return resource The raw LDAP extension resource.
     */
    public function getResource()
    {
        /**
         * @todo by reference?
         */
        return $this->_resource;
    }

    /**
     * @return string The hostname of the LDAP server being used to authenticate accounts
     */
    protected function _getHost()
    {
        return $this->_options['host'];
    }

    /**
     * @return int The port of the LDAP server or 0 to indicate that no port value is set
     */
    protected function _getPort()
    {
        if ($this->_options['port'])
            return $this->_options['port'];
        return 0;
    }

    /**
     * @return string The default acctname for binding
     */
    protected function _getUsername()
    {
        return $this->_options['username'];
    }

    /**
     * @return string The default password for binding
     */
    protected function _getPassword()
    {
        return $this->_options['password'];
    }

    /**
     * @return boolean The default SSL / TLS encrypted transport control
     */
    protected function _getUseSsl()
    {
        return $this->_options['useSsl'];
    }

    /**
     * @return string The default base DN under which objects of interest are located
     */
    protected function _getBaseDn()
    {
        return $this->_options['baseDn'];
    }

    /**
     * @return string Either ACCTNAME_FORM_BACKSLASH, ACCTNAME_FORM_PRINCIPAL or ACCTNAME_FORM_USERNAME indicating the form usernames should be canonicalized to.
     */
    protected function _getAccountCanonicalForm()
    {
        /* Account names should always be qualified with a domain. In some scenarios
         * using non-qualified account names can lead to security vulnerabilities. If
         * no account canonical form is specified, we guess based in what domain
         * names have been supplied.
         */

        $accountCanonicalForm = $this->_options['accountCanonicalForm'];
        if (!$accountCanonicalForm) {
            $accountDomainName = $this->_options['accountDomainName'];
            $accountDomainNameShort = $this->_options['accountDomainNameShort'];
            if ($accountDomainNameShort) {
                $accountCanonicalForm = Zend_Ldap::ACCTNAME_FORM_BACKSLASH;
            } else if ($accountDomainName) {
                $accountCanonicalForm = Zend_Ldap::ACCTNAME_FORM_PRINCIPAL;
            } else {
                $accountCanonicalForm = Zend_Ldap::ACCTNAME_FORM_USERNAME;
            }
        }

        return $accountCanonicalForm;
    }


    /**
     * @return string A format string for building an LDAP search filter to match an account
     */
    protected function _getAccountFilterFormat()
    {
        return $this->_options['accountFilterFormat'];
    }

    /**
     * @return string The LDAP search filter for matching directory accounts
     */
    protected function _getAccountFilter($acctname)
    {
        $this->_splitName($acctname, $dname, $aname);
        $accountFilterFormat = $this->_getAccountFilterFormat();
        $aname = Zend_Ldap::filterEscape($aname);
        if ($accountFilterFormat)
            return sprintf($accountFilterFormat, $aname);
        if (!$this->_options['bindRequiresDn']) {
            // is there a better way to detect this?
            return "(&(objectClass=user)(sAMAccountName=$aname))";
        }
        return "(&(objectClass=posixAccount)(uid=$aname))";
    }

    /**
     * @param string $name The name to split
     * @param string $dname The resulting domain name (this is an out parameter)
     * @param string $aname The resulting account name (this is an out parameter)
     */
    protected function _splitName($name, &$dname, &$aname)
    {
        $dname = NULL;
        $aname = $name;

        $pos = strpos($name, '@');
        if ($pos) {
            $dname = substr($name, $pos + 1);
            $aname = substr($name, 0, $pos);
        } else {
            $pos = strpos($name, '\\');
            if ($pos) {
                $dname = substr($name, 0, $pos);
                $aname = substr($name, $pos + 1);
            }
        }
    }

    /**
     * @param string $acctname The name of the account
     * @return string The DN of the specified account
     * @throws Zend_Ldap_Exception
     */
    protected function _getAccountDn($acctname)
    {
        if (Zend_Ldap::explodeDn($acctname))
            return $acctname;
        $acctname = $this->getCanonicalAccountName($acctname, Zend_Ldap::ACCTNAME_FORM_USERNAME);
        $acct = $this->_getAccount($acctname, array('dn'));
        return $acct['dn'];
    }

    /**
     * @param string $dname The domain name to check
     * @return bool
     */
    protected function _isPossibleAuthority($dname)
    {
        if ($dname === null)
            return true;
        $accountDomainName = $this->_options['accountDomainName'];
        $accountDomainNameShort = $this->_options['accountDomainNameShort'];
        if ($accountDomainName === null && $accountDomainNameShort === null)
            return true;
        if (strcasecmp($dname, $accountDomainName) == 0)
            return true;
        if (strcasecmp($dname, $accountDomainNameShort) == 0)
            return true;
        return false;
    }

    /**
     * @param string $acctname The name to canonicalize
     * @param int $type The desired form of canonicalization
     * @return string The canonicalized name in the desired form
     * @throws Zend_Ldap_Exception
     */
    public function getCanonicalAccountName($acctname, $form = 0)
    {
        $this->_splitName($acctname, $dname, $uname);

        if (!$this->_isPossibleAuthority($dname)) {
            /**
             * @see Zend_Ldap_Exception
             */
            require_once 'Zend/Ldap/Exception.php';
            throw new Zend_Ldap_Exception(null,
                    "Binding domain is not an authority for user: $acctname",
                    Zend_Ldap_Exception::LDAP_X_DOMAIN_MISMATCH);
        }

        if ($form === Zend_Ldap::ACCTNAME_FORM_DN)
            return $this->_getAccountDn($acctname);

        if (!$uname) {
            /**
             * @see Zend_Ldap_Exception
             */
            require_once 'Zend/Ldap/Exception.php';
            throw new Zend_Ldap_Exception(null, "Invalid account name syntax: $acctname");
        }

        $uname = strtolower($uname);

        if ($form === 0)
            $form = $this->_getAccountCanonicalForm();

        switch ($form) {
            case Zend_Ldap::ACCTNAME_FORM_USERNAME:
                return $uname;
            case Zend_Ldap::ACCTNAME_FORM_BACKSLASH:
                $accountDomainNameShort = $this->_options['accountDomainNameShort'];
                if (!$accountDomainNameShort) {
                    /**
                     * @see Zend_Ldap_Exception
                     */
                    require_once 'Zend/Ldap/Exception.php';
                    throw new Zend_Ldap_Exception(null, 'Option required: accountDomainNameShort');
                }
                return "$accountDomainNameShort\\$uname";
            case Zend_Ldap::ACCTNAME_FORM_PRINCIPAL:
                $accountDomainName = $this->_options['accountDomainName'];
                if (!$accountDomainName) {
                    /**
                     * @see Zend_Ldap_Exception
                     */
                    require_once 'Zend/Ldap/Exception.php';
                    throw new Zend_Ldap_Exception(null, 'Option required: accountDomainName');
                }
                return "$uname@$accountDomainName";
            default:
                /**
                 * @see Zend_Ldap_Exception
                 */
                require_once 'Zend/Ldap/Exception.php';
                throw new Zend_Ldap_Exception(null, "Unknown canonical name form: $form");
        }
    }

    /**
     * @param array $attrs An array of names of desired attributes
     * @return array An array of the attributes representing the account
     * @throws Zend_Ldap_Exception
     */
    private function _getAccount($acctname, array $attrs = null)
    {
        $baseDn = $this->_getBaseDn();
        if (!$baseDn) {
            /**
             * @see Zend_Ldap_Exception
             */
            require_once 'Zend/Ldap/Exception.php';
            throw new Zend_Ldap_Exception(null, 'Base DN not set');
        }

        $accountFilter = $this->_getAccountFilter($acctname);
        if (!$accountFilter) {
            /**
             * @see Zend_Ldap_Exception
             */
            require_once 'Zend/Ldap/Exception.php';
            throw new Zend_Ldap_Exception(null, 'Invalid account filter');
        }

        if (!is_resource($this->_resource))
            $this->bind();

        $resource = $this->_resource;
        $str = $accountFilter;
        $code = 0;

        /**
         * @todo break out search operation into simple function (private for now)
         */

        if (!extension_loaded('ldap')) {
            /**
             * @see Zend_Ldap_Exception
             */
            require_once 'Zend/Ldap/Exception.php';
            throw new Zend_Ldap_Exception(null, 'LDAP extension not loaded');
        }

        $result = @ldap_search($resource,
                        $baseDn,
                        $accountFilter,
                        $attrs);
        if (is_resource($result) === true) {
            $count = @ldap_count_entries($resource, $result);
            if ($count == 1) {
                $entry = @ldap_first_entry($resource, $result);
                if ($entry) {
                    $acct = array('dn' => @ldap_get_dn($resource, $entry));
                    $name = @ldap_first_attribute($resource, $entry, $berptr);
                    while ($name) {
                        $data = @ldap_get_values_len($resource, $entry, $name);
                        $acct[$name] = $data;
                        $name = @ldap_next_attribute($resource, $entry, $berptr);
                    }
                    @ldap_free_result($result);
                    return $acct;
                }
            } else if ($count == 0) {
                /**
                 * @see Zend_Ldap_Exception
                 */
                require_once 'Zend/Ldap/Exception.php';
                $code = Zend_Ldap_Exception::LDAP_NO_SUCH_OBJECT;
            } else {

                /**
                 * @todo limit search to 1 record and remove some of this logic?
                 */

                $resource = null;
                $str = "$accountFilter: Unexpected result count: $count";
                /**
                 * @see Zend_Ldap_Exception
                 */
                require_once 'Zend/Ldap/Exception.php';
                $code = Zend_Ldap_Exception::LDAP_OPERATIONS_ERROR;
            }
            @ldap_free_result($result);
        }

        /**
         * @see Zend_Ldap_Exception
         */
        require_once 'Zend/Ldap/Exception.php';
        throw new Zend_Ldap_Exception($resource, $str, $code);
    }

    /**
     * @return Zend_Ldap Provides a fluent interface
     */
    public function disconnect()
    {
        if (is_resource($this->_resource)) {
            if (!extension_loaded('ldap')) {
                /**
                 * @see Zend_Ldap_Exception
                 */
                require_once 'Zend/Ldap/Exception.php';
                throw new Zend_Ldap_Exception(null, 'LDAP extension not loaded');
            }
            @ldap_unbind($this->_resource);
        }
        $this->_resource = null;
        return $this;
    }

    /**
     * @param string $host The hostname of the LDAP server to connect to
     * @param int $port The port number of the LDAP server to connect to
     * @return Zend_Ldap Provides a fluent interface
     * @throws Zend_Ldap_Exception
     */
    public function connect($host = null, $port = 0, $useSsl = false)
    {
        if ($host === null)
            $host = $this->_getHost();
        if ($port === 0)
            $port = $this->_getPort();
        if ($useSsl === false)
            $useSsl = $this->_getUseSsl();

        if (!$host) {
            /**
             * @see Zend_Ldap_Exception
             */
            require_once 'Zend/Ldap/Exception.php';
            throw new Zend_Ldap_Exception(null, 'A host parameter is required');
        }

        /* To connect using SSL it seems the client tries to verify the server
         * certificate by default. One way to disable this behavior is to set
         * 'TLS_REQCERT never' in OpenLDAP's ldap.conf and restarting Apache. Or,
         * if you really care about the server's cert you can put a cert on the
         * web server.
         */
        $url = $useSsl ? "ldaps://$host" : "ldap://$host";
        if ($port) {
            $url .= ":$port";
        }

        /* Because ldap_connect doesn't really try to connect, any connect error
         * will actually occur during the ldap_bind call. Therefore, we save the
         * connect string here for reporting it in error handling in bind().
         */
        $this->_connectString = $url;

        $this->disconnect();

        if (!extension_loaded('ldap')) {
            /**
             * @see Zend_Ldap_Exception
             */
            require_once 'Zend/Ldap/Exception.php';
            throw new Zend_Ldap_Exception(null, 'LDAP extension not loaded');
        }

        /* Only OpenLDAP 2.2 + supports URLs so if SSL is not requested, just
         * use the old form.
         */
        $resource = $useSsl ? @ldap_connect($url) : @ldap_connect($host, $port);

        if (is_resource($resource) === true) {

            $this->_resource = $resource;

            $optReferrals = $this->_options['optReferrals'] ? 1 : 0;

            if (@ldap_set_option($resource, LDAP_OPT_PROTOCOL_VERSION, 3) &&
                        @ldap_set_option($resource, LDAP_OPT_REFERRALS, $optReferrals)) {
                if ($useSsl ||
                            $this->_options['useStartTls'] !== true ||
                            @ldap_start_tls($resource)) {
                    return $this;
                }
            }

            /**
             * @see Zend_Ldap_Exception
             */
            require_once 'Zend/Ldap/Exception.php';

            $zle = new Zend_Ldap_Exception($resource, "$host:$port");
            $this->disconnect();
            throw $zle;
        }
        /**
         * @see Zend_Ldap_Exception
         */
        require_once 'Zend/Ldap/Exception.php';
        throw new Zend_Ldap_Exception("Failed to connect to LDAP server: $host:$port");
    }

    /**
     * @param string $username The username for authenticating the bind
     * @param string $password The password for authenticating the bind
     * @return Zend_Ldap Provides a fluent interface
     * @throws Zend_Ldap_Exception
     */
    public function bind($username = null, $password = null)
    {
        $moreCreds = true;

        if ($username === null) {
            $username = $this->_getUsername();
            $password = $this->_getPassword();
            $moreCreds = false;
        }

        if ($username === NULL) {
            /* Perform anonymous bind
             */
            $password = NULL;
        } else {
            /* Check to make sure the username is in DN form.
             */
            if (!Zend_Ldap::explodeDn($username)) {
                if ($this->_options['bindRequiresDn']) {
                    /* moreCreds stops an infinite loop if _getUsername does not
                     * return a DN and the bind requires it
                     */
                    if ($moreCreds) {
                        try {
                            $username = $this->_getAccountDn($username);
                        } catch (Zend_Ldap_Exception $zle) {
                            /**
                             * @todo Temporary measure to deal with exception thrown for ldap extension not loaded
                             */
                            if (strpos($zle->getMessage(), 'LDAP extension not loaded') !== false) {
                                throw $zle;
                            }
                            // end temporary measure
                            switch ($zle->getCode()) {
                                case Zend_Ldap_Exception::LDAP_NO_SUCH_OBJECT:
                                case Zend_Ldap_Exception::LDAP_X_DOMAIN_MISMATCH:
                                    throw $zle;
                            }
                            throw new Zend_Ldap_Exception(null,
                                        'Failed to retrieve DN for account: ' . $zle->getMessage(),
                                        Zend_Ldap_Exception::LDAP_OPERATIONS_ERROR);
                        }
                    } else {
                        /**
                         * @see Zend_Ldap_Exception
                         */
                        require_once 'Zend/Ldap/Exception.php';
                        throw new Zend_Ldap_Exception(null, 'Binding requires username in DN form');
                    }
                } else {
                    $username = $this->getCanonicalAccountName($username,
                                Zend_Ldap::ACCTNAME_FORM_PRINCIPAL);
                }
            }
        }

        if (!is_resource($this->_resource))
            $this->connect();

        if ($username !== null &&
                    $password === '' &&
                    $this->_options['allowEmptyPassword'] !== true) {
            /**
             * @see Zend_Ldap_Exception
             */
            require_once 'Zend/Ldap/Exception.php';

            $zle = new Zend_Ldap_Exception(null,
                    'Empty password not allowed - see allowEmptyPassword option.');
        } else {
            if (@ldap_bind($this->_resource, $username, $password))
                return $this;

            $message = $username === null ? $this->_connectString : $username;

            /**
             * @see Zend_Ldap_Exception
             */
            require_once 'Zend/Ldap/Exception.php';
    
            switch (Zend_Ldap_Exception::getLdapCode($this)) {
                case Zend_Ldap_Exception::LDAP_SERVER_DOWN:
                    /* If the error is related to establishing a connection rather than binding,
                     * the connect string is more informative than the username.
                     */
                    $message = $this->_connectString;
            }
    
            $zle = new Zend_Ldap_Exception($this->_resource, $message);
        }
        $this->disconnect();
        throw $zle;
    }
}
