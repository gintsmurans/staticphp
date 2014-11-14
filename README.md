# StaticPHP

StaticPHP aim is to provide a web application framework that should help building web sites and web applications a lot faster than using any other php framework by providing already well defined application structure and tools.

### Requirements

* PHP 5.4+
* Twig 1.5+


### Installation

There are two ways to install staticphp framework:

1. Easy one - using composer.
2. A little bit complicated one - manually.

**1. Using composer**

Run `composer create-project 4apps/staticphp ./` for stable version and `composer create-project 4apps/staticphp ./ master-dev` for latest development version from github. Composer will install all the dependecies for you.

To install composer take a look [here](https://getcomposer.org/doc/00-intro.md).


**2. Manually**

Download latest release from [Releases](https://github.com/gintsmurans/staticphp/releases) or [development version](https://github.com/gintsmurans/staticphp/archive/master.zip) from github. Extract archive contents to some directory (lets call it "somedir").

Download [Twig](https://github.com/twigphp/Twig/archive/v1.16.2.tar.gz). Extract archive, rename the directory to twig and put it in _./somedir/vendor/twig_ so that _Autoloader.php_ file is under _./somedir/vendor/twig/twig/lib/Twig/_. For installing Twig C php extension, please refer to this [guide](http://twig.sensiolabs.org/doc/installation.html#installing-the-c-extension).


### Gettings started

Most quickest way to run your project is to use php's in-built server. To do that, cd into the _./somedir/application/public_ and run `php -S 0.0.0.0:8081`. Now open your **servers_ip:8081** (or **127.0.0.1:8081**) and staticphp first page should show up. By default, running as cli server, turns debugging on, but you can configure that in _./somedir/applications/config/config.php_ by settings $config['environment'] or $config['debug'].

_* Take a look at home controller in ./somedir/applications/controllers/home.php for basic usage. Base views in ./somedir/applications/views/ also shows the most basic usage of views._

_* TODO: Write usage guide_


### Components

Installing via composer automatically downloads jquery and bootstrap components.   By default those are installed in _./somedir/application/public/assets/vendor/_. Base views shipped with staticphp are built using these components, so you can quickly get started with your project.


### Api




### Example app

[This](http://staticphp-example.gm.lv/) is a simple todo example application based on sessions and memcached. To view the source, checkout the "example" branch.


### Basic Nginx configuration

    server {
        listen       80;
        listen       443 ssl;
        server_name  staticphp.gm.lv;

        root  /www/sites/gm.lv/staticphp/application/public;
        index index.php index.html index.htm;

        # Error responses
        error_page 403 /errors/E403.html;
        error_page 404 405 =404 /errors/E404.html;
        error_page 500 501 502 503 504 =500 /errors/E500.html;

        # Handle error responses
        location ~ /errors/(E[0-9]*.html) {
            alias /www/sites/gm.lv/staticphp/application/views/errors/$1;
        }

        # Base location
        location / {
            if (!-e $request_filename)
            {
                rewrite  ^(.*)$  /index.php?/$1  last;
            }
        }

        # Allow font origin (for webfonts and similar)
        location ~* \.(eot|ttf|woff|svg)$ {
            add_header Access-Control-Allow-Origin *;
        }

        # Set assets expiration headers to max
        location ~ ^/assets/ {
            expires max;
        }

        # Handle php files
        location ~ \.php$ {
            if (!-f $request_filename) {
                return 404;
            }

            fastcgi_pass   127.0.0.1:9000;
            fastcgi_index  index.php;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            include /etc/nginx/fastcgi_params;
        }

        # Show 404 for hidden files
        location ~ /\. {
            return 404;
        }
    }


## TODO

####Before releasing a stable version

* √ Change all include to require, so that we don't expose staticphp to any security issues by doing something that can't be done.
* Update staticphp start page.
* Add filesystem helpers to core \load class.
* Go through core router class and make sure there are no redundant methods.
* √ Rename all class methods in camelCase format to comply with php-fip standards. Also possibly filenames.
* Check whether url prefixes are working.
* Choose documentation parser and write api documentation, e.g. write descriptions for all staticphp class methods and files.
* Check before_controller hook.
* Json reponse has been used very often, maybe we should make some kind of output filtering method that outputs content base on output type?
* Update one of the projects currently using staticphp to get the idea of whether we are not missing any required variable to be available globaly in view files.
* Rewrite all sessions classes into one by adding an option to choose from session backend to use, possibly allowing to use multiple backends (e.g. memcached -> sql).
* Check whether form validation helper still works and how it applies to Twig.
* Pages helper should register it self with Twig once loadded and if Twig is available.
* Make cache class for memcached and redis.
