# Twig library for CodeIgniter 3.X

A simple yet powerful Twig implementation for CodeIgniter.

## Installation

1. Install Twig with **Composer** into your *application* folder: ```composer require "twig/twig:~2.0"``` (you should see a new *vendor* folder created after that)

2. Open your *application/config.php* file and make sure that ```$config['composer_autoload']``` is ```TRUE```

2. Download and unzip this repo and move the files into their respective folders (```application/libraries``` and ```application/config```)

3. Declare (if not) this constants at the end of your *application/constants.php* file:

```php
defined('DS')         OR define('DS', DIRECTORY_SEPARATOR);
defined('ROOTPATH')   OR define('ROOTPATH', dirname(BASEPATH).DS);
defined('CONFIGPATH') OR define('CONFIGPATH', ROOTPATH.'application'.DS.'config'.DS);
defined('MODULEPATH') OR define('MODULEPATH', ROOTPATH.'application'.DS.'modules'.DS);
defined('ASSETSPATH') OR define('ASSETSPATH', ROOTPATH.'assets'.DS);
```

## Usage

Load the library in your controller(s) using:

```php
$this->load->library('twig');
```

For render a template, just use the render() method:

```php
$this->twig->render('my_super_template');
```

You can edit the *application/config/twig.php* file for customize the library behavior.

For more information, please consult the repository Wiki.