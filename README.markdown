Changed structure of StaticPHP
==============================

* __core/__
  * __cache/__ - Directory for all kind of file caches.
  * __config/__ - Contains configurations files. Calling load::config('filename'); will load configuration file with filename "filename.php".
  * __helpers/__ - Helpers directory mostly used for different functions. system.php helper is like a startup script for sending headers, initializing database connections, etc. Usage: load::helper('filename');.
  * __libraries/__ - Holds php classes, that are widely used in application. For example, image manipulation class should be placed here. Usage: load::library('filename').
  * __modules/__ - Modules of the site. Below are some examples.
    * __errors__ - Error templates. for example E404.tpl.php for 404 Not Found errors.
    * __test1.php__ - Class will be used requesting example.com/test1/method.
    * __test2/__
        * __images__ - Images that belongs to test2 controller.
        * __test2.php__ - Will be used requesting example.com/test2/method.
        * __test3.php__ - Will be used requesting example.com/test2/test3/method.
        * __style.css__ - Css file for test2 controller.
        * __scripts.js__ - File for browser scripting.
  * __.htaccess__ - Access file for apache.
  * __index.php__ - Application main/loader file.
  * __load.php__ - Class for loading files and holding configuration values.
  * __router.php__ - Router class. It determines correct controller and method to load and some other methods like router::redirect().
