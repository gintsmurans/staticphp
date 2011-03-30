StaticPHP
==========


Directory structure
-------------------

* __core/__
  * __config/__ - Contains configurations files. Calling load::config('filename'); will load configuration file with filename "filename.php".
    * __config.php__ - Default config file.
    * __routing.php__ - Default router routing file.
    * __xxx/__ - Directory from which to overload config files. Set it in config.php
  * __controllers/__ - Controller files, below are some examples.
    * __test1.php__ - Class will be used requesting example.com/test1[/method].
    * __test2/__
        * __test2.php__ - Will be used requesting example.com/test2[/method].
        * __test3.php__ - Will be used requesting example.com/test2/test3[/method].
  * __files/__ - Directory for various types of files, that needs to be protected from downloading
  * __helpers/__ - Helpers directory mostly used for different functions. system.php helper is like a startup script for sending headers, initializing database connections, etc. Usage: load::helper('filename');.
  * __models/__ - Holds php classes, that are widely used in application. For example, image manipulation class should be placed here. Usage: load::model('filename').
  * __public/__ - Public directory of the site. This is the one where you should point your webserver's document root.
    * __files/__ - Directory for public files, like, css, js, images and so on.
    * __.htaccess__ - Access file for apache.
    * __index.php__ - Application main/loader file.
  * __system__ - 
    * __load.php__ - Class for loading files and holding configuration values.
    * __router.php__ - Router class. It determines correct controller and method to load and some other methods like router::redirect().    
  * __views/__ - Directory for templates
    * __errors__ - Error templates. for example E404.php for 404 Not Found errors.
