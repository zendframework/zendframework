# CHANGELOG

## 2.0.5

- 3004: Zend\Db unit tests fail with code coverage enabled
  (https://github.com/zendframework/zf2/issues/3004)
- 3039: combine double if into single conditional
  (https://github.com/zendframework/zf2/issues/3039)
- 3042: fix typo 'consist of' should be 'consists of' in singular
  (https://github.com/zendframework/zf2/issues/3042)
- 3045: Reduced the #calls of rawurlencode() using a cache mechanism
  (https://github.com/zendframework/zf2/issues/3045)
- 3048: Applying quickfix for zendframework/zf2#3004
  (https://github.com/zendframework/zf2/issues/3048)
- 3095: Process X-Forwarded-For header in correct order
  (https://github.com/zendframework/zf2/issues/3095)

## 2.0.4

- 2808: Add serializer better inheritance and extension
  (https://github.com/zendframework/zf2/issues/2808)
- 2813: Add test on canonical name with the ServiceManager
  (https://github.com/zendframework/zf2/issues/2813)
- 2832: bugfix: The helper DateFormat does not cache correctly when a pattern is
  set. (https://github.com/zendframework/zf2/issues/2832)
- 2837: Add empty option before empty check
  (https://github.com/zendframework/zf2/issues/2837)
- 2843: change self:: with static:: in call-ing static property/method
  (https://github.com/zendframework/zf2/issues/2843)
- 2857: Unnecessary path assembly on return in
  Zend\Mvc\Router\Http\TreeRouteStack->assemble() line 236
  (https://github.com/zendframework/zf2/issues/2857)
- 2867: Enable view sub-directories when using ModuleRouteListener
  (https://github.com/zendframework/zf2/issues/2867)
- 2872: Resolve naming conflicts in foreach statements
  (https://github.com/zendframework/zf2/issues/2872)
- 2878: Fix : change self:: with static:: in call-ing static property/method()
  in other components ( all ) (https://github.com/zendframework/zf2/issues/2878)
- 2879: remove unused const in Zend\Barcode\Barcode.php
  (https://github.com/zendframework/zf2/issues/2879)
- 2896: Constraints in Zend\Db\Metadata\Source\AbstractSource::getTable not
  initalised (https://github.com/zendframework/zf2/issues/2896)
- 2907: Fixed proxy adapter keys being incorrectly set due Zend\Http\Client
  (https://github.com/zendframework/zf2/issues/2907)
- 2909: Change format of Form element DateTime and DateTimeLocal
  (https://github.com/zendframework/zf2/issues/2909)
- 2921: Added Chinese translations for zf2 validate/captcha resources
  (https://github.com/zendframework/zf2/issues/2921)
- 2924: small speed-up of Zend\EventManager\EventManager::triggerListeners()
  (https://github.com/zendframework/zf2/issues/2924)
- 2929: SetCookie::getFieldValue() always uses urlencode() for cookie values,
  even in case they are already encoded
  (https://github.com/zendframework/zf2/issues/2929)
- 2930: Add minor test coverage to MvcEvent
  (https://github.com/zendframework/zf2/issues/2930)
- 2932: Sessions: SessionConfig does not allow setting non-directory save path
  (https://github.com/zendframework/zf2/issues/2932)
- 2937: preserve matched route name within route match instance while
  forwarding... (https://github.com/zendframework/zf2/issues/2937)
- 2940: change 'Cloud\Decorator\Tag' to 'Cloud\Decorator\AbstractTag'
  (https://github.com/zendframework/zf2/issues/2940)
- 2941: Logical operator fix : 'or' change to '||' and 'and' change to '&&'
  (https://github.com/zendframework/zf2/issues/2941)
- 2952: Various Zend\Mvc\Router\Http routers turn + into a space in path
  segments (https://github.com/zendframework/zf2/issues/2952)
- 2957: Make Partial proxy to view render function
  (https://github.com/zendframework/zf2/issues/2957)
- 2971: Zend\Http\Cookie undefined self::CONTEXT_REQUEST
  (https://github.com/zendframework/zf2/issues/2971)
- 2976: Fix for #2541 (https://github.com/zendframework/zf2/issues/2976)
- 2981: Controller action HttpResponse is not used by SendResponseListener
  (https://github.com/zendframework/zf2/issues/2981)
- 2983: replaced all calls to $this->xpath with $this->getXpath() to always
  have... (https://github.com/zendframework/zf2/issues/2983)
- 2986: Add class to file missing a class (fixes #2789)
  (https://github.com/zendframework/zf2/issues/2986)
- 2987: fixed Zend\Session\Container::exchangeArray
  (https://github.com/zendframework/zf2/issues/2987)
- 2994: Fixes #2993 - Add missing asterisk to method docblock
  (https://github.com/zendframework/zf2/issues/2994)
- 2997: Fixing abstract factory instantiation time
  (https://github.com/zendframework/zf2/issues/2997)
- 2999: Fix for GitHub issue 2579
  (https://github.com/zendframework/zf2/issues/2999)
- 3002: update master's resources/ja Zend_Validate.php message
  (https://github.com/zendframework/zf2/issues/3002)
- 3003: Adding tests for zendframework/zf2#2593
  (https://github.com/zendframework/zf2/issues/3003)
- 3006: Hotfix for #2497 (https://github.com/zendframework/zf2/issues/3006)
- 3007: Fix for issue 3001 Zend\Db\Sql\Predicate\Between fails with min and max
  ... (https://github.com/zendframework/zf2/issues/3007)
- 3008: Hotfix for #2482 (https://github.com/zendframework/zf2/issues/3008)
- 3009: Hotfix for #2451 (https://github.com/zendframework/zf2/issues/3009)
- 3013: Solved Issue 2857 (https://github.com/zendframework/zf2/issues/3013)
- 3025: Removing the separator between the hidden and the visible inputs. As
  the... (https://github.com/zendframework/zf2/issues/3025)
- 3027: Reduced #calls of plugin() in PhpRenderer using a cache mechanism
  (https://github.com/zendframework/zf2/issues/3027)
- 3029: Fixed the pre-commit script, missed the fix command
  (https://github.com/zendframework/zf2/issues/3029)
- 3030: Mark module as loaded before trigginer EVENT_LOAD_MODULE
  (https://github.com/zendframework/zf2/issues/3030)
- 3031: Zend\Db\Sql Fix for Insert's Merge and Set capabilities with simlar keys
  (https://github.com/zendframework/zf2/issues/3031)


## 2.0.3:

- 2244: Fix for issue ZF2-503 (https://github.com/zendframework/zf2/issues/2244)
- 2318: Allow to remove decimals in CurrencyFormat
  (https://github.com/zendframework/zf2/issues/2318)
- 2363: Hotfix db features with eventfeature
  (https://github.com/zendframework/zf2/issues/2363)
- 2380: ZF2-482 Attempt to fix the buffer. Also added extra unit tests.
  (https://github.com/zendframework/zf2/issues/2380)
- 2392: Update library/Zend/Db/Adapter/Platform/Mysql.php
  (https://github.com/zendframework/zf2/issues/2392)
- 2395: Fix for http://framework.zend.com/issues/browse/ZF2-571
  (https://github.com/zendframework/zf2/issues/2395)
- 2397: Memcached option merge issuse
  (https://github.com/zendframework/zf2/issues/2397)
- 2402: Adding missing dependencies
  (https://github.com/zendframework/zf2/issues/2402)
- 2404: Fix to comments (https://github.com/zendframework/zf2/issues/2404)
- 2416: Fix expressionParamIndex for AbstractSql
  (https://github.com/zendframework/zf2/issues/2416)
- 2420: Zend\Db\Sql\Select: Fixed issue with join expression named parameters
  overlapping. (https://github.com/zendframework/zf2/issues/2420)
- 2421: Update library/Zend/Http/Header/SetCookie.php
  (https://github.com/zendframework/zf2/issues/2421)
- 2422: fix add 2 space after @param in Zend\Loader
  (https://github.com/zendframework/zf2/issues/2422)
- 2423: ManagerInterface must be interface, remove 'interface' description
  (https://github.com/zendframework/zf2/issues/2423)
- 2425: Use built-in Travis composer
  (https://github.com/zendframework/zf2/issues/2425)
- 2426: Remove need of setter in ClassMethods hydrator
  (https://github.com/zendframework/zf2/issues/2426)
- 2432: Prevent space before end of tag with HTML5 doctype
  (https://github.com/zendframework/zf2/issues/2432)
- 2433: fix for setJsonpCallback not called when recieved JsonModel + test
  (https://github.com/zendframework/zf2/issues/2433)
- 2434: added phpdoc in Zend\Db
  (https://github.com/zendframework/zf2/issues/2434)
- 2437: Hotfix/console 404 reporting
  (https://github.com/zendframework/zf2/issues/2437)
- 2438: Improved previous fix for ZF2-558.
  (https://github.com/zendframework/zf2/issues/2438)
- 2440: Turkish Translations for Captcha and Validate
  (https://github.com/zendframework/zf2/issues/2440)
- 2441: Allow form collection to have any helper
  (https://github.com/zendframework/zf2/issues/2441)
- 2516: limit(20) -> generates LIMIT '20' and throws an IllegalQueryException
  (https://github.com/zendframework/zf2/issues/2516)
- 2545: getSqlStringForSqlObject() returns an invalid SQL statement with LIMIT
  and OFFSET clauses (https://github.com/zendframework/zf2/issues/2545)
- 2595: Pgsql adapater has codes related to MySQL
  (https://github.com/zendframework/zf2/issues/2595)
- 2613: Prevent password to be rendered if form validation fails
  (https://github.com/zendframework/zf2/issues/2613)
- 2617: Fixed Zend\Validator\Iban class name
  (https://github.com/zendframework/zf2/issues/2617)
- 2619: Form enctype fix when File elements are within a collection
  (https://github.com/zendframework/zf2/issues/2619)
- 2620: InputFilter/Input when merging was not using raw value
  (https://github.com/zendframework/zf2/issues/2620)
- 2622: Added ability to specify port
  (https://github.com/zendframework/zf2/issues/2622)
- 2624: Form's default input filters added multiple times
  (https://github.com/zendframework/zf2/issues/2624)
- 2630: fix relative link ( remove the relative links ) in README.md
  (https://github.com/zendframework/zf2/issues/2630)
- 2631: Update library/Zend/Loader/AutoloaderFactory.php
  (https://github.com/zendframework/zf2/issues/2631)
- 2633: fix redundance errors "The input does not appear to be a valid date"
  show twice (https://github.com/zendframework/zf2/issues/2633)
- 2635: Fix potential issue with Sitemap test
  (https://github.com/zendframework/zf2/issues/2635)
- 2636: add isset checks around timeout and maxredirects
  (https://github.com/zendframework/zf2/issues/2636)
- 2641: hotfix : formRow() element error multi-checkbox and radio renderError
  not shown (https://github.com/zendframework/zf2/issues/2641)
- 2642: Fix Travis build for CS issue
  (https://github.com/zendframework/zf2/issues/2642)
- 2643: fix for setJsonpCallback not called when recieved JsonModel + test
  (https://github.com/zendframework/zf2/issues/2643)
- 2644: Add fluidity to the prepare() function for a form
  (https://github.com/zendframework/zf2/issues/2644)
- 2652: Zucchi/filter tweaks (https://github.com/zendframework/zf2/issues/2652)
- 2665: pdftest fix (https://github.com/zendframework/zf2/issues/2665)
- 2666: fixed url change (https://github.com/zendframework/zf2/issues/2666)
- 2667: Possible fix for rartests
  (https://github.com/zendframework/zf2/issues/2667)
- 2669: skip whem gmp is loaded
  (https://github.com/zendframework/zf2/issues/2669)
- 2673: Input fallback value option
  (https://github.com/zendframework/zf2/issues/2673)
- 2676: mysqli::close() never called
  (https://github.com/zendframework/zf2/issues/2676)
- 2677: added phpdoc to Zend\Stdlib
  (https://github.com/zendframework/zf2/issues/2677)
- 2678: Zend\Db\Adapter\Sqlsrv\Sqlsrv never calls Statement\initialize() (fix
  within) (https://github.com/zendframework/zf2/issues/2678)
- 2679: Zend/Log/Logger.php using incorrect php errorLevel
  (https://github.com/zendframework/zf2/issues/2679)
- 2680: Cache: fixed bug on getTotalSpace of filesystem and dba adapter
  (https://github.com/zendframework/zf2/issues/2680)
- 2681: Cache/Dba: fixed notices on tearDown db4 tests
  (https://github.com/zendframework/zf2/issues/2681)
- 2682: Replace 'Configuration' with 'Config' when retrieving configuration
  (https://github.com/zendframework/zf2/issues/2682)
- 2683: Hotfix: Allow items from Abstract Factories to have setShared() called
  (https://github.com/zendframework/zf2/issues/2683)
- 2685: Remove unused Uses (https://github.com/zendframework/zf2/issues/2685)
- 2686: Adding code to allow EventManager trigger listeners using wildcard
  identifier (https://github.com/zendframework/zf2/issues/2686)
- 2687: Hotfix/db sql nested expressions
  (https://github.com/zendframework/zf2/issues/2687)
- 2688: Hotfix/tablegateway event feature
  (https://github.com/zendframework/zf2/issues/2688)
- 2689: Hotfix/composer phpunit
  (https://github.com/zendframework/zf2/issues/2689)
- 2690: Use RFC-3339 full-date format (Y-m-d) in Date element
  (https://github.com/zendframework/zf2/issues/2690)
- 2691: join on conditions don't accept alternatives to columns
  (https://github.com/zendframework/zf2/issues/2691)
- 2693: Update library/Zend/Db/Adapter/Driver/Mysqli/Connection.php
  (https://github.com/zendframework/zf2/issues/2693)
- 2694: Bring fluid interface to Feed Writer
  (https://github.com/zendframework/zf2/issues/2694)
- 2698: fix typo in # should be :: in exception
  (https://github.com/zendframework/zf2/issues/2698)
- 2699: fix elseif in javascript Upload Demo
  (https://github.com/zendframework/zf2/issues/2699)
- 2700: fix cs in casting variable
  (https://github.com/zendframework/zf2/issues/2700)
- 2705: Fix french translation
  (https://github.com/zendframework/zf2/issues/2705)
- 2707: Improved error message when ServiceManager does not find an invokable
  class (https://github.com/zendframework/zf2/issues/2707)
- 2710: #2461 - correcting the url encoding of path segments
  (https://github.com/zendframework/zf2/issues/2710)
- 2711: Fix/demos ProgressBar/ZendForm.php : Object of class Zend\Form\Form
  could not be converted to string
  (https://github.com/zendframework/zf2/issues/2711)
- 2712: fix cs casting variable for (array)
  (https://github.com/zendframework/zf2/issues/2712)
- 2713: Update library/Zend/Mvc/Service/ViewHelperManagerFactory.php
  (https://github.com/zendframework/zf2/issues/2713)
- 2714: Don't add separator if not prefixing columns
  (https://github.com/zendframework/zf2/issues/2714)
- 2717: Extends when it can : Validator\DateStep extends Validator\Date to
  reduce code redundancy (https://github.com/zendframework/zf2/issues/2717)
- 2719: Fixing the Cache Storage Factory Adapter Factory
  (https://github.com/zendframework/zf2/issues/2719)
- 2728: Bad Regex for Content Type header
  (https://github.com/zendframework/zf2/issues/2728)
- 2731: Reset the Order part when resetting Select
  (https://github.com/zendframework/zf2/issues/2731)
- 2732: Removed references to Mysqli in Zend\Db\Adapter\Driver\Pgsql
  (https://github.com/zendframework/zf2/issues/2732)
- 2733: fix @package Zend_Validate should be Zend_Validator
  (https://github.com/zendframework/zf2/issues/2733)
- 2734: fix i18n @package and @subpackage value
  (https://github.com/zendframework/zf2/issues/2734)
- 2736: fix captcha helper test.
  (https://github.com/zendframework/zf2/issues/2736)
- 2737: Issue #2728 - Bad Regex for Content Type header
  (https://github.com/zendframework/zf2/issues/2737)
- 2738: fix link 'quickstart' to version 2.0
  (https://github.com/zendframework/zf2/issues/2738)
- 2739: remove '@subpackage'  because Zend\Math is not in subpackage
  (https://github.com/zendframework/zf2/issues/2739)
- 2742: remove () in echo-ing (https://github.com/zendframework/zf2/issues/2742)
- 2749: Fix for #2678 (Zend\Db's Sqlsrv Driver)
  (https://github.com/zendframework/zf2/issues/2749)
- 2750: Adds the ability to instanciate by factory to AbstractPluginManager
  (https://github.com/zendframework/zf2/issues/2750)
- 2754: add the support to register module paths over namespace
  (https://github.com/zendframework/zf2/issues/2754)
- 2755:  remove Zend\Mvc\Controller\PluginBroker from aliases in
  "$defaultServiceConfig" (https://github.com/zendframework/zf2/issues/2755)
- 2759: Fix Zend\Code\Scanner\TokenArrayScanner
  (https://github.com/zendframework/zf2/issues/2759)
- 2764: Fixed Zend\Math\Rand::getString() to pass the parameter $strong to
  ::getBytes() (https://github.com/zendframework/zf2/issues/2764)
- 2765: Csrf: always use dedicated setter
  (https://github.com/zendframework/zf2/issues/2765)
- 2766: Session\Storage: always preserve REQUEST_ACCESS_TIME
  (https://github.com/zendframework/zf2/issues/2766)
- 2768: Zend\Validator dependency is missed in Zend\Cache composer.json
  (https://github.com/zendframework/zf2/issues/2768)
- 2769: change valueToLDAP to valueToLdap and valueFromLDAP to valueFromLdap
  (https://github.com/zendframework/zf2/issues/2769)
- 2770: Memcached (https://github.com/zendframework/zf2/issues/2770)
- 2775: Zend\Db\Sql: Fix for Mysql quoting during limit and offset
  (https://github.com/zendframework/zf2/issues/2775)
- 2776: Allow whitespace in Iban
  (https://github.com/zendframework/zf2/issues/2776)
- 2777: Fix issue when PREG_BAD_UTF8_OFFSET_ERROR is defined but Unicode support
  is not enabled on PCRE (https://github.com/zendframework/zf2/issues/2777)
- 2778: Undefined Index fix in ViewHelperManagerFactory
  (https://github.com/zendframework/zf2/issues/2778)
- 2779: Allow forms that have been added as fieldsets to bind values to bound
  ob... (https://github.com/zendframework/zf2/issues/2779)
- 2782: Issue 2781 (https://github.com/zendframework/zf2/issues/2782)


## 2.0.2:

- 2383: Changed unreserved char definition in Zend\Uri (ZF2-533) and added shell
  escaping to the test runner (https://github.com/zendframework/zf2/pull/2383)
- 2393: Trying to solve issue ZF2-558
  (https://github.com/zendframework/zf2/pull/2393)
- 2398: Segment route: add fix for optional groups within optional groups
  (https://github.com/zendframework/zf2/pull/2398)
- 2400: Use 'Router' in http env and 'HttpRouter' in cli
  (https://github.com/zendframework/zf2/pull/2400)
- 2401: Better precision for userland fmod algorithm
  (https://github.com/zendframework/zf2/pull/2401)


## 2.0.1:

- 2285: Seed RouteMatch params as long as params is set. This permits setting an
  empty array. (https://github.com/zendframework/zf2/pull/2285)
- 2286: prepareNotFoundViewModel listner -  eventResult as ViewModel if set
  (https://github.com/zendframework/zf2/pull/2286)
- 2290: <span>$label</span> only when filled
  (https://github.com/zendframework/zf2/pull/2290)
- 2292: Allow (int)0 in coomments count in entry feed
  (https://github.com/zendframework/zf2/pull/2292)
- 2295: force to check className parameters
  (https://github.com/zendframework/zf2/pull/2295)
- 2296: mini-fix in controller plugin manager
  (https://github.com/zendframework/zf2/pull/2296)
- 2297: fixed phpdoc in Zend\Mvc\ApplicationInterface
  (https://github.com/zendframework/zf2/pull/2297)
- 2298: Update to Date element use statements to make it clearer which DateTime
  (https://github.com/zendframework/zf2/pull/2298)
- 2300: FormRow translate label fix (#ZF2-516)
  (https://github.com/zendframework/zf2/pull/2300)
- 2302: Notifications now to #zftalk.dev
  (https://github.com/zendframework/zf2/pull/2302)
- 2306: Fix several cs (https://github.com/zendframework/zf2/pull/2306)
- 2307: Removed comment about non existent Zend_Tool
  (https://github.com/zendframework/zf2/pull/2307)
- 2308: Fix pluginmanager get method error
  (https://github.com/zendframework/zf2/pull/2308)
- 2309: Add consistency with event name
  (https://github.com/zendframework/zf2/pull/2309)
- 2310: Update library/Zend/Db/Sql/Select.php
  (https://github.com/zendframework/zf2/pull/2310)
- 2311: Version update (https://github.com/zendframework/zf2/pull/2311)
- 2312: Validator Translations (https://github.com/zendframework/zf2/pull/2312)
- 2313: ZF2-336: Zend\Form adds enctype attribute as multipart/form-data
  (https://github.com/zendframework/zf2/pull/2313)
- 2317: Make Fieldset constructor consistent with parent Element class
  (https://github.com/zendframework/zf2/pull/2317)
- 2321: ZF2-534 Zend\Log\Writer\Syslog prevents setting application name
  (https://github.com/zendframework/zf2/pull/2321)
- 2322: Jump to cache-storing instead of returning
  (https://github.com/zendframework/zf2/pull/2322)
- 2323: Conditional statements improved(minor changes).
  (https://github.com/zendframework/zf2/pull/2323)
- 2324: Fix for ZF2-517: Zend\Mail\Header\GenericHeader fails to parse empty
  header (https://github.com/zendframework/zf2/pull/2324)
- 2328: Wrong \__clone method (https://github.com/zendframework/zf2/pull/2328)
- 2331: added validation support for optgroups
  (https://github.com/zendframework/zf2/pull/2331)
- 2332: README-GIT update with optional pre-commit hook
  (https://github.com/zendframework/zf2/pull/2332)
- 2334: Mail\Message::getSubject() should return value the way it was set
  (https://github.com/zendframework/zf2/pull/2334)
- 2335: ZF2-511 Updated refactored names and other fixes
  (https://github.com/zendframework/zf2/pull/2335)
- 2336: ZF-546 Remove duplicate check for time
  (https://github.com/zendframework/zf2/pull/2336)
- 2337: ZF2-539 Input type of image should not have attribute value
  (https://github.com/zendframework/zf2/pull/2337)
- 2338: ZF2-543: removed linked but not implemented cache adapters
  (https://github.com/zendframework/zf2/pull/2338)
- 2341: Updated Zend_Validate.php pt_BR translation to 25.Jul.2011 EN Revision
  (https://github.com/zendframework/zf2/pull/2341)
- 2342: ZF2-549 Zend\Log\Formatter\ErrorHandler does not handle complex events
  (https://github.com/zendframework/zf2/pull/2342)
- 2346: updated Page\Mvc::isActive to check if the controller param was
  tinkered (https://github.com/zendframework/zf2/pull/2346)
- 2349: Zend\Feed Added unittests for more code coverage
  (https://github.com/zendframework/zf2/pull/2349)
- 2350: Bug in Zend\ModuleManager\Listener\LocatorRegistrationListener
  (https://github.com/zendframework/zf2/pull/2350)
- 2351: ModuleManagerInterface is never used
  (https://github.com/zendframework/zf2/pull/2351)
- 2352: Hotfix for AbstractDb and Csrf Validators
  (https://github.com/zendframework/zf2/pull/2352)
- 2354: Update library/Zend/Feed/Writer/AbstractFeed.php
  (https://github.com/zendframework/zf2/pull/2354)
- 2355: Allow setting CsrfValidatorOptions in constructor
  (https://github.com/zendframework/zf2/pull/2355)
- 2356: Update library/Zend/Http/Cookies.php
  (https://github.com/zendframework/zf2/pull/2356)
- 2357: Update library/Zend/Barcode/Object/AbstractObject.php
  (https://github.com/zendframework/zf2/pull/2357)
- 2358: Update library/Zend/ServiceManager/AbstractPluginManager.php
  (https://github.com/zendframework/zf2/pull/2358)
- 2359: Update library/Zend/Server/Method/Parameter.php
  (https://github.com/zendframework/zf2/pull/2359)
- 2361: Zend\Form Added extra unit tests and some code improvements
  (https://github.com/zendframework/zf2/pull/2361)
- 2364: Remove unused use statements
  (https://github.com/zendframework/zf2/pull/2364)
- 2365: Resolve undefined classes and constants
  (https://github.com/zendframework/zf2/pull/2365)
- 2366: fixed typo in Zend\View\HelperPluginManager
  (https://github.com/zendframework/zf2/pull/2366)
- 2370: Error handling in AbstractWriter::write using Zend\Stdlib\ErrorHandler
  (https://github.com/zendframework/zf2/pull/2370)
- 2372: Update library/Zend/ServiceManager/Config.php
  (https://github.com/zendframework/zf2/pull/2372)
- 2375: zend-inputfilter already requires
  (https://github.com/zendframework/zf2/pull/2375)
- 2376: Activate the new GitHub feature: Contributing Guidelines
  (https://github.com/zendframework/zf2/pull/2376)
- 2377: Update library/Zend/Mvc/Controller/AbstractController.php
  (https://github.com/zendframework/zf2/pull/2377)
- 2379: Typo in property name in Zend/Db/Metadata/Object/AbstractTableObject.php
  (https://github.com/zendframework/zf2/pull/2379)
- 2382: PHPDoc params in AbstractTableGateway.php
  (https://github.com/zendframework/zf2/pull/2382)
- 2384: Replace Router with Http router in url view helper
  (https://github.com/zendframework/zf2/pull/2384)
- 2387: Replace PHP internal fmod function because it gives false negatives
  (https://github.com/zendframework/zf2/pull/2387)
- 2388: Proposed fix for ZF2-569 validating float with trailing 0's (10.0,
  10.10) (https://github.com/zendframework/zf2/pull/2388)
- 2391: clone in Filter\FilterChain
  (https://github.com/zendframework/zf2/pull/2391)
- Security fix: a number of classes were not using the Escaper component in
  order to perform URL, HTML, and/or HTML attribute escaping. Please see
  http://framework.zend.com/security/advisory/ZF2012-03 for more details.
