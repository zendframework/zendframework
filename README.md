### Welcome to the *Zend Framework 2.0.0* Release!

Master: [![Build Status](https://secure.travis-ci.org/zendframework/zf2.png?branch=master)](http://travis-ci.org/zendframework/zf2)

## RELEASE INFORMATION

*Zend Framework 2.0.0beta4*

THIS RELEASE IS A DEVELOPMENT RELEASE AND NOT INTENDED FOR PRODUCTION USE.
PLEASE USE AT YOUR OWN RISK.

This is the fourth in a series of planned beta releases. The beta release
cycle will follow the "gmail" style of betas, whereby new features will
be added in each new release, and BC will not be guaranteed; beta
releases will happen _approximately_ every six weeks. 

Once the established milestones have been reached and the featureset has reached
maturity and reasonable stability, we will freeze the API and prepare for
Release Candidate (RC) status. At this time, we are only planning for one more
beta release (beta5) before starting the RC process.

### NEW FEATURES IN BETA4

 - Config component (Enrico Zimuel)
    -  Added reader and writer implementations for JSON and YAML configuration
 - Crypt and Math (Enrico Zimuel)
    - Creates a generic API for string and stream en/decryption
    - Provides bcrypt support
    - Provides BigInteger support
    - Provides common methodology surrounding credential encryption and hashing
 - Db layer (Ralph Schindler)
 - EventManager (Matthew Weier O'Phinney)
    - New SharedEventManager, a non-static version of the original
      StaticEventManager
    - StaticEventManager now extends SharedEventManager and implements a
      singleton pattern
    - New ServiceManager creates a shared instance of SharedEventManager and
      injects it in a non-shared EventManager instance per service; static usage
      is discouraged at this time.
    - attachAggregate() now accepts an optional $priority, which, when present,
      will be passed to the ListenerAggregate, allowing specifying a priority
      during attachment of its events.
    - EventManager now can handle arrays of events as well as wildcard events
    - SharedEventManager now can handle arrays of contexts, wildcard contexts,
      and arrays/wildcard events.
 - Form (Matthew Weier O'Phinney, Kyle Spraggs, Guilherme Blanco)
    - Complete rewrite
    - Elements compose a name and attributes
    - Fieldsets compose a name, attributes, and elements and fieldsets
    - Forms compose a name, attributes, elements, fieldsets, an InputFilter, and
      optionally a Hydrator and bound object.
    - New form view helpers accept the Form objects in order to generate markup.
    - Object binding allows direct binding of model data to and from the Form.
 - InputFilter (Matthew Weier O'Phinney)
    - New component for object-oriented creation of input filters
    - Input objects compose filter and validator chains, as well as metadata
      such as required, allow empty, break on failure, and more.
    - InputFilter objects compose Input and InputFilter objects, and allow
      validating the entire set or specified validation groups.
 - Log (Enrico Zimuel, Benoit Durand)
    - Refactored to provide more flexibility
    - Adds API discoverability (instead of method overloading)
    - Uses the PluginBroker for loading writers and formatters
    - Uses PriorityQueue to manage writer priority
    - Uses FilterChain for filtering messages
    - Adds a renderer for exceptions, a JSON formatter, and additional interfaces
 - Mail (Enrico Zimuel)
    - Allow batch sending via the SMTP transport
 - ModuleManager (Evan Coury, Matthew Weier O'Phinney)
    - Renamed from "Module" to "ModuleManager"
    - Renamed "Consumer" subnamespace to "Feature"
    - Added new listeners:
      - OnBootstrapListener (Module classes defining onBootstrap() will have
        that method attached as a listener on the Application bootstrap event)
      - LocatorRegistrationListener (Module classes implementing the
        LocatorRegisteredInterface feature will be injected in the
        ServiceManager)
      - ServiceListener (Module classes defining getServiceConfiguration() will
        have that method called, and the configuration merged; once all modules
        are loaded, that merged configuration will be passed to the
        ServiceManager)
 - MVC (Matthew Weier O'Phinney, Ralph Schindler, Evan Coury)
    - Removed Bootstrap class and rewrote Application class
      - Composes a ServiceManager, and simply fires events
    - Added RouteListener and DispatchListener classes, implementing the default
      route and dispatch strategies.
    - Created a new "Service" subnamespace, with ServiceManager configuration
      and factories for the default MVC services.
    - Created a new "ViewManager" class, which triggers on the bootstrap event,
      at which time it creates the various objects of the view layer and wires
      them together as well as registers them with the appropriate events.
 - ServiceManager component (Ralph Schindler, Matthew Weier O'Phinney)
    - Highly performant, programmatic service creation
    - Largely replaces DI, but can also consume Zend\Di
    - Allows:
      - Service registration
      - Lazy-loaded service objects
      - Service factories
      - Service aliasing
      - Abstract (fallback) factories
      - Initializers (manipulate instances after creation)
    - Fully integrated in the MVC solution
 - Renamed interfaces
   - Most, if not all, interfaces were renamed to suffix with the word
     "Interface". This is to promote discovery of interfaces, as well as make
     naming simpler.
 - Composer support
   - Zend Framework is now installable via Composer (http://packagist.org/), as
     are each of its individual components
 - Travis CI integration
   - ZF2 is tested on each commit by http://travis-ci.org/

Over *400* pull requests for a variety of features and bugfixes were handled
since beta3!

### SYSTEM REQUIREMENTS

Zend Framework 2 requires PHP 5.3.3 or later; we recommend using the
latest PHP version whenever possible.

### INSTALLATION

Please see INSTALL.md.

### CONTRIBUTING

If you wish to contribute to Zend Framework 2.0, please read both the
README-DEV.md and README-GIT.md file.

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
You can find a copy of this license in LICENSE.txt.

### ACKNOWLEDGEMENTS

The Zend Framework team would like to thank all the contributors to the Zend
Framework project, our corporate sponsor, and you, the Zend Framework user.
Please visit us sometime soon at http://framework.zend.com.
