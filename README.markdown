StaticPHP
==========


Directory structure
-------------------

* __application/__ - Application directory - can be named differently, also multiple application directories will work (just set a correct "document root" in your web server)
  * __config/__ - Contains configurations files. Calling load::config('filename'); will load configuration file with filename "config/filename.php".
    * __config.php__ - Default config file.
    * __routing.php__ - Default router routing file.
  * __controllers/__ - Controller files, below are some examples.
    * __home.php__ - Class will be used when for example http://example.com/home/ uri is requested.
    * __test2/__
        * __test2.php__ - http://example.com/test2[/method].
        * __test3.php__ - http://example.com/test2/test3[/method].
  * __files/__ - Directory for various types of files, that needs to be protected from downloading, like icc profiles, wsdl schemas, and other resources
  * __helpers/__ - Helpers directory mostly used for different functions. system.php helper is like a startup script for sending headers, initializing database connections, etc. To load a helper use this method load::helper('filename');.
  * __models/__ - Holds php classes, that are widely used in application. For example, image manipulation class should be placed here as well as database class. To load a model: load::model('filename').
  * __public/__ - Public directory of the site. This is the one where you should point your webserver's document root.
    * __css/__ - Directory for stylesheet files.
    * __.htaccess__ - Access file for apache.
    * __index.php__ - Application main/loader file.
    * __js/__ - Directory for javascript files.
  * __views/__ - Directory for templates
    * __errors__ - Error templates. for example E404.php for 404 Not Found errors.
* __system__ - 
  * __core.php__ - Core php file to init loading of session
  * __load.php__ - Class for loading files and holding configuration values.
  * __router.php__ - Router class. It determines correct controller and method to load and some other methods like router::redirect().    
