# CHANGELOG

## 2.1.4 (13 Mar 2013):

- ZF2013-01: Query route (http://framework.zend.com/security/ZF2013-01)
- ZF2013-02: RNG support (http://framework.zend.com/security/ZF2013-02)
- ZF2013-03: DB platform quoting (http://framework.zend.com/security/ZF2013-03)
- 2752: `Zend_Json_Server` to accept null parameters
  (https://github.com/zendframework/zf2/issues/2752)
- 3696: `Zend\Json\Server\Server` should allow parameters with NULL values
  (https://github.com/zendframework/zf2/issues/3696)
- 3767: Allow NULL parameter values in `Zend/Json/Server`
  (https://github.com/zendframework/zf2/issues/3767)
- 3827: Fix mismatches between the PHPDoc and the method signatures
  (https://github.com/zendframework/zf2/issues/3827)
- 3840: allow a null page in pages array, to compensate for ZF issue #3823
  (https://github.com/zendframework/zf2/issues/3840)
- 3842: Hotfix/zend test improve console usage
  (https://github.com/zendframework/zf2/issues/3842)
- 3849: Check if values are set in `Zend\Db\Sql\Insert.php` for prepared
  statement
  (https://github.com/zendframework/zf2/issues/3849)
- 3867: `FileGenerator::setUses()` MUST can take arguments from
  `FileGenerator::getUses()`
  (https://github.com/zendframework/zf2/issues/3867)
- 3868: `ClassGenerator::fromReflection` not generate class properties
  (https://github.com/zendframework/zf2/issues/3868)
- 3869: Remove BC break in `Identical` validator
  (https://github.com/zendframework/zf2/issues/3869)
- 3871: The method delete on the `RowGateway` now returns the affected rows
  (https://github.com/zendframework/zf2/issues/3871)
- 3873: Fixes an issue when binding a model to a form collection element
  (https://github.com/zendframework/zf2/issues/3873)
- 3885: Hotfix/add tests console adapter
  (https://github.com/zendframework/zf2/issues/3885)
- 3886: Add tests console prompt
  (https://github.com/zendframework/zf2/issues/3886)
- 3888: `DefinitionList` `hasMethod` fix
  (https://github.com/zendframework/zf2/issues/3888)
- 3907: Add tests console request response
  (https://github.com/zendframework/zf2/issues/3907)
- 3916: Fix PUT HTTP method usage with params
  (https://github.com/zendframework/zf2/issues/3916)
- 3917: Clean the Console abstract adapter
  (https://github.com/zendframework/zf2/issues/3917)
- 3921: [+BUGFIX] Fixed column names bug `Zend\Db\Sql\Select`
  (https://github.com/zendframework/zf2/issues/3921)
- 3925: Added view and validator dependency
  (https://github.com/zendframework/zf2/issues/3925)
- 3936: Improve the remove of `SendResponseListener`
  (https://github.com/zendframework/zf2/issues/3936)
- 3946: Adding config to `openssl_pkey_export()`
  (https://github.com/zendframework/zf2/issues/3946)
- 3947: fix exception %s passed variable of 'A service by the name or alias %s'  should be $name
  (https://github.com/zendframework/zf2/issues/3947)
- 3948: Bug/merging translator textdomains
  (https://github.com/zendframework/zf2/issues/3948)
- 3950: Fix zero value in argument
  (https://github.com/zendframework/zf2/issues/3950)
- 3957: [Hotfix] Fixed incorrect `PDO_Oci` platform recognition
  (https://github.com/zendframework/zf2/issues/3957)
- 3960: Update toString() to use late static binding for encoding methods
  (https://github.com/zendframework/zf2/issues/3960)
- 3964: Fix fluent interface
  (https://github.com/zendframework/zf2/issues/3964)
- 3966: Better polyfill support for `Stdlib` and `Session`
  (https://github.com/zendframework/zf2/issues/3966)
- 3968: fixed `Exception\InvalidArgumentException` messages in `Zend\Log`
  (https://github.com/zendframework/zf2/issues/3968)
- 3971: SessionArrayStorage doesn't preserve `_REQUEST_ACCESS_TIME`
  (https://github.com/zendframework/zf2/issues/3971)
- 3973: Documentation improvement `Zend\View\Stream`
  (https://github.com/zendframework/zf2/issues/3973)
- 3980: change `HOST_DNS_OR_IPV4_OR_IPV6` to `0x13` for `$validHostTypes`
  (https://github.com/zendframework/zf2/issues/3980)
- 3981: Improve exception messages
  (https://github.com/zendframework/zf2/issues/3981)
- 3982: Fix `\Zend\Soap\AutoDiscover` constructor
  (https://github.com/zendframework/zf2/issues/3982)
- 3984: Update `ArrayStack.php`
  (https://github.com/zendframework/zf2/issues/3984)
- 3987: Fix ChromePhp logger interface and debug level
  (https://github.com/zendframework/zf2/issues/3987)
- 3988: Fix & Unit test for `preparestatement` notices
  (https://github.com/zendframework/zf2/issues/3988)
- 3991: Hotfix/3858 - `findHelper` problem in Navigation Helper
  (https://github.com/zendframework/zf2/issues/3991)
- 3993: `SessionArrayStorage` Request Access Time and Storage Initialization
  (https://github.com/zendframework/zf2/issues/3993)
- 3997: Allow https on scheme without a hostname
  (https://github.com/zendframework/zf2/issues/3997)
- 4001: Fix `ViewFeedStrategyFactory` comment
  (https://github.com/zendframework/zf2/issues/4001)
- 4005: Hotfix/case sensitive console
  (https://github.com/zendframework/zf2/issues/4005)
- 4007: Pass `ClassGenerator` instance instead of boolean
  (https://github.com/zendframework/zf2/issues/4007)
- 4009: Minor if to else if improvement
  (https://github.com/zendframework/zf2/issues/4009)
- 4010: Hotfix/zend test with console route
  (https://github.com/zendframework/zf2/issues/4010)

## 2.1.3 (21 Feb 2013):

- 3714: Zend\Stdlib\ArrayObject::offsetExists() returning by reference
  (https://github.com/zendframework/zf2/issues/3714)
- 3855: Fix #3852
  (https://github.com/zendframework/zf2/issues/3855)
- 3856: Simple route case insensitive
  (https://github.com/zendframework/zf2/issues/3856)

## 2.1.2 (20 Feb 2013):

- 3085: create controller via Zend\Mvc\Controller\ControllerManager
  (https://github.com/zendframework/zf2/issues/3085)
- 3469: ConnectionInterface docblock is wrong or implementation is wrong..
  (https://github.com/zendframework/zf2/issues/3469)
- 3506: [WIP] [#3113] Fix spelling in error validation messages
  (https://github.com/zendframework/zf2/issues/3506)
- 3636: If route has child routes and in URL has arbitrary query like "?lang=de"
  it does not work
  (https://github.com/zendframework/zf2/issues/3636)
- 3652: Query parameter ?action=somevalue will get 404 error
  (https://github.com/zendframework/zf2/issues/3652)
- 3683: Fix to make sure NotEmpty validator is not already set
  (https://github.com/zendframework/zf2/issues/3683)
- 3691: Fix for GitHub issue 3469
  (https://github.com/zendframework/zf2/issues/3691)
- 3698: Openssl error string
  (https://github.com/zendframework/zf2/issues/3698)
- 3699: Certain servers may not set a whitespace after a colon 
  (Set-Cookie: header)
  (https://github.com/zendframework/zf2/issues/3699)
- 3701: Synced pt\_BR\Zend\_Validate.php with en\Zend\_Validate.php
  (https://github.com/zendframework/zf2/issues/3701)
- 3702: added new file: resources\languages\pt\_BR\Zend\_Captcha.php
  (https://github.com/zendframework/zf2/issues/3702)
- 3703: [WIP] Adding parallel testing ANT build configuration and related files
  (https://github.com/zendframework/zf2/issues/3703)
- 3705: Recent composer.json update of stdlib package
  (https://github.com/zendframework/zf2/issues/3705)
- 3706: clear joins and create without columns
  (https://github.com/zendframework/zf2/issues/3706)
- 3707: quoteIdentifier problem in sequence
  (https://github.com/zendframework/zf2/issues/3707)
- 3708: Filter\File\RenameUpload: wrap move\_uploaded\_file to be easly mocked
  (https://github.com/zendframework/zf2/issues/3708)
- 3712: Fix for URIs with a query string not matching
  (https://github.com/zendframework/zf2/issues/3712)
- 3713: Session Container Mismatch & Version Compare fixes for 5.3.3
  (https://github.com/zendframework/zf2/issues/3713)
- 3715: [#3705] Fix autoload.files setting in composer.json
  (https://github.com/zendframework/zf2/issues/3715)
- 3716: Added the Zend\Form decepence in composer.json for Zend\Mvc
  (https://github.com/zendframework/zf2/issues/3716)
- 3721: Created README.md files for each component
  (https://github.com/zendframework/zf2/issues/3721)
- 3722: [Form] [DateTimeSelect] Filter, manager, and view helper fixes
  (https://github.com/zendframework/zf2/issues/3722)
- 3725: Use built-in php constants
  (https://github.com/zendframework/zf2/issues/3725)
- 3729: Zend\Barcode (Fixes #2862)
  (https://github.com/zendframework/zf2/issues/3729)
- 3732: Fix for #2531 - Multiplie navigation don't work
  (https://github.com/zendframework/zf2/issues/3732)
- 3733: Fix/select where
  (https://github.com/zendframework/zf2/issues/3733)
- 3735: [Form] [FormElementManager] don't overwrite form factory if already set
  (https://github.com/zendframework/zf2/issues/3735)
- 3742: Object+hydrator element annotation fix
  (https://github.com/zendframework/zf2/issues/3742)
- 3743: [#3739 & #3740] Using version-compare in accept header handler params.
  (https://github.com/zendframework/zf2/issues/3743)
- 3746: Fix bugs for some locales!
  (https://github.com/zendframework/zf2/issues/3746)
- 3757: Fixed a bug where mail messages were malformed when using the Sendmail
  (https://github.com/zendframework/zf2/issues/3757)
- 3764: Validator File MimeType (IsImage & IsCompressed)
  (https://github.com/zendframework/zf2/issues/3764)
- 3771: Zend\File\Transfer\Adapter\Http on receive : error "File was not found"  in ZF 2.1
  (https://github.com/zendframework/zf2/issues/3771)
- 3778: [#3711] Fix regression in query string matching
  (https://github.com/zendframework/zf2/issues/3778)
- 3782: [WIP] Zend\Di\Di::get() with call parameters ignored shared instances.
  (https://github.com/zendframework/zf2/issues/3782)
- 3783: Provide branch-alias entries for each component composer.json
  (https://github.com/zendframework/zf2/issues/3783)
- 3785: Zend\Db\Sql\Literal Fix when % is used in string
  (https://github.com/zendframework/zf2/issues/3785)
- 3786: Inject shared event manager in initializer
  (https://github.com/zendframework/zf2/issues/3786)
- 3789: Update library/Zend/Mail/Header/AbstractAddressList.php
  (https://github.com/zendframework/zf2/issues/3789)
- 3793: Resolved Issue: #3748 - offsetGet and __get should do a direct proxy to
  $_SESSION
  (https://github.com/zendframework/zf2/issues/3793)
- 3794: Implement query and fragment assembling into the HTTP router itself
  (https://github.com/zendframework/zf2/issues/3794)
- 3797: remove @category, @package, and @subpackage docblocks
  (https://github.com/zendframework/zf2/issues/3797)
- 3798: Remove extra semicolons
  (https://github.com/zendframework/zf2/issues/3798)
- 3803: Fix identical validator
  (https://github.com/zendframework/zf2/issues/3803)
- 3806: Remove obsolete catch statement
  (https://github.com/zendframework/zf2/issues/3806)
- 3807: Resolve undefined classes in phpDoc
  (https://github.com/zendframework/zf2/issues/3807)
- 3808: Add missing @return annotations
  (https://github.com/zendframework/zf2/issues/3808)
- 3813: Bug fix for GlobIterator extending service
  (https://github.com/zendframework/zf2/issues/3813)
- 3817: Add failing tests for Simple console route
  (https://github.com/zendframework/zf2/issues/3817)
- 3819: Allow form element filter to convert a string to array
  (https://github.com/zendframework/zf2/issues/3819)
- 3828: Cannot validate form when keys of collection in data are non consecutive
  (https://github.com/zendframework/zf2/issues/3828)
- 3831: Non-matching argument type for ArrayObject
  (https://github.com/zendframework/zf2/issues/3831)
- 3832: Zend\Db\Sql\Predicate\Predicate->literal() does not work with integer 0
  as $expressionParameters
  (https://github.com/zendframework/zf2/issues/3832)
- 3836: Zend\Db\Sql\Predicate\Predicate Fix for literal() usage
  (https://github.com/zendframework/zf2/issues/3836)
- 3837: Fix for legacy Transfer usage of File Validators
  (https://github.com/zendframework/zf2/issues/3837)
- 3838: Stdlib\ArrayObject & Zend\Session\Container Compatibility with ArrayObject
  (https://github.com/zendframework/zf2/issues/3838)
- 3839: Fixes #2477 - Implemented optional subdomains using regex
  (https://github.com/zendframework/zf2/issues/3839)

## 2.1.1 (06 Feb 2013):

- 2510: Zend\Session\Container does not allow modification by reference
  (https://github.com/zendframework/zf2/issues/2510)
- 2899: Can't inherit abstract function
  Zend\Console\Prompt\PromptInterface::show()
  (https://github.com/zendframework/zf2/issues/2899)
- 3455: Added DISTINCT on Zend\Db\Sql\Select
  (https://github.com/zendframework/zf2/issues/3455)
- 3456: Connection creation added in Pgsql.php createStatement method
  (https://github.com/zendframework/zf2/issues/3456)
- 3608: Fix validate data contains arrays as values
  (https://github.com/zendframework/zf2/issues/3608)
- 3610: Form: rely on specific setter
  (https://github.com/zendframework/zf2/issues/3610)
- 3618: Fix bug when $indent have some string
  (https://github.com/zendframework/zf2/issues/3618)
- 3622: Updated Changelog with BC notes for 2.1 and 2.0.7
  (https://github.com/zendframework/zf2/issues/3622)
- 3623: Authentication using DbTable Adapter doesn't work for 2.1.0
  (https://github.com/zendframework/zf2/issues/3623)
- 3625: Missing instance/object for parameter route upgrading to 2.1.\*
  (https://github.com/zendframework/zf2/issues/3625)
- 3627: Making relative links in Markdown files
  (https://github.com/zendframework/zf2/issues/3627)
- 3629: Zend\Db\Select using alias in joins can results in wrong SQL
  (https://github.com/zendframework/zf2/issues/3629)
- 3638: Fixed method that removed part from parts in Mime\Message
  (https://github.com/zendframework/zf2/issues/3638)
- 3639: Session Metadata and SessionArrayStorage requestaccesstime fixes.
  (https://github.com/zendframework/zf2/issues/3639)
- 3640: [#3625] Do not query abstract factories for registered invokables
  (https://github.com/zendframework/zf2/issues/3640)
- 3641: Zend\Db\Sql\Select Fix for #3629
  (https://github.com/zendframework/zf2/issues/3641)
- 3645: Exception on destructing the SMTP Transport instance
  (https://github.com/zendframework/zf2/issues/3645)
- 3648: Ensure run() always returns Application instance
  (https://github.com/zendframework/zf2/issues/3648)
- 3649: Created script to aggregate return status
  (https://github.com/zendframework/zf2/issues/3649)
- 3650: InjectControllerDependencies initializer overriding an previously
  defined EventManager
  (https://github.com/zendframework/zf2/issues/3650)
- 3651: Hotfix/3650
  (https://github.com/zendframework/zf2/issues/3651)
- 3656: Zend\Validator\Db\AbstractDb.php and mysqli
  (https://github.com/zendframework/zf2/issues/3656)
- 3658: Zend\Validator\Db\AbstractDb.php and mysqli (issue: 3656)
  (https://github.com/zendframework/zf2/issues/3658)
- 3661: ZF HTTP Status Code overwritten
  (https://github.com/zendframework/zf2/issues/3661)
- 3662: Remove double injection in Plugin Controller Manager
  (https://github.com/zendframework/zf2/issues/3662)
- 3663: Remove useless shared in ServiceManager
  (https://github.com/zendframework/zf2/issues/3663)
- 3671: Hotfix/restful head identifier
  (https://github.com/zendframework/zf2/issues/3671)
- 3673: Add translations for Zend\Validator\File\UploadFile
  (https://github.com/zendframework/zf2/issues/3673)
- 3679: remove '\' character from Traversable 
  (https://github.com/zendframework/zf2/issues/3679)
- 3680: Zend\Validator\Db Hotfix (supersedes #3658)
  (https://github.com/zendframework/zf2/issues/3680)
- 3681: [#2899] Remove redundant method declaration
  (https://github.com/zendframework/zf2/issues/3681)
- 3682: Zend\Db\Sql\Select Quantifier (DISTINCT, ALL, + Expression) support -
  supersedes #3455
  (https://github.com/zendframework/zf2/issues/3682)
- 3684: Remove the conditional class declaration of ArrayObject
  (https://github.com/zendframework/zf2/issues/3684)
- 3687: fix invalid docblock
  (https://github.com/zendframework/zf2/issues/3687)
- 3689: [#3684] Polyfill support for version-dependent classes
  (https://github.com/zendframework/zf2/issues/3689)
- 3690: oracle transaction support
  (https://github.com/zendframework/zf2/issues/3690)
- 3692: Hotfix/db parametercontainer mixed use
  (https://github.com/zendframework/zf2/issues/3692)

## 2.1.0 (29 Jan 2013):

- 2378: ZF2-417 Form Annotation Hydrator options support
  (https://github.com/zendframework/zf2/issues/2378)
- 2390: Expose formally protected method in ConfigListener
  (https://github.com/zendframework/zf2/issues/2390)
- 2405: [WIP] Feature/accepted model controller plugin
  (https://github.com/zendframework/zf2/issues/2405)
- 2424: Decorator plugin manager was pointing to an inexistent class
  (https://github.com/zendframework/zf2/issues/2424)
- 2428: Form develop/allow date time
  (https://github.com/zendframework/zf2/issues/2428)
- 2430: [2.1] Added the scrypt key derivation algorithm in Zend\Crypt
  (https://github.com/zendframework/zf2/issues/2430)
- 2439: [2.1] Form File Upload refactor
  (https://github.com/zendframework/zf2/issues/2439)
- 2486: The Upload validator might be broken
  (https://github.com/zendframework/zf2/issues/2486)
- 2506: Throwing exception in template (and/or layout) doesnt fails gracefully
  (https://github.com/zendframework/zf2/issues/2506)
- 2524: Throws exception when trying to generate bcrypt
  (https://github.com/zendframework/zf2/issues/2524)
- 2537: Create a NotIn predicate
  (https://github.com/zendframework/zf2/issues/2537)
- 2616: Initial ZF2 RBAC Component
  (https://github.com/zendframework/zf2/issues/2616)
- 2629: JsonStrategy should set response charset
  (https://github.com/zendframework/zf2/issues/2629)
- 2647: Fix/bcrypt: added the set/get BackwardCompatibility
  (https://github.com/zendframework/zf2/issues/2647)
- 2668: Implement XCache storage adapter (fixes #2581)
  (https://github.com/zendframework/zf2/issues/2668)
- 2671: Added fluent inteface to prepend and set method. Zend\View\Container\AbstractContainer
  (https://github.com/zendframework/zf2/issues/2671)
- 2725: Feature/logger factory
  (https://github.com/zendframework/zf2/issues/2725)
- 2726: Zend\Validator\Explode does not handle NULL
  (https://github.com/zendframework/zf2/issues/2726)
- 2727: Added ability to add additional information to the logs via processors.
  (https://github.com/zendframework/zf2/issues/2727)
- 2772: Adding cookie route. Going to open PR for comments.
  (https://github.com/zendframework/zf2/issues/2772)
- 2815: Fix fro GitHub issue 2600 (Cannot check if a table is read only)
  (https://github.com/zendframework/zf2/issues/2815)
- 2819: Support for ListenerAggregates in SharedEventManager
  (https://github.com/zendframework/zf2/issues/2819)
- 2820: Form plugin manager
  (https://github.com/zendframework/zf2/issues/2820)
- 2863: Handle postgres sequences
  (https://github.com/zendframework/zf2/issues/2863)
- 2876: memcached changes
  (https://github.com/zendframework/zf2/issues/2876)
- 2884: Allow select object to pass on select->join
  (https://github.com/zendframework/zf2/issues/2884)
- 2888: Bugfix dateformat helper
  (https://github.com/zendframework/zf2/issues/2888)
- 2918: \Zend\Mime\Mime::LINEEND causes problems with some SMTP-Servers
  (https://github.com/zendframework/zf2/issues/2918)
- 2945: SOAP 1.2 support for WSDL generation
  (https://github.com/zendframework/zf2/issues/2945)
- 2947: Add DateTimeSelect element to form
  (https://github.com/zendframework/zf2/issues/2947)
- 2950: Abstract row gatewayset from array
  (https://github.com/zendframework/zf2/issues/2950)
- 2968: Zend\Feed\Reader\Extension\Atom\Entry::getAuthors and Feed::getAuthors
  should return Collection\Author
  (https://github.com/zendframework/zf2/issues/2968)
- 2973: Zend\Db\Sql : Create NotIn predicate
  (https://github.com/zendframework/zf2/issues/2973)
- 2977: Method signature of merge() in Zend\Config\Config prevents mocking
  (https://github.com/zendframework/zf2/issues/2977)
- 2988: Cache: Added storage adapter using a session container
  (https://github.com/zendframework/zf2/issues/2988)
- 2990: Added note of new xcache storage adapter
  (https://github.com/zendframework/zf2/issues/2990)
- 3010: [2.1][File Uploads] Multi-File input filtering and FilePRG plugin update
  (https://github.com/zendframework/zf2/issues/3010)
- 3011: Response Json Client
  (https://github.com/zendframework/zf2/issues/3011)
- 3016: [develop] PRG Plugin fixes: Incorrect use of session hops expiration
  (https://github.com/zendframework/zf2/issues/3016)
- 3019: [2.1][develop] PRG Plugins fix
  (https://github.com/zendframework/zf2/issues/3019)
- 3055: Zend Validators complain of array to string conversion for nested array
  values that do not pass validation when using E\_NOTICE
  (https://github.com/zendframework/zf2/issues/3055)
- 3058: [2.1][File Upload] Session Progress fixes
  (https://github.com/zendframework/zf2/issues/3058)
- 3059: [2.1] Add reference to ChromePhp LoggerWriter in WriterPluginManager
  (https://github.com/zendframework/zf2/issues/3059)
- 3069: Hotfix/xcache empty namespace
  (https://github.com/zendframework/zf2/issues/3069)
- 3073: Documentation and code  mismatch
  (https://github.com/zendframework/zf2/issues/3073)
- 3084: Basic support for aggregates in SharedEventManager according to feedback...
  (https://github.com/zendframework/zf2/issues/3084)
- 3086: Updated constructors to accept options array according to AbstractWriter...
  (https://github.com/zendframework/zf2/issues/3086)
- 3088: Zend\Permissions\Rbac roles should inherit parent permissions, not child
  permissions
  (https://github.com/zendframework/zf2/issues/3088)
- 3093: Feature/cookies refactor
  (https://github.com/zendframework/zf2/issues/3093)
- 3105: RFC Send Response Workflow
  (https://github.com/zendframework/zf2/issues/3105)
- 3110: Stdlib\StringUtils
  (https://github.com/zendframework/zf2/issues/3110)
- 3140: Tests for Zend\Cache\Storage\Adapter\MemcachedResourceManager
  (https://github.com/zendframework/zf2/issues/3140)
- 3195: Date element formats not respected in validators.
  (https://github.com/zendframework/zf2/issues/3195)
- 3199: [2.1][FileUploads] FileInput AJAX Post fix
  (https://github.com/zendframework/zf2/issues/3199)
- 3212: Cache: Now an empty namespace means disabling namespace support
  (https://github.com/zendframework/zf2/issues/3212)
- 3215: Check $exception type before throw
  (https://github.com/zendframework/zf2/issues/3215)
- 3219: Fix hook in plugin manager
  (https://github.com/zendframework/zf2/issues/3219)
- 3224: Zend\Db\Sql\Select::getSqlString(Zend\Db\Adapter\Platform\Mysql) doesn't
  work properly with limit param
  (https://github.com/zendframework/zf2/issues/3224)
- 3243: [2.1] Added the support of Apache's passwords
  (https://github.com/zendframework/zf2/issues/3243)
- 3246: [2.1][File Upload] Change file upload filtering to preserve the $\_FILES
  array
  (https://github.com/zendframework/zf2/issues/3246)
- 3247: Fix zend test with the new sendresponselistener
  (https://github.com/zendframework/zf2/issues/3247)
- 3257: Support nested error handler
  (https://github.com/zendframework/zf2/issues/3257)
- 3259: [2.1][File Upload] RenameUpload filter rewrite w/option to use uploaded
  'name'
  (https://github.com/zendframework/zf2/issues/3259)
- 3263: correcting ConsoleResponseSender's __invoke
  (https://github.com/zendframework/zf2/issues/3263)
- 3276: DateElement now support a string
  (https://github.com/zendframework/zf2/issues/3276)
- 3283: fix Undefined function DocBlockReflection::factory error
  (https://github.com/zendframework/zf2/issues/3283)
- 3287: Added missing constructor parameter
  (https://github.com/zendframework/zf2/issues/3287)
- 3308: Update library/Zend/Validator/File/MimeType.php
  (https://github.com/zendframework/zf2/issues/3308)
- 3314: add ContentTransferEncoding Headers
  (https://github.com/zendframework/zf2/issues/3314)
- 3316: Update library/Zend/Mvc/ResponseSender/ConsoleResponseSender.php
  (https://github.com/zendframework/zf2/issues/3316)
- 3334: [2.1][develop] Added missing Exception namespace to Sha1 validator
  (https://github.com/zendframework/zf2/issues/3334)
- 3339: Xterm's 256 colors integration for Console.
  (https://github.com/zendframework/zf2/issues/3339)
- 3343: add SimpleStreamResponseSender + Tests
  (https://github.com/zendframework/zf2/issues/3343)
- 3349: Provide support for more HTTP methods in the AbstractRestfulController
  (https://github.com/zendframework/zf2/issues/3349)
- 3350: Add little more fun to console
  (https://github.com/zendframework/zf2/issues/3350)
- 3357: Add default prototype tags in reflection
  (https://github.com/zendframework/zf2/issues/3357)
- 3359: Added filter possibility
  (https://github.com/zendframework/zf2/issues/3359)
- 3363: Fix minor doc block errors
  (https://github.com/zendframework/zf2/issues/3363)
- 3365: Fix trailing spaces CS error causing all travis builds to fail
  (https://github.com/zendframework/zf2/issues/3365)
- 3366: Zend\Log\Logger::registerErrorHandler() should accept a parameter to set
  the return value of the error_handler callback 
  (https://github.com/zendframework/zf2/issues/3366)
- 3370: [2.1] File PRG plugin issue when merging POST data with nested keys
  (https://github.com/zendframework/zf2/issues/3370)
- 3376: Remove use of deprecated /e-modifier of preg_replace
  (https://github.com/zendframework/zf2/issues/3376)
- 3377: removed test failing since PHP>=5.4
  (https://github.com/zendframework/zf2/issues/3377)
- 3378: Improve code generators consistency
  (https://github.com/zendframework/zf2/issues/3378)
- 3385: render view one last time in case exception thrown from inside view
  (https://github.com/zendframework/zf2/issues/3385)
- 3389: FileExtension validor error in Form context
  (https://github.com/zendframework/zf2/issues/3389)
- 3392: Development branch of AbstractRestfulController->processPostData()
  doesn't handle Content-Type application/x-www-form-urlencoded correctly
  (https://github.com/zendframework/zf2/issues/3392)
- 3404: Provide default $_SESSION array superglobal proxy storage adapter 
  (https://github.com/zendframework/zf2/issues/3404)
- 3405: fix dispatcher to catch legitimate exceptions
  (https://github.com/zendframework/zf2/issues/3405)
- 3414: Zend\Mvc\Controller\AbstractRestfulController: various fixes to Json
  handling
  (https://github.com/zendframework/zf2/issues/3414)
- 3418: [2.1] Additional code comments for FileInput
  (https://github.com/zendframework/zf2/issues/3418)
- 3420: Authentication Validator
  (https://github.com/zendframework/zf2/issues/3420)
- 3421: Allow to set arbitrary status code for Exception strategy
  (https://github.com/zendframework/zf2/issues/3421)
- 3426: Zend\Form\View\Helper\FormSelect
  (https://github.com/zendframework/zf2/issues/3426)
- 3427: `Zend\ModuleManager\Feature\ProvidesDependencyModulesInterface`
  (https://github.com/zendframework/zf2/issues/3427)
- 3440: [#3376] Better fix
  (https://github.com/zendframework/zf2/issues/3440)
- 3442: Better content-type negotiation
  (https://github.com/zendframework/zf2/issues/3442)
- 3446: Zend\Form\Captcha setOptions don't follow interface contract
  (https://github.com/zendframework/zf2/issues/3446)
- 3450: [Session][Auth] Since the recent BC changes to Sessions,
  Zend\Authentication\Storage\Session does not work
  (https://github.com/zendframework/zf2/issues/3450)
- 3454: ACL permissions are not correctly inherited.
  (https://github.com/zendframework/zf2/issues/3454)
- 3458: Session data is empty in Session SaveHandler's write function
  (https://github.com/zendframework/zf2/issues/3458)
- 3461: fix for zendframework/zf2#3458
  (https://github.com/zendframework/zf2/issues/3461)
- 3470: Session not working in current development?
  (https://github.com/zendframework/zf2/issues/3470)
- 3479: Fixed #3454.
  (https://github.com/zendframework/zf2/issues/3479)
- 3482: Feature/rest delete replace collection
  (https://github.com/zendframework/zf2/issues/3482)
- 3483: [#2629] Add charset to Content-Type header
  (https://github.com/zendframework/zf2/issues/3483)
- 3485: Zend\Db Oracle Driver
  (https://github.com/zendframework/zf2/issues/3485)
- 3491: Update library/Zend/Code/Generator/PropertyGenerator.php
  (https://github.com/zendframework/zf2/issues/3491)
- 3493: [Log] fixes #3366: Now Logger::registerErrorHandler() accepts continue
  (https://github.com/zendframework/zf2/issues/3493)
- 3494: [2.1] Zend\Filter\Word\* no longer extends Zend\Filter\PregReplace
  (https://github.com/zendframework/zf2/issues/3494)
- 3495: [2.1] Added Zend\Stdlib\StringUtils::hasPcreUnicodeSupport()
  (https://github.com/zendframework/zf2/issues/3495)
- 3496: [2.1] fixed tons of missing/wrong use statements
  (https://github.com/zendframework/zf2/issues/3496)
- 3498: add method to Zend\Http\Response\Stream
  (https://github.com/zendframework/zf2/issues/3498)
- 3499: removed "self" typehints in Zend\Config and Zend\Mvc
  (https://github.com/zendframework/zf2/issues/3499)
- 3501: Exception while createing RuntimeException in Pdo Connection class
  (https://github.com/zendframework/zf2/issues/3501)
- 3507: hasAcl dosn't cheks $defaultAcl Member Variable
  (https://github.com/zendframework/zf2/issues/3507)
- 3508: Removed all @category, @package, and @subpackage annotations
  (https://github.com/zendframework/zf2/issues/3508)
- 3509: Zend\Form\View\Helper\FormSelect
  (https://github.com/zendframework/zf2/issues/3509)
- 3510: FilePRG: replace array_merge with ArrayUtils::merge
  (https://github.com/zendframework/zf2/issues/3510)
- 3511: Revert PR #3088 as discussed in #3265.
  (https://github.com/zendframework/zf2/issues/3511)
- 3519: Allow to pull route manager from sl
  (https://github.com/zendframework/zf2/issues/3519)
- 3523: Components dependent on Zend\Stdlib but it's not marked in composer.json
  (https://github.com/zendframework/zf2/issues/3523)
- 3531: [2.1] Fix variable Name and Resource on Oracle Driver Test
  (https://github.com/zendframework/zf2/issues/3531)
- 3532: Add legend translation support into formCollection view helper
  (https://github.com/zendframework/zf2/issues/3532)
- 3538: ElementPrepareAwareInterface should use FormInterface
  (https://github.com/zendframework/zf2/issues/3538)
- 3541: \Zend\Filter\Encrypt and \Zend\Filter\Decrypt not working together?
  (https://github.com/zendframework/zf2/issues/3541)
- 3543: Hotfix: Undeprecate PhpEnvironement Response methods
  (https://github.com/zendframework/zf2/issues/3543)
- 3545: Removing service initializer as of zendframework/zf2#3537
  (https://github.com/zendframework/zf2/issues/3545)
- 3546: Add RoleInterface
  (https://github.com/zendframework/zf2/issues/3546)
- 3555: [2.1] [Forms] [Bug] Factory instantiates Elements directly but should be
  using the FormElementManager
  (https://github.com/zendframework/zf2/issues/3555)
- 3556: fix for zendframework/zf2#3555
  (https://github.com/zendframework/zf2/issues/3556)
- 3557: [2.1] Fixes for FilePRG when using nested form elements
  (https://github.com/zendframework/zf2/issues/3557)
- 3559: Feature/translate flash message
  (https://github.com/zendframework/zf2/issues/3559)
- 3561: Zend\Mail SMTP Fix Connection Handling
  (https://github.com/zendframework/zf2/issues/3561)
- 3566: Flash Messenger fixes for PHP < 5.4, and fix for default namespacing
  (https://github.com/zendframework/zf2/issues/3566)
- 3567: Zend\Db: Adapter construction features + IbmDb2 & Oracle Platform
  features
  (https://github.com/zendframework/zf2/issues/3567)
- 3572: Allow to add serializers through config
  (https://github.com/zendframework/zf2/issues/3572)
- 3576: BC Break in Controller Loader, controllers no more present in controller
  loader.
  (https://github.com/zendframework/zf2/issues/3576)
- 3583: [2.1] Fixed an issue on salt check in Apache Password
  (https://github.com/zendframework/zf2/issues/3583)
- 3584: Zend\Db Fix for #3290
  (https://github.com/zendframework/zf2/issues/3584)
- 3585: [2.1] Added the Apache htpasswd support for HTTP Authentication
  (https://github.com/zendframework/zf2/issues/3585)
- 3586: Zend\Db Fix for #2563
  (https://github.com/zendframework/zf2/issues/3586)
- 3587: Zend\Db Fix/Feature for #3294
  (https://github.com/zendframework/zf2/issues/3587)
- 3597: Zend\Db\TableGateway hotfix for MasterSlaveFeature
  (https://github.com/zendframework/zf2/issues/3597)
- 3598: Feature Zend\Db\Adapter\Profiler
  (https://github.com/zendframework/zf2/issues/3598)
- 3599: [WIP] Zend\Db\Sql Literal Objects
  (https://github.com/zendframework/zf2/issues/3599)
- 3600: Fixed the unit test for Zend\Filter\File\Encrypt and Decrypt
  (https://github.com/zendframework/zf2/issues/3600)
- 3605: Restore Zend\File\Transfer
  (https://github.com/zendframework/zf2/issues/3605)
- 3606: Zend\Db\Sql\Select Add Support For SubSelect in Join Table - #2881 &
  #2884
  (https://github.com/zendframework/zf2/issues/3606)

### Potential Breakage

Includes a fix to the classes `Zend\Filter\Encrypt`
and `Zend\Filter\Decrypt` which may pose a small break for end-users. Each
requires an encryption key be passed to either the constructor or the
setKey() method now; this was done to improve the security of each
class.

`Zend\Session` includes a new `Zend\Session\Storage\SessionArrayStorage`
class, which acts as a direct proxy to the $_SESSION superglobal. The
SessionManager class now uses this new storage class by default, in
order to fix an error that occurs when directly manipulating nested
arrays of $_SESSION in third-party code. For most users, the change will
be seamless. Those affected will be those (a) directly accessing the
storage instance, and (b) using object notation to access session
members:

    $foo = null;
    /** @var $storage Zend\Session\Storage\SessionStorage */
    if (isset($storage->foo)) {
        $foo = $storage->foo;
    }

If you are using array notation, as in the following example, your code
remains forwards compatible:

    $foo = null;

    /** @var $storage Zend\Session\Storage\SessionStorage */
    if (isset($storage['foo'])) {
        $foo = $storage['foo'];
    }

If you are not working directly with the storage instance, you will be
unaffected.

For those affected, the following courses of action are possible:

 * Update your code to replace object property notation with array
   notation, OR
 * Initialize and register a Zend\Session\Storage\SessionStorage object
   explicitly with the session manager instance.

## 2.0.8 (13 Mar 2013):

- ZF2013-01: Query route (http://framework.zend.com/security/ZF2013-01)
- ZF2013-02: RNG support (http://framework.zend.com/security/ZF2013-02)
- ZF2013-03: DB platform quoting (http://framework.zend.com/security/ZF2013-03)

## 2.0.7 (29 Jan 2013):

- 1992: [2.1] Adding simple Zend/I18n/Loader/Tmx
  (https://github.com/zendframework/zf2/issues/1992)
- 2024: Add HydratingResultSet::toEntityArray()
  (https://github.com/zendframework/zf2/issues/2024)
- 2031: [2.1] Added MongoDB session save handler
  (https://github.com/zendframework/zf2/issues/2031)
- 2080: [2.1] Added a ChromePhp logger
  (https://github.com/zendframework/zf2/issues/2080)
- 2086: [2.1] Module class map cache
  (https://github.com/zendframework/zf2/issues/2086)
- 2100: [2.1] refresh() method in Redirect plugin
  (https://github.com/zendframework/zf2/issues/2100)
- 2105: [2.1] Feature/unidecoder
  (https://github.com/zendframework/zf2/issues/2105)
- 2106: [2.1] Class annotation scanner
  (https://github.com/zendframework/zf2/issues/2106)
- 2125: [2.1] Add hydrator wildcard and new hydrator strategy
  (https://github.com/zendframework/zf2/issues/2125)
- 2129: [2.1] Feature/overrideable di factories
  (https://github.com/zendframework/zf2/issues/2129)
- 2152: [2.1] [WIP] adding basic table view helper
  (https://github.com/zendframework/zf2/issues/2152)
- 2175: [2.1] Add DateSelect and MonthSelect elements
  (https://github.com/zendframework/zf2/issues/2175)
- 2189: [2.1] Added msgpack serializer
  (https://github.com/zendframework/zf2/issues/2189)
- 2190: [2.1] [WIP] Zend\I18n\Filter\SlugUrl - Made a filter to convert text to
  slugs
  (https://github.com/zendframework/zf2/issues/2190)
- 2208: [2.1] Update library/Zend/View/Helper/HeadScript.php
  (https://github.com/zendframework/zf2/issues/2208)
- 2212: [2.1] Feature/uri normalize filter
  (https://github.com/zendframework/zf2/issues/2212)
- 2225: Zend\Db\Sql : Create NotIn predicate
  (https://github.com/zendframework/zf2/issues/2225)
- 2232: [2.1] Load Messages from other than file
  (https://github.com/zendframework/zf2/issues/2232)
- 2271: [2.1] Ported FingersCrossed handler from monolog to ZF2
  (https://github.com/zendframework/zf2/issues/2271)
- 2288: Allow to create empty option in Select
  (https://github.com/zendframework/zf2/issues/2288)
- 2305: Add support for prev and next link relationships
  (https://github.com/zendframework/zf2/issues/2305)
- 2315: Add MVC service factories for Filters and Validators
  (https://github.com/zendframework/zf2/issues/2315)
- 2316: Add paginator factory & adapter plugin manager
  (https://github.com/zendframework/zf2/issues/2316)
- 2333: Restore mail message from string
  (https://github.com/zendframework/zf2/issues/2333)
- 2339: ZF2-530 Implement PropertyScanner
  (https://github.com/zendframework/zf2/issues/2339)
- 2343: Create Zend Server Monitor Event
  (https://github.com/zendframework/zf2/issues/2343)
- 2367: Convert abstract classes that are only offering static methods
  (https://github.com/zendframework/zf2/issues/2367)
- 2374: Modified Acl/Navigation to be extendable
  (https://github.com/zendframework/zf2/issues/2374)
- 2381: Method Select::from can accept instance of Select as subselect
  (https://github.com/zendframework/zf2/issues/2381)
- 2389: Add plural view helper
  (https://github.com/zendframework/zf2/issues/2389)
- 2396: Rbac component for ZF2
  (https://github.com/zendframework/zf2/issues/2396)
- 2399: Feature/unidecoder new
  (https://github.com/zendframework/zf2/issues/2399)
- 2411: Allow to specify custom pattern for date
  (https://github.com/zendframework/zf2/issues/2411)
- 2414: Added a new validator to check if input is instance of certain class
  (https://github.com/zendframework/zf2/issues/2414)
- 2415: Add plural helper to I18n
  (https://github.com/zendframework/zf2/issues/2415)
- 2417: Allow to render template separately
  (https://github.com/zendframework/zf2/issues/2417)
- 2648: AbstractPluginManager should not respond to...
  (https://github.com/zendframework/zf2/issues/2648)
- 2650: Add view helper and controller plugin to pull the current identity from ...
  (https://github.com/zendframework/zf2/issues/2650)
- 2670: quoteIdentifier() & quoteIdentifierChain() bug
  (https://github.com/zendframework/zf2/issues/2670)
- 2702: Added addUse method in ClassGenerator
  (https://github.com/zendframework/zf2/issues/2702)
- 2704: Functionality/writer plugin manager
  (https://github.com/zendframework/zf2/issues/2704)
- 2706: Feature ini adapter translate
  (https://github.com/zendframework/zf2/issues/2706)
- 2718: Chain authentication storage
  (https://github.com/zendframework/zf2/issues/2718)
- 2774: Fixes #2745 (generate proper query strings).
  (https://github.com/zendframework/zf2/issues/2774)
- 2783: Added methods to allow access to the routes of the SimpleRouteStack.
  (https://github.com/zendframework/zf2/issues/2783)
- 2794: Feature test phpunit lib
  (https://github.com/zendframework/zf2/issues/2794)
- 2801: Improve Zend\Code\Scanner\TokenArrayScanner
  (https://github.com/zendframework/zf2/issues/2801)
- 2807: Add buffer handling to HydratingResultSet
  (https://github.com/zendframework/zf2/issues/2807)
- 2809: Allow Zend\Db\Sql\TableIdentifier in Zend\Db\Sql\Insert, Update & Delete
  (https://github.com/zendframework/zf2/issues/2809)
- 2812: Catch exceptions thrown during rendering
  (https://github.com/zendframework/zf2/issues/2812)
- 2821: Added loadModule.post event to loadModule().
  (https://github.com/zendframework/zf2/issues/2821)
- 2822: Added the ability for FirePhp to understand 'extras' passed to \Zend\Log
  (https://github.com/zendframework/zf2/issues/2822)
- 2841: Allow to remove attribute in form element
  (https://github.com/zendframework/zf2/issues/2841)
- 2844: [Server] & [Soap] Typos and docblocks
  (https://github.com/zendframework/zf2/issues/2844)
- 2848: fixing extract behavior of Zend\Form\Element\Collection and added
  ability to use own fieldset helper within FormCollection-helper
  (https://github.com/zendframework/zf2/issues/2848)
- 2855: add a view event
  (https://github.com/zendframework/zf2/issues/2855)
- 2868: [WIP][Server] Rewrite Reflection API to reuse code from
  Zend\Code\Reflection API
  (https://github.com/zendframework/zf2/issues/2868)
- 2870: [Code] Add support for @throws, multiple types and typed arrays
  (https://github.com/zendframework/zf2/issues/2870)
- 2875: [InputFilter] Adding hasUnknown and getUnknown methods to detect and get
  unknown inputs
  (https://github.com/zendframework/zf2/issues/2875)
- 2919: Select::where should accept PredicateInterface
  (https://github.com/zendframework/zf2/issues/2919)
- 2927: Add a bunch of traits to ZF2
  (https://github.com/zendframework/zf2/issues/2927)
- 2931: Cache: Now an empty namespace means disabling namespace support
  (https://github.com/zendframework/zf2/issues/2931)
- 2953: [WIP] #2743 fix docblock @category/@package/@subpackage
  (https://github.com/zendframework/zf2/issues/2953)
- 2989: Decouple Zend\Db\Sql from concrete Zend\Db\Adapter implementations
  (https://github.com/zendframework/zf2/issues/2989)
- 2995: service proxies / lazy services
  (https://github.com/zendframework/zf2/issues/2995)
- 3017: Fixing the problem with order and \Zend\Db\Sql\Expression
  (https://github.com/zendframework/zf2/issues/3017)
- 3028: Added Json support for POST and PUT operations in restful controller.
  (https://github.com/zendframework/zf2/issues/3028)
- 3056: Add pattern & storage cache factory
  (https://github.com/zendframework/zf2/issues/3056)
- 3057: Pull zend filter compress snappy
  (https://github.com/zendframework/zf2/issues/3057)
- 3078: Allow NodeList to be accessed via array like syntax.
  (https://github.com/zendframework/zf2/issues/3078)
- 3081: Fix for Collection extract method updates targetElement object
  (https://github.com/zendframework/zf2/issues/3081)
- 3106: Added template map generator
  (https://github.com/zendframework/zf2/issues/3106)
- 3189: Added xterm's 256 colors
  (https://github.com/zendframework/zf2/issues/3189)
- 3200: Added ValidatorChain::attach() and ValidatorChain::attachByName() to
  keep consistent with FilterChain
  (https://github.com/zendframework/zf2/issues/3200)
- 3202: Added NTLM authentication support to Zend\Soap\Client\DotNet.
  (https://github.com/zendframework/zf2/issues/3202)
- 3218: Zend-Form: Allow Input Filter Preference Over Element Defaults
  (https://github.com/zendframework/zf2/issues/3218)
- 3230: Add Zend\Stdlib\Hydrator\Strategy\ClosureStrategy
  (https://github.com/zendframework/zf2/issues/3230)
- 3241: Reflection parameter type check
  (https://github.com/zendframework/zf2/issues/3241)
- 3260: Zend/Di, retriving same shared instance for different extra parameters
  (https://github.com/zendframework/zf2/issues/3260)
- 3261: Fix sendmail key
  (https://github.com/zendframework/zf2/issues/3261)
- 3262:  Allows several translation files for same domain / locale 
  (https://github.com/zendframework/zf2/issues/3262)
- 3269: A fix for issue #3195. Date formats are now used during validation.
  (https://github.com/zendframework/zf2/issues/3269)
- 3272: Support for internationalized .IT domain names
  (https://github.com/zendframework/zf2/issues/3272)
- 3273: Parse docblock indented with tabs
  (https://github.com/zendframework/zf2/issues/3273)
- 3285: Fixed wrong return usage and added @throws docblock
  (https://github.com/zendframework/zf2/issues/3285)
- 3286: remove else in already return early
  (https://github.com/zendframework/zf2/issues/3286)
- 3288: Removed unused variable
  (https://github.com/zendframework/zf2/issues/3288)
- 3292: Added Zend Monitor custom event support
  (https://github.com/zendframework/zf2/issues/3292)
- 3295: Proposing removal of subscription record upon unsubscribe
  (https://github.com/zendframework/zf2/issues/3295)
- 3296: Hotfix #3046 - set /dev/urandom as entropy file for Session
  (https://github.com/zendframework/zf2/issues/3296)
- 3298: Add PROPFIND Method to Zend/HTTP/Request
  (https://github.com/zendframework/zf2/issues/3298)
- 3300: Zend\Config - Fix count after merge
  (https://github.com/zendframework/zf2/issues/3300)
- 3302: Fixed #3282
  (https://github.com/zendframework/zf2/issues/3302)
- 3303: Fix indentation, add trailing ',' to last element in array
  (https://github.com/zendframework/zf2/issues/3303)
- 3304: Missed the Zend\Text dependency for Zend\Mvc in composer.json
  (https://github.com/zendframework/zf2/issues/3304)
- 3307: Fix an issue with inheritance of placeholder registry
  (https://github.com/zendframework/zf2/issues/3307)
- 3313: Fix buffering getTotalSpace
  (https://github.com/zendframework/zf2/issues/3313)
- 3317: Fixed FileGenerator::setUse() to ignore already added uses.
  (https://github.com/zendframework/zf2/issues/3317)
- 3318: Fixed FileGenerator::setUses() to allow passing in array of strings.
  (https://github.com/zendframework/zf2/issues/3318)
- 3320: Change @copyright Year : 2012 with 2013
  (https://github.com/zendframework/zf2/issues/3320)
- 3321: remove relative link in CONTRIBUTING.md
  (https://github.com/zendframework/zf2/issues/3321)
- 3322: remove copy variable for no reason
  (https://github.com/zendframework/zf2/issues/3322)
- 3324: enhance strlen to improve performance
  (https://github.com/zendframework/zf2/issues/3324)
- 3326: Minor loop improvements
  (https://github.com/zendframework/zf2/issues/3326)
- 3327: Fix indentation
  (https://github.com/zendframework/zf2/issues/3327)
- 3328: pass on the configured format to the DateValidator instead of hardcoding it
  (https://github.com/zendframework/zf2/issues/3328)
- 3329: Fixed DefinitionList::hasMethod()
  (https://github.com/zendframework/zf2/issues/3329)
- 3331: no chaining in form class' bind method
  (https://github.com/zendframework/zf2/issues/3331)
- 3333: Fixed Zend/Mvc/Router/Http/Segment
  (https://github.com/zendframework/zf2/issues/3333)
- 3340: Add root namespace character
  (https://github.com/zendframework/zf2/issues/3340)
- 3342: change boolean to bool for consistency
  (https://github.com/zendframework/zf2/issues/3342)
- 3345: Update library/Zend/Form/View/Helper/FormRow.php
  (https://github.com/zendframework/zf2/issues/3345)
- 3352: ClassMethods hydrator and wrong method definition
  (https://github.com/zendframework/zf2/issues/3352)
- 3355: Fix for GitHub issue 2511
  (https://github.com/zendframework/zf2/issues/3355)
- 3356: ZF session validators
  (https://github.com/zendframework/zf2/issues/3356)
- 3362: Use CamelCase for naming
  (https://github.com/zendframework/zf2/issues/3362)
- 3369: Removed unused variable in Zend\Json\Decoder.php
  (https://github.com/zendframework/zf2/issues/3369)
- 3386: Adding attributes for a lightweight export
  (https://github.com/zendframework/zf2/issues/3386)
- 3393: [Router] no need to correct ~ in the path encoding
  (https://github.com/zendframework/zf2/issues/3393)
- 3396: change minimal verson of PHPUnit
  (https://github.com/zendframework/zf2/issues/3396)
- 3403: [ZF-8825] Lower-case lookup for "authorization" header
  (https://github.com/zendframework/zf2/issues/3403)
- 3409: Fix for broken handling of
  Zend\ServiceManager\ServiceManager::shareByDefault = false (Issue #3408)
  (https://github.com/zendframework/zf2/issues/3409)
- 3410: [composer] Sync replace package list
  (https://github.com/zendframework/zf2/issues/3410)
- 3415: Remove import of Zend root namespace
  (https://github.com/zendframework/zf2/issues/3415)
- 3423: Issue #3348 fix
  (https://github.com/zendframework/zf2/issues/3423)
- 3425: German Resources Zend\_Validate.php updated.
  (https://github.com/zendframework/zf2/issues/3425)
- 3429: Add __destruct to SessionManager
  (https://github.com/zendframework/zf2/issues/3429)
- 3430: SessionManager: Throw exception when attempting to setId after the
  session has been started
  (https://github.com/zendframework/zf2/issues/3430)
- 3437: Feature/datetime factory format
  (https://github.com/zendframework/zf2/issues/3437)
- 3438: Add @method tags to the AbstractController
  (https://github.com/zendframework/zf2/issues/3438)
- 3439: Individual shared setting does not override the shareByDefault setting
  of the ServiceManager
  (https://github.com/zendframework/zf2/issues/3439)
- 3443: Adding logic to check module dependencies at module loading time
  (https://github.com/zendframework/zf2/issues/3443)
- 3445: Update library/Zend/Validator/Hostname.php
  (https://github.com/zendframework/zf2/issues/3445)
- 3452: Hotfix/session mutability
  (https://github.com/zendframework/zf2/issues/3452)
- 3473: remove surplus call deep namespace
  (https://github.com/zendframework/zf2/issues/3473)
- 3477: The display_exceptions config-option is not passed to 404 views.
  (https://github.com/zendframework/zf2/issues/3477)
- 3480: [Validator][#2538] hostname validator overwrite 
  (https://github.com/zendframework/zf2/issues/3480)
- 3484: [#3055] Remove array to string conversion notice
  (https://github.com/zendframework/zf2/issues/3484)
- 3486: [#3073] Define filter() in Decompress filter
  (https://github.com/zendframework/zf2/issues/3486)
- 3487: [#3446] Allow generic traversable configuration to Captcha element
  (https://github.com/zendframework/zf2/issues/3487)
- 3492: Hotfix/random crypt test fail
  (https://github.com/zendframework/zf2/issues/3492)
- 3502: Features/port supermessenger
  (https://github.com/zendframework/zf2/issues/3502)
- 3513: Fixed bug in acl introduced by acca10b6abe74b3ab51890d5cbe0ab8da4fdf7e0
  (https://github.com/zendframework/zf2/issues/3513)
- 3520: Replace all is_null($value) calls with null === $value
  (https://github.com/zendframework/zf2/issues/3520)
- 3527: Explode validator: allow any value type to be validated
  (https://github.com/zendframework/zf2/issues/3527)
- 3530: The hasACL and hasRole don't check their default member variables
  (https://github.com/zendframework/zf2/issues/3530)
- 3550: Fix for the issue #3541 - salt size for Encrypt/Decrypt Filter
  (https://github.com/zendframework/zf2/issues/3550)
- 3562: Fix: Calling count() results in infinite loop
  (https://github.com/zendframework/zf2/issues/3562)
- 3563: Zend\Db: Fix for #3523 changeset - composer.json and stdlib
  (https://github.com/zendframework/zf2/issues/3563)
- 3571: Correctly parse empty Subject header
  (https://github.com/zendframework/zf2/issues/3571)
- 3575: Fix name of plugin referred to in exception message
  (https://github.com/zendframework/zf2/issues/3575)
- 3579: Some minor fixes in \Zend\View\Helper\HeadScript() class
  (https://github.com/zendframework/zf2/issues/3579)
- 3593: \Zend\Json\Server Fix _getDefaultParams if request-params are an
  associative array
  (https://github.com/zendframework/zf2/issues/3593)
- 3594: Added contstructor to suppressfilter
  (https://github.com/zendframework/zf2/issues/3594)
- 3601: Update Travis to start running tests on PHP 5.5
  (https://github.com/zendframework/zf2/issues/3601)
- 3604: fixed Zend\Log\Logger::registerErrorHandler() doesn't log previous
  exceptions 
  (https://github.com/zendframework/zf2/issues/3604)

### Potential Breakage

Includes a fix to the classes `Zend\Filter\Encrypt`
and `Zend\Filter\Decrypt` which may pose a small break for end-users. Each
requires an encryption key be passed to either the constructor or the
setKey() method now; this was done to improve the security of each
class.

## 2.0.6 (19 Dec 2012):

- 2885: Zend\Db\TableGateway\AbstractTableGateway won't work with Sqlsrv
  db adapter (https://github.com/zendframework/zf2/issues/2885)
- 2922: Fix #2902 (https://github.com/zendframework/zf2/issues/2922)
- 2961: Revert PR #2943 for 5.3.3 fix
  (https://github.com/zendframework/zf2/issues/2961)
- 2962: Allow Accept-Encoding header to be set explicitly by http
  request (https://github.com/zendframework/zf2/issues/2962)
- 3033: Fix error checking on Zend\Http\Client\Adapter\Socket->write().
  (https://github.com/zendframework/zf2/issues/3033)
- 3040: remove unused 'use DOMXPath' and property $count and $xpath
  (https://github.com/zendframework/zf2/issues/3040)
- 3043: improve conditional : reduce file size
  (https://github.com/zendframework/zf2/issues/3043)
- 3044: Extending Zend\Mvc\Router\Http\Segment causes error
  (https://github.com/zendframework/zf2/issues/3044)
- 3047: Fix Zend\Console\Getopt::getUsageMessage()
  (https://github.com/zendframework/zf2/issues/3047)
- 3049: Hotfix/issue #3033
  (https://github.com/zendframework/zf2/issues/3049)
- 3050: Fix : The annotation @\Zend\Form\Annotation\AllowEmpty declared
  on does not accept any values
  (https://github.com/zendframework/zf2/issues/3050)
- 3052: Fixed #3051 (https://github.com/zendframework/zf2/issues/3052)
- 3061: changed it back 'consist' => the 'must' should be applied to all
  parts of the sentence
  (https://github.com/zendframework/zf2/issues/3061)
- 3063: hotfix: change sha382 to sha384 in
  Zend\Crypt\Key\Derivation\SaltedS2k
  (https://github.com/zendframework/zf2/issues/3063)
- 3070: Fix default value unavailable exception for in-build php classes
  (https://github.com/zendframework/zf2/issues/3070)
- 3074: Hotfix/issue #2451 (https://github.com/zendframework/zf2/issues/3074)
- 3091: console exception strategy displays previous exception message
  (https://github.com/zendframework/zf2/issues/3091)
- 3114: Fixed Client to allow also empty passwords in HTTP
  Authentication. (https://github.com/zendframework/zf2/issues/3114)
- 3125: #2607 - Fixing how headers are accessed
  (https://github.com/zendframework/zf2/issues/3125)
- 3126: Fix for GitHub issue 2605
  (https://github.com/zendframework/zf2/issues/3126)
- 3127: fix cs: add space after casting
  (https://github.com/zendframework/zf2/issues/3127)
- 3130: Obey PSR-2 (https://github.com/zendframework/zf2/issues/3130)
- 3144: Zend\Form\View\Helper\Captcha\AbstractWord input and hidden
  attributes (https://github.com/zendframework/zf2/issues/3144)
- 3148: Fixing obsolete method of checking headers, made it use the new
  method. (https://github.com/zendframework/zf2/issues/3148)
- 3149: Zf2634 - Adding missing method Client::encodeAuthHeader
  (https://github.com/zendframework/zf2/issues/3149)
- 3151: Rename variable to what it probably should be
  (https://github.com/zendframework/zf2/issues/3151)
- 3155: strip duplicated semicolon
  (https://github.com/zendframework/zf2/issues/3155)
- 3156: fix typos in docblocks
  (https://github.com/zendframework/zf2/issues/3156)
- 3162: Allow Forms to have an InputFilterSpecification
  (https://github.com/zendframework/zf2/issues/3162)
- 3163: Added support of driver\_options to Mysqli DB Driver
  (https://github.com/zendframework/zf2/issues/3163)
- 3164: Cast $step to float in \Zend\Validator\Step
  (https://github.com/zendframework/zf2/issues/3164)
- 3166: [#2678] Sqlsrv driver incorrectly throwing exception when
  $sqlOrResource... (https://github.com/zendframework/zf2/issues/3166)
- 3167: Fix #3161 by checking if the server port already exists in the
  host (https://github.com/zendframework/zf2/issues/3167)
- 3169: Fixing issue #3036 (https://github.com/zendframework/zf2/issues/3169)
- 3170: Fixing issue #2554 (https://github.com/zendframework/zf2/issues/3170)
- 3171: hotfix : add  '$argName' as 'argument %s' in sprintf ( at 1st
  parameter ) (https://github.com/zendframework/zf2/issues/3171)
- 3178: Maintain priority flag when cloning a Fieldset
  (https://github.com/zendframework/zf2/issues/3178)
- 3184: fix misspelled getCacheStorge()
  (https://github.com/zendframework/zf2/issues/3184)
- 3186: Dispatching to a good controller but wrong action triggers a
  Fatal Error (https://github.com/zendframework/zf2/issues/3186)
- 3187: Fixing ansiColorMap by removing extra m's showed in the console
  (https://github.com/zendframework/zf2/issues/3187)
- 3194: Write clean new line for writeLine method (no background color)
  (https://github.com/zendframework/zf2/issues/3194)
- 3197: Fix spelling error (https://github.com/zendframework/zf2/issues/3197)
- 3201: Session storage set save path
  (https://github.com/zendframework/zf2/issues/3201)
- 3204: [wip] Zend\Http\Client makes 2 requests to url if
  setStream(true) is called
  (https://github.com/zendframework/zf2/issues/3204)
- 3207: dead code clean up.
  (https://github.com/zendframework/zf2/issues/3207)
- 3208: Zend\Mime\Part: Added EOL paramter to getEncodedStream()
  (https://github.com/zendframework/zf2/issues/3208)
- 3213: [#3173] Incorrect creating instance
  Zend/Code/Generator/ClassGenerator.php by fromArray
  (https://github.com/zendframework/zf2/issues/3213)
- 3214: Fix passing of tags to constructor of docblock generator class
  (https://github.com/zendframework/zf2/issues/3214)
- 3217: Cache: Optimized Filesystem::setItem with locking enabled by
  writing the... (https://github.com/zendframework/zf2/issues/3217)
- 3220: [2.0] Log Writer support for MongoClient driver class
  (https://github.com/zendframework/zf2/issues/3220)
- 3226: Licence is not accessable via web
  (https://github.com/zendframework/zf2/issues/3226)
- 3229: fixed bug in DefinitionList::hasMethod()
  (https://github.com/zendframework/zf2/issues/3229)
- 3234: Removed old Form TODO since all items are complete
  (https://github.com/zendframework/zf2/issues/3234)
- 3236: Issue #3222 - Added suport for multi-level nested ini config
  variables (https://github.com/zendframework/zf2/issues/3236)
- 3237: [BUG] Service Manager Not Shared Duplicate new Instance with
  multiple Abstract Factories
  (https://github.com/zendframework/zf2/issues/3237)
- 3238: Added French translation for captcha
  (https://github.com/zendframework/zf2/issues/3238)
- 3250: Issue #2912 - Fix for LicenseTag generation
  (https://github.com/zendframework/zf2/issues/3250)
- 3252: subject prepend text in options for Log\Writer\Mail
  (https://github.com/zendframework/zf2/issues/3252)
- 3254: Better capabilities surrounding console notFoundAction
  (https://github.com/zendframework/zf2/issues/3254)


## 2.0.5 (29 Nov 2012):

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

## 2.0.4 (20 Nov 2012):

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


## 2.0.3 (17 Oct 2012):

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
- 2733: fix @package Zend\_Validate should be Zend\_Validator
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
- 2766: Session\Storage: always preserve REQUEST\_ACCESS\_TIME
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
- 2777: Fix issue when PREG\_BAD\_UTF8__OFFSET_ERROR is defined but Unicode support
  is not enabled on PCRE (https://github.com/zendframework/zf2/issues/2777)
- 2778: Undefined Index fix in ViewHelperManagerFactory
  (https://github.com/zendframework/zf2/issues/2778)
- 2779: Allow forms that have been added as fieldsets to bind values to bound
  ob... (https://github.com/zendframework/zf2/issues/2779)
- 2782: Issue 2781 (https://github.com/zendframework/zf2/issues/2782)


## 2.0.2 (21 Sep 2012):

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


## 2.0.1 (20 Sep 2012):

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
- 2307: Removed comment about non existent Zend\_Tool
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
- 2328: Wrong \_\_clone method (https://github.com/zendframework/zf2/pull/2328)
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
