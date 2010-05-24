During the namespace conversion, do the following:

 % cd working
 % php ../tools/phptools/bin/php-namespacer.php \
 > -m./ -l=../library/ -p=Zend -o=./tmp/library -d=Zend/<component name>

Check the changes, and merge them into the trunk. Then make the unit
tests pass.

Checklist:
First pass
[X] Zend_Exception
[X] Zend_Version
[X] Zend_Loader
    [X] Including pluginloader, resource loader, etc.
[X] Zend_Registry
[X] Zend_Config
[X] Zend_Debug
[X] Zend_Log
    Would need to ignore Firebug writer for now, and potentially Db writer
[X] Zend_Cache
    [X] Zend_Locale
        [X] Zend_Date
        [X] Zend_Translate
        [X] Zend_TimeSync
        [X] Zend_Measure
        [X] Zend_Currency

Second pass:
[X] Zend_Filter
    skip Input at first
[X] Zend_Validate
    skip Validate_Db at first
    do [X] Zend_Filter_Input at this time
[X] Zend_Uri

Third pass:
[X] Zend_Server
[X] Zend_Http

Fourth pass:
THE FOLLOWING IN ANY ORDER (except where indicated):
[X] Zend_Json
[X] Zend_Crypt 
[X] `-> Zend_Oauth 
[X] `-> Zend_XmlRpc
[X] Zend_Acl
[X] Zend_Reflection
[X] Zend_CodeGenerator
[X] Zend_Console
[X] Zend_Dom
[X] Zend_Gdata
[X] Zend_InfoCard
[X] Zend_Ldap
[ ] Zend_Mime 
[ ] `-> Zend_Mail
[X] Zend_Markup
[X] Zend_Memory 
[X] `-> Zend_Pdf
[X]     `-> Zend_Barcode
[X] Zend_Db (except Firebug profiler)
[ ] `-> Zend_Feed
[ ]     `-> Zend_Queue
[X] Zend_Text
[X] `-> Zend_ProgressBar
[X]     `-> Zend_File
[X] Zend_Search
[X] Zend_Service_Abstract
[X] `-> Zend_Rest_Client
[X]     `-> Zend_Service_ReCaptcha
[X]         `-> Zend_Captcha
[ ] Zend_Soap
[X] Zend_Tag
[ ] Zend_Service_*
[X] Zend_Session 
[X] `-> Zend_Auth 
[X]     `-> Zend_Amf 
[X]         `-> Zend_Serializer

Fifth pass:
All together:
[ ] Zend_Controller, [ ] Zend_View, [ ] Zend_Layout, [ ] Zend_Rest_Route/Controller

Sixth pass:
[ ] Zend_Wildfire (and all Firebug related sub-components)
[ ] Zend_OpenId
[ ] Zend_Form -> [ ] Zend_Dojo
[ ] Zend_Navigation
[ ] Zend_Paginator
[ ] Zend_Test

Seventh (and last) pass:
[ ] Zend_Application -> [ ] Zend_Tool
