### Welcome to the *Zend Framework 2.0.0* Release!

Master: [![Build Status](https://secure.travis-ci.org/zendframework/zf2.png?branch=master)](http://travis-ci.org/zendframework/zf2)

## RELEASE INFORMATION

*Zend Framework 2.0.0beta5*

6 July 2012

THIS RELEASE IS A DEVELOPMENT RELEASE AND NOT INTENDED FOR PRODUCTION USE.
PLEASE USE AT YOUR OWN RISK.

This is the fifth and last in a series of planned beta releases. The
beta release cycle has followed the "gmail" style of betas, whereby new
features have been added in each new release, and BC has not been
guaranteed.

Following this release, we plan to perform some repository cleanup, a
standards audit, and documentation migration. Once these tasks are
complete, we will prepare our first Release Candidate (RC).

### NEW FEATURES IN BETA5

PLEASE NOTE: this beta includes a number of breaks from the previous
beta. Please read the notes below prefixed with "BC BREAK" for specific
breakages. The ZendSkeletonApplication typically reflects any BC changes
that have been made, and is a good resource.

 - Escaper component (Padraic Brady)
    - Provides context-specific escaping mechanisms for HTML content,
      HTML attributes, URLs, CSS, and JavaScript.
    - BC BREAK: The escape() view helper was removed, and replaced with
      escapeHtml(), escapeHtmlAttr(), escapeJs(), escapeCss(), and
      escapeUrl() implementations.
 - New I18n component (Ben Scholzen, Chris Martin, Dennis Portnov,
   Matthew Weier O'Phinney)
    - New component leveraging PHP's ext/intl extension to provide
      internationalization (i18n) and localization (L10n) features and
      capabilities to applications.
    - LEVERAGES:
        - DateTime, DateTimezone, IntlDateFormatter
        - Locale
        - NumberFormatter
    - BC BREAK: REMOVES the following components:
        - Zend\Currency
        - Zend\Date
        - Zend\Locale
        - Zend\Measure
        - Zend\Translator
        - All filters, validators, and view helpers that relied on the
          above.
    - PROVIDES:
        - Zend\I18n\Translator, including support for gettext and
          PHP-array-based translations (more are planned).
        - Zend\I18n\Filter, containing localized filtering capabilites
          for Alnum (alphanumeric), Alpha (alphabetic), and NumberFormat
          (numerical strings).
        - Zend\I18n\Validator, containing localized validation
          capabilities for Alnum (alphanumeric), Alpha (alphabetic),
          Iban (international bank account number standard), Int
          (integer), and PostCode (localized postal codes).
        - Zend\I18n\View, containing localized view helpers for
          CurrencyFormat, DateFormat, NumberFormat, Translate, and
          TranslatePlural.
 - Db layer additions (Ralph Schindler, Rob Allen, Guillaume Metayer,
   Sascha Howe, Chris Testroet, Evan Coury, Ben Youngblood)
    - Metadata support
    - Postgresql adapter/driver
    - New HydratingResultSet, allowing the ability to specify a custom
      hydrator (from Zend\Stdlib\Hydrator) for hydrating row objects.
    - Many bugfixes and stabilizations
 - Form additions (Matthew Weier O'Phinney, Michaël Gallego, Yanick Rochon)
    - Annotations support: Ability to use annotations with a domain
      object in order to define a form, fieldsets, elements, inputs and
      input filters, and more.
    - Hydration of fieldsets; fieldsets may compose their own hydrators
      if desired.
    - Collection support; allows multiple instances of the same
      fieldset. As an example, you might have an interface that
      allows adding a set of form elements via an XHR call; on the
      backend, these would be defined as a collection, allowing
      arbitrary numbers of these fieldsets to be submitted.
    - New view helpers covering most HTML5-specific element types, most
      XHTML-specific element types. Additionally, a number of the
      HTML5-specific element types now have Element implementations to
      create turn-key solutions that include validation and filtering.
    - BC BREAK: Options support. Many attributes were being used not as
      HTML attributes but to define behavior. The ElementInterface now
      has an accessor and mutator for options. Examples of options
      include labels for non-radio/checkbox/select elements, the CAPTCHA
      adapter for CAPTCHA elements, CSRF tokens, etc. If you were
      defining labels in your forms, please move the label and label
      attributes definitions from the "attributes" to the "options" of
      the element, fieldset, or form.
    - BC BREAK: new interface, ElementPrepareAwareInterface, defining
      the method "prepareElement(Form $form)". The FieldsetInterface,
      and, by extension, FormInterface, extend this new interface. It is
      used to allow preparing elements prior to creating a
      representation.
 - MVC additions (Kyle Spraggs, Evan Coury, Matthew Weier O'Phinney)
    - New "Params" controller plugin. Allows retrieving query, post,
      cookie, header, and route parameters. Usage is
      $this->params()->fromQuery($name, $default).
    - New listener, Zend\Mvc\ModuleRouteListener. When enabled, if a
      route match contains a "\__NAMESPACE__" key, that namespace value
      will be prepended to the value of the "controller" key. This
      should typically be used in the root route for a given module, to
      ensure controller names do not clash.
    - Bootstrap simplification. A new "init()" method was created that
      accepts the path to a configuration file, and then creates and
      bootstraps the application; this eliminates all common boilerplate
      for the bootstrap scripts.
 - Hydrator changes (Adam Lundrigan)
    - BC BREAK: the ClassMethods hydrator now assumes by default that
      it should convert between underscore_separated names and
      camelCase.
 - BC BREAK: Doctrine Annotations Parser (Matthew Weier O'Phinney, Marco
   Pivetta, Guilherme Blanco)
    - Zend\Code\Annotation now has a dependency on Doctrine\Common for
      its annotation parser.
    - Annotations now conform to Doctrine's standards by default, but
      the AnnotationManager in ZF2 allows attaching alternate parsers
      for specific annotation types.
 - BC BREAK: Removal of Plugin Broker usage (Matthew Weier O'Phinney,
   Evan Coury)
    - All uses of the Plugin Broker / Plugin Class Locator combination
      were removed. A new class, Zend\ServiceManager\AbstractPluginManager, 
      was created and used to replace all previous usages of the plugin
      broker. This provides more flexibility in creation of plugins, as
      well as reduces the number of APIs developers need to learn.
    - Configuration of plugin managers is now done at the top-level. All
      plugin manager configuration follows the format utilized by
      Zend\ServiceManager\ServiceConfiguration, and
      Zend\ModuleManager\Listener\ServiceListener has been updated to
      allow informing it of plugin manager instances it should manage,
      as well as the configuration key to utilize.
 - BC BREAK: Coding Standards (Maks3w, Sascha Prolic, Rob Allen)
    - Renamed most abstract classes to prefix them with the term
      "Abstract". In particular, ActionController and RestfulController
      are now AbstractActionController and AbstractRestfulController.
    - Renamed getters in HTTP, EventManager, and Mail components. These
      components were using accessors such as "events()", "query()",
      "headers()", etc. All such accessors were renamed to prepend
      "get", and, in the case of "events()", renamed to indicate the
      actual object retrieved ("getEventManager()"). 
 - SECURITY FIX: XmlRpc (Matthew Weier O'Phinney)
    - A security issue arising from XML eXternal Entity (XXE) injection
      was patched; see http://framework.zend.com/security/advisory/ZF2012-01

Over *400* pull requests for a variety of features and bugfixes were handled
since beta4!

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
