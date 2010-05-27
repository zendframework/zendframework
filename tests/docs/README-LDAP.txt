README
======

LDAP is a strange system.  That said, here are some interesting notes to
help get testing underway.


On Mac OS.X
-----------

Mac OS X comes with openldap installed.  With this, you will need to make
a few changes.  First, the slapd.conf needs to be altered:

    database        bdb
    suffix          "dc=example,dc=com"
    rootdn          "cn=Manager,dc=example,dc=com"
    rootpw          {SSHA}cR8cMV8LTzDpSiInDERB89QnEqpzwzS5

That contains the hashed password for 'insecure'.

Make sure the daemon is running, 

Next create the structure that is needed.

Creating top level node, as well as entry for Manager

    File: example.com.ldif

        dn: dc=example,dc=com
        dc: example
        description: LDAP Example
        objectClass: dcObject
        objectClass: organization
        o: example
    
    Add this:

        ldapadd -x -D "cn=Manager,dc=example,dc=com" -W -f ./example.com.ldif
    
    File: manager.example.com.ldif

        dn: cn=Manager,dc=example,dc=com
        cn: Manager
        objectClass: organizationalRole
    
    Add this:

        ldapadd -x -D "cn=Manager,dc=example,dc=com" -W -f ./manager.example.com.ldif
        
After this has been added, we can then use something like Apache Studio
for LDAP to handle creating the rest of the required information.

Create the following:
    ou=test,dc=example,dc=com
        objectClass=organizationalUnit
        ou=test

also:
    uid=user1,dc=example,dc=com
        objectClass=account
        objectClass=simpleSecurityObject
        uid=user1
        userPassword=<<will be provided>>


TestConfiguration values:

    define('TESTS_ZEND_LDAP_HOST', 'localhost');
    //define('TESTS_ZEND_LDAP_PORT', 389);
    define('TESTS_ZEND_LDAP_USE_START_TLS', false);
    //define('TESTS_ZEND_LDAP_USE_SSL', false);
    define('TESTS_ZEND_LDAP_USERNAME', 'cn=Manager,dc=example,dc=com');
    define('TESTS_ZEND_LDAP_PRINCIPAL_NAME', 'Manager@example.com');
    define('TESTS_ZEND_LDAP_PASSWORD', 'insecure');
    define('TESTS_ZEND_LDAP_BIND_REQUIRES_DN', true);
    define('TESTS_ZEND_LDAP_BASE_DN', 'dc=example,dc=com');
    define('TESTS_ZEND_LDAP_ACCOUNT_FILTER_FORMAT', '(&(objectClass=account)(uid=%s))');
    define('TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME', 'example.com');
    define('TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME_SHORT', 'EXAMPLE');
    define('TESTS_ZEND_LDAP_ALT_USERNAME', 'user1');
    define('TESTS_ZEND_LDAP_ALT_DN', 'uid=user1,dc=example,dc=com');
    define('TESTS_ZEND_LDAP_ALT_PASSWORD', 'user1');
    define('TESTS_ZEND_LDAP_WRITEABLE_SUBTREE', 'ou=test,dc=example,dc=com');