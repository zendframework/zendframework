In order to generate test files do the following:

  1) Ensure namings in PHPNamespacer-MappedClasses.xml are as you
     want them to be.
     
  2) Go run the library namespacer as noted in the top level
     README-DEV.txt
     
  3) Enter the tests directory, and ensure the proper file structure.
       ** important **
       This means that if there are any files outside of the main
       directory they should be moved into the directory
       Example:

           Zend/UriTest.php should be moved to
           Zend/Uri/UriTest.php

           NEXT:

           Change the class name to reflect the location change:
           Zend_UriTest becomes Zend_Uri_UriTest
           
  
  4) Run the following command (for example if you were working on Uri):
       php test-iterator.php Uri

  5) Ensure the proper files have been written inside of the tmp folder.
     The tool will parse Test files, and copy other assets.  In many cases
     these assets might contain code that will need to be converted by hand.

  6) Move original files to a safe place, then move generated files into the
     main tests directory
     
  7) Convert the rest by hand.



NOTE:

  You can also simply run the test-namespacer.php over a single file, for an
  example, see the contents of the test-iterator.php file, specifically the
  system() command.