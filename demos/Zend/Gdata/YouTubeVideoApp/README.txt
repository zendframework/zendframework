== YouTube data API Video App in PHP ==

PHP sample code for the YouTube data API.  Utilizes the Zend Framework
Zend_Gdata component to communicate with the YouTube data API.

Requires the Zend Framework Zend_Gdata component and PHP >= 5.1.4
This sample is run from within a web browser.  These files are required:

session_details.php - a script to view log output and session variables
operations.php - the main logic, which interfaces with the YouTube API
index.php - the HTML to represent the web UI, contains some PHP
video_app.css - the CSS to define the interface style
video_app.js - the JavaScript used to provide the video list AJAX interface

--------------

NOTE: If using in production, some additional precautions with regards
to filtering the input data should be used.  This code is designed only
for demonstration purposes.

--------------

Please be sure to obtain a Developer Key from YouTube prior to using
this application by visiting this site:

http://code.google.com/apis/youtube/dashboard/
        
More information on the YouTube Data API and Tools is available here:
        
http://code.google.com/apis/youtube 

For a video explaining the basics of how this application works, please
visit this link:

http://www.youtube.com/watch?v=iIp7OnHXBlo

To see this application running live, please visit:

http://googlecodesamples.com

== UPDATES ==

3/2009 - Removed functionality to set the Developer Key in a form. Instead,
         it is now hard-coded in the index.php page. This reduces complexity.
