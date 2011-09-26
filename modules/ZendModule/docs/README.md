ZF2 Module Manager Prototype
============================

Description
-----------
This is a prototype of how a module loader and manager for ZF2 might work.

Currently Implemented
---------------------

* **Phar support:** 
  Modules can be packaged, distributed, installed, and ran as phar archives. 
  Supports both executable and non-executable archives; with and without a stub.
  Module class must be made available by either Module.php in the root of the
  phar or in the stub of the phar if it is an executable phar. Below is a list
  of phar archive/compression formats that are supported and their respective
  extensions, as detected by the module loader:
    * **Executable** (can be included directly, which executes stub):
        * phar (.phar)
        * phar + gz  (.phar.gz)
        * phar + bz2 (.phar.bz2)
        * tar (.phar.tar)
        * tar + gz (.phar.tar.gz)
        * tar + bz2 (.phar.tar.bz2)
        * zip (.zip)
    * **Non-executable** (phar cannot be included directly; no stub can be set):
        * tar (.tar)
        * tar + gz (.tar.gz)
        * tar + bz2 (.tar.bz2)
        * zip (.zip)
* **Configuration merging:**
    The module manager goes through each enabled module, loads it's
    `Zend\Config\Config` object via the `getConfig()` method of the respective
    `Module` class; merging them into a single configuration object to be passed
    along to the bootstrap class, which can be defined inthe config of course!
* **Caching merged configuration:**
    To avoid the overhead of loading and merging the configuration of each
    module for every execution, the module manager supports caching the merged
    configuration as an array via `var_export()`. Subsequent requests will bypass
    the entire configuration loading/merging process, nearly eliminating any
    configuration-induced overhead.
* **Module init()**
    The module manager calls on the `init()` method on the `Module` class of
    each enabled module, passing itself as the only parameter. This gives
    modules a chance to register their own autoloaders or perform any other
    initial setup required. **Warning:** The `init()` method is called for every
    enabled module for every single request. The work it performs should be kept
    to an absolute minimum (such as registering a simple classmap autoloader).
* **100% unit test coverage**
    Much effort has been put into extensive unit testing of the module loader
    and manager. In addition to covering every line of code, further effort was
    made to test other use-cases such as nested/sub-modules and various other 
    behaviors.


Stuff that still needs figured out:
-----------------------------------
* **Overall**
    * How can modules cleanly "share" resources? For example, you have 5 module which all use a database connection (or maybe two: master for writes, slave  for reads).
    * How can modules use varying view templating types? For example, one module uses twig, another uses smarty, another mustache, and yet another uses phtml. Does it make sense to have modules for each template library or system, then modules can just decalre the respective one as a dependency?
* **Installation**
    * Importing DB Schema
    * ~~"Merging/compiling" of config (routes, di config, etc) to save cycles at runtime.~~ [SOLVED]
    * Making static assets publically available
    * Resolving dependencies
* **Upgrades**
    * DB migrations
    * Replacement of assets, merged config values (routes, di, etc)
    * How to handle (or not handle) if assets/config values have been changed/modified?
* **Uninstallation**
    * What's removed, what should stay?
    * Removal of "merged/compiled-in" config values. How to tell if a config value has been overridden / changed by another module? What to do then?
    * Should it check for other modules that are depending on it? How much is too much, and when do we just leave it up to the developers?

