### Welcome to the *Zend Framework 2.0.0* Release!

Master: [![Build Status](https://secure.travis-ci.org/zendframework/zf2.png?branch=master)](http://travis-ci.org/zendframework/zf2)

## RELEASE INFORMATION

*Zend Framework 2.0.0rc1*

This is the first release candidate for 2.0.0. We will be releasing RCs
on a weekly basis until we feel all critical issues are addressed. At
this time, we anticipate no API changes before the stable release, and
recommend testing your production applications against it.

25 July 2012

### NEW FEATURES / UPDATES IN RC1

 - Documentation
   - is now in a new repository,
     https://github.com/zendframework/zf2-documentation
   - Documentation has been converted from DocBook5 to ReStructured Text
     (reST or rst).
 - Coding standards fixes
   - We are (mostly) PSR-2 compliant at this time
 - Moved all Service components, include Cloud, GData, OAuth, OpenID,
   and Rest to separate repositories under the zendframework
   organization on GitHub. This will allow them to be versioned
   separately, which allows them to break backwards compatibility when
   necessitated by API changes.
 - Removed Zend\InfoCard as InfoCard has been declared obsolete by
   MicroSoft.
 - Removed Zend\Registry; without the singleton nature, it was confusing
   and no longer relevant.
 - Removed Zend\Test component. Most features are now part of PHPUnit,
   and the others were ZF1-specific.
 - Removed Zend\Wildfire, as its API was specific to ZF1, and because we
   can easily leverage FirePHP at this time.
 - Removed Zend\DocBook, as it was primarily to assist in creating
   DocBook5 skeletons for the manual; since we've moved to rst, this is
   no longer relevant.
 - Removed Zend\Dojo, as the implementation was ZF1 specific, and
   out-of-date with recent Dojo releases.
 - Moved Amf, Markup, Pdf, Queue, Search, and TimeSync to separate
   repositories, as their APIs are not yet stable. PDF will be released
   with 2.0.0rc1, but only to provide a dependency for Zend\Barcode.
 - Renamed any classes, properties, or methods referencing the word
   "configuration" to read "config" instead; this provides consistency
   internally, and with the Zend\Config component.
 - Moved Zend\Acl to Zend\Permissions\Acl.
 - Console
   - Added features for console routing, providing more flexibility over the
     traditional Getopt methodology.
   - Added colorization, tables, prompts, and a variety of other interactive
     features.
   - Added ability to use controllers to respond to console routes.
 - Crypt, Math, Filter\Encrypt and Filter\Decrypt
   - Random number generation was consolidated to Zend\Math.
   - Removed the Mcrypt adapter in Filter and replaced with a
     BlockCipher algorithm.
 - DB
   - Metadata now understands enums and sets
   - Added Replace SQL statement type
   - AbstractRowGateway provides more cohesive access to field values.
 - EventManager
   - The first call to setSharedManager() will now seed the
     StaticEventManager, in order to match user expectations that the
     shared event manager is the same everywhere.
 - Form
   - Select-style elements now have options populated as value => label
     pairs instead of label => value pairs. This is done to ensure that
     option values are unique.
   - Added getValue() and setValue() to the ElementInterface to make a
     semantic distinction between an element value and attributes. This
     allows for easier handling of non-string or calculated values, such
     as those found in the Csrf and DateTime-family of elements.
   - getMessages() now omits any elements that have an empty messages
     array.
   - Allows removing elements from Collections
   - Fixed default validators for MultiCheckbox and Radio elements
   - Custom options are now allowed for all elements, fieldsets, and
     forms.
   - Labels and several other view helpers are now translator-capable
 - Http
   - set/getServer() and set/getEnv() were removed from Http\Request
     and now part of Http\PhpEnvironment\Request
   - set/getFile() methods in Http\PhpEnvironment\Request
     were renamed to set/getFiles(). Also above methods
   - When submitted form has file inputs with brackets (name="file[]")
     $fileParams parameters in Http\PhpEnvironment\Request will be
     re-structured to have the same look as query/post/server/envParams
   - each of get(Post|Query|Headers|Files|Env|Server) were provided with
     two optional arguments, a specific key to retrieve, and a default
     value to return if not found.
   - Accept header parsing is more robust with regards to priority.
 - InputFilter
   - Added ability to retrieve input objects
 - I18n
   - Moved Zend\I18n\Validator\Iban to Zend\Validator\Iban
     and replaced the option "locale" with "country_code"
 - Json
   - Enabled a number of additional flags for json_encode
 - Loader
   - Removed the PrefixPathLoader, and replaced all usages of it in the
     framework with custom AbstractPluginManager implementations; this
     includes Zend\Feed, Zend\Text, and Zend\File\Transfer.
 - Log
   - Added a MongoDB log writer
   - Added a FirePHP log writer
   - Refactored how filters are instantiated and managed to use an
     AbstractPluginManager instance.
 - Mail
   - Added a MessageId header class
 - ModuleManager
   - Made it possible to substitute alternate ServiceListener
     implementations
   - Moved default service configuration from the ModuleManager to the
     ServiceListener
 - MVC
   - Fixed a potential security issue in the ControllerLoader whereby
     arbitrary, non-controller classes could be instantiated. This
     involves removing the ability to fetch controllers via the DI Proxy
     (a minor backwards compatibility break).
   - Restful controller now provides a simpler way to marshal input data
     and override the default behavior.
   - Most View-related services were moved to their own factories to
     allow easier overriding by developers.
   - New PostRedirectGet plugin, to simplify PRG strategies for form
     submissions.
 - Serializer was refactored to make better use of PHP 5.3 features and
   to simplify the API.
 - ServiceManager
   - Allow passing the SM instance to initializers
   - Allow specifying the classname of an InitializerInterface
     implementation to addInitializer()
 - Validator
   - ValidatorChain now has a getValidators() method
   - InArray validator now does context-aware strict checks to prevent
     false positive matches, fixing a potential security vulnerability.
 - View
   - New AbstractTranslatorHelper, for helpers that should allow
     translations.

Almost *200* pull requests for a variety of features and bugfixes were handled
since beta5!

### SYSTEM REQUIREMENTS

Zend Framework 2 requires PHP 5.3.3 or later; we recommend using the
latest PHP version whenever possible.

### INSTALLATION

Please see [INSTALL.md](INSTALL.md).

### CONTRIBUTING

If you wish to contribute to Zend Framework 2.0, please read both the
[README-DEV.md](README-DEV.md) and [README-GIT.md](README-GIT.md) file.

### QUESTIONS AND FEEDBACK

Online documentation can be found at http://framework.zend.com/manual.
Questions that are not addressed in the manual should be directed to the
appropriate mailing list:

http://framework.zend.com/wiki/display/ZFDEV/Mailing+Lists

If you find code in this release behaving in an unexpected manner or
contrary to its documented behavior, please create an issue in the Zend
Framework issue tracker at:

http://framework.zend.com/issues/browse/ZF2

If you would like to be notified of new releases, you can subscribe to
the fw-announce mailing list by sending a blank message to
<fw-announce-subscribe@lists.zend.com>.

### LICENSE

The files in this archive are released under the Zend Framework license.
You can find a copy of this license in [LICENSE.txt](LICENSE.txt).

### ACKNOWLEDGEMENTS

The Zend Framework team would like to thank all the [contributors](https://github.com/zendframework/zf2/contributors) to the Zend
Framework project, our corporate sponsor, and you, the Zend Framework user.
Please visit us sometime soon at http://framework.zend.com.
