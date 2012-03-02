Welcome to the Zend Framework 2.0.0 Release! 

RELEASE INFORMATION
---------------
Zend Framework 2.0.0beta3

THIS RELEASE IS A DEVELOPMENT RELEASE AND NOT INTENDED FOR PRODUCTION USE.
PLEASE USE AT YOUR OWN RISK.

This is the third in a series of planned beta releases. The beta release
cycle will follow the "gmail" style of betas, whereby new features will
be added in each new release, and BC will not be guaranteed; beta
releases will happen approximately every six weeks. 

Once the established milestones have been reached and the featureset has
reached maturity and reasonable stability, we will freeze the API and
prepare for Release Candidate status.

NEW FEATURES IN BETA3
---------------------

- Refactored Config component (Ben Scholzen, Artur Bodera, Enrico Zimuel, Evan Coury)
  - All readers moved under Zend\Config\Reader
    - JSON and YAML readers removed until beta4
    - New API: 
      $xml = new Zend\Config\Reader\Xml(); 
      $config = new Zend\Config\Config($xml->fromFile($filename);

      or:
      $xml     = new Zend\Config\Reader\Xml(); 
      $config = $xml->fromFile($filename, true);

      or, simpler:
      $config = Zend\Config\Factory::fromFile($filename);
    - All constant injection removed from readers
      - New Processor API allows processing the retrieved configuration to do
        optional injection/manipulation of configuration values.
    - Ability to import other configuration files within a configuration file
      added.
  - Factory added, to simplify retrieving configuration from any configuration
    format supported.
- New View layer (Matthew Weier O'Phinney)
  - View layer is now:
    - Models, for aggregating and representing data to render; models may be
      nested to represent complex view hierarchies
    - Renderers, which render templates, using either variables provided or
      Models
    - Resolvers, which resolve template names to resources a renderer may
      consume
    - View, which allows attaching strategies for determining the renderer to
      use, as well as how to inject the response when done.
  - Old Zend_View is now Zend\View\Renderer\PhpRenderer
    - Composes a Resolver, a PluginBroker (for helpers), a Variables container
      (for aggregating variables to pass to the view script), and a FilterChain
      (for output filtering). 
    - render() now accepts View\Models
    - allows rendering stacks of templates under the same variable scope, via
      the addTemplate() mechanism
    - moves escaping to an Escape view helper; no auto-escaping is enabled
  - MVC integration
    - Strategy listeners for:
      - Handling and returning 404 pages
      - Handling and returning error pages due to exceptions
      - RAD support for creation and injection of view models from action
        controller return values
    - Addition of a "render" event, executing after "dispatch" and before
      "finish"
- New Db layer (Ralph Schindler)
  - Complete rewrite from the ground up.
  - New architecture features low-level drivers, which also provide access to
    the PHP resource being consumed; adapters, which provide basic abstraction
    for common CRUD operations; new SQL abstraction layer, with full predicate
    support; abstraction for ResultSet's, with the ability to cast rows to
    specific object types; abstraction for SQL metadata; and a revised Table and
    Row Data Gateway.
- New Zend\Service\AgileZen component (Enrico Zimuel)
  - Support for the full AgileZen (http://www.agilezen.com) API
  - Developed for use with http://framework.zend.com/zf2/board 
- PHP 5.4 support
  - A number of issues when running ZF2 under PHP 5.4 were identified and
    corrected.
- Other components that received attention:
  - Zend\GData (Maks3w)
  - Zend\Navigation (Kyle Spraggs, Frank Brückner)
  - Zend\Session (Mike Willbanks)
  - Zend\Service\Technorati (Maks3w)
  - Zend\Service\StrikeIron (Maks3w)
  - Zend\Service\Twitter (Maks3w)
  - Zend\Http\Header\Accept* (Matthew Weier O'Phinney, Enrico Zimuel)
    - Adds support for q priority, level identifiers, and wildcard media and
      submedia types
  - Zend\Ldap (Maks3w, Stefah Gehrig)
  - Zend\Oauth (bakura10)
  - Zend\Mvc and Zend\Module (Evan Coury, many others)

Around 200 pull requests for a variety of features and bugfixes were handled
since beta2.

SYSTEM REQUIREMENTS
-------------------

Zend Framework 2 requires PHP 5.3.3 or later; we recommend using the
latest PHP version whenever possible.

INSTALLATION
------------

Please see INSTALL.txt.

CONTRIBUTING
------------

If you wish to contribute to Zend Framework 2.0, please read both the
README-DEV.txt and README-GIT.txt file.

QUESTIONS AND FEEDBACK
----------------------

Online documentation can be found at http://framework.zend.com/manual.
Questions that are not addressed in the manual should be directed to the
appropriate mailing list:

http://framework.zend.com/wiki/display/ZFDEV/Mailing+Lists

If you find code in this release behaving in an unexpected manner or
contrary to its documented behavior, please create an issue in the Zend
Framework issue tracker at:

http://framework.zend.com/issues

If you would like to be notified of new releases, you can subscribe to
the fw-announce mailing list by sending a blank message to
fw-announce-subscribe@lists.zend.com.

LICENSE
-------

The files in this archive are released under the Zend Framework license.
You can find a copy of this license in LICENSE.txt.

ACKNOWLEDGEMENTS
----------------

The Zend Framework team would like to thank all the contributors to the Zend
Framework project, our corporate sponsor, and you, the Zend Framework user.
Please visit us sometime soon at http://framework.zend.com.
