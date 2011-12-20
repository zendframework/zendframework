Welcome to the Zend Framework 2.0.0 Release! 

RELEASE INFORMATION
---------------
Zend Framework 2.0.0beta2

THIS RELEASE IS A DEVELOPMENT RELEASE AND NOT INTENDED FOR PRODUCTION USE.
PLEASE USE AT YOUR OWN RISK.

This is the second in a series of planned beta releases. The beta release
cycle will follow the "gmail" style of betas, whereby new features will
be added in each new release, and BC will not be guaranteed; beta
releases will happen approximately every six weeks. 

Once the established milestones have been reached and the featureset has
reached maturity and reasonable stability, we will freeze the API and
prepare for Release Candidate status.

NEW FEATURES IN BETA2
---------------------

- Refactored Mail component
  - Does not change existing Storage API, except:
    - Zend\Mail\MailMessage was moved to Zend\Mail\Storage\MailMessage
    - Zend\Mail\MailPart was moved to Zend\Mail\Storage\MailPart
    - Zend\Mail\Message was moved to Zend\Mail\Storage\Message
    - Zend\Mail\Part was moved to Zend\Mail\Storage\Part
  - Zend\Mail\Mail was renamed to Zend\Mail\Message
    - Encapsulates a mail message and all headers
    - MIME messages are created by attaching a Zend\Mime\Message object as the
      mail message body
  - Added Zend\Mail\Address and Zend\Mail\AddressList
    - Used to represent single addresses and address collections, particularly
      within mail headers
  - Added Zend\Mail\Header\* and Zend\Mail\Headers
    - Representations of mail headers
  - Zend\Mail\Transport interface defines simply "send(Message $message)"
    - Smtp, File, and Sendmail transports rewritten to consume Message objects,
      and to introduce Options classes.
- Refactored Zend\Cache
  - Completely rewritten component.
  - New API features storage adapters and adapter plugins for implementing cache
    storage and features such as serialization, clearing, and optimizing.
    - Current adapters include filesystem, APC, memcached, and memory.
    - All adapters can describe capabilities.
    - Plugins are implemented as event listeners.
  - New "Pattern" API created to simplify things like method, class, object, and
    output caching.
- MVC updates
  - Zend\Module\Manager was stripped of most functionality; it now simply
    iterates requested modules and triggers events.
  - Former manager functionality such as class loading and instantiation,
    "init()" triggering, configuration gathering, and autoloader seeding were
    moved to event listeners.
  - Post-module loading configuration globbing support was added.
    - Simplifies story of overriding module configuration.
  - Recommended filesystem no longer uses plurals for directory names.
  - Recommendations now include a chdir(__DIR__ . '/../') from the
    public/index.php file, and specifying configuration paths to be relative to
    application directory.

In addition, over 100 bug and feature requests were handled since beta1.

SYSTEM REQUIREMENTS
-------------------

Zend Framework 2 requires PHP 5.3 or later; we recommend using the
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
