INTRODUCTION

The manual is written in Docbook XML and requires a Unix-like
operating system with the standard GNU toolchain and xsltproc 
or a similar XSLT processor to build the source XML into the 
HTML that is shipped with the Zend Framework distributions.

On Windows, you can compile the docbook using Cygwin.  See:
http://www.cygwin.com



INSTALLATION FOR WINDOWS USERS

Installation steps for Cygwin:
  1. Choose "Install from Internet", click [Next]
  2. Choose the directory where you want to install Cygwin. Leave the other
     options on their "RECOMMENDED" selection. Click [Next]
  3. Select a directory where you want downloaded files to be stored. Click
     [Next]
  4. Select your way of connecting to the Internet. Click [Next]
  5. Choose the most suitable mirror in the mirrorlist. Click [Next]
  6. Select the following packages from Devel or Libs to install:
     * automake1.9
     * libxslt
     * make
     All dependent packages will automatically be selected for you.
     Click [Next]
  7. Sit back and relax while Cygwin and the selected packages are being
     downloaded and installed. This may take a while.
  8. Check the option "Create icon on Desktop" and "Add icon to Start Menu" to
     your liking. Click [Finish].


     
BUILDING THE DOCUMENTATION (*NIX AND CYGWIN)     

To build the documentation into HTML:
  1. Go to a shell prompt, or Windows users will run Cygwin (you can double-click 
     the icon on the Desktop or in the Start menu if you've chosen any of these 
     options at install-time)
  2. Navigate to the directory where the documentation files are stored using
     the traditional Unix commands.  For Cygwin users, drives are stored under 
     "/cygdrive".  So if your documentation files are stored under 
     "c:\ZF\documentation", you'll need to run the command "cd c:/ZF/documentation/". 
     You're under a Unix environment, so don't forget all paths are case sensitive!
  3. To compile the doc, go to the directory in which manual.xml is located and run:
     $ autoconf
     $ ./configure
     $ make


   
TROUBLESHOOTING   
    
If you're encountering errors while trying the build instructions above...
  1. Remove all files from the html/ subdir except dbstyle.css
  
  2. Remove all files from the root dir except manual.xml, configure.in,
     Makefile.in and README.  The important one here is entities.ent.
     
  3. You can optionally remove the "/autom4te.cache" directory and the
     "/build/docbook-xsl" directory
     
  4. Try to build again following the instructions given above. If it still
     throws errors, post a message to the fw-docs@lists.zend.com list.
     