# Twig library for CodeIgniter 3

*Version 1.0-alpha*  
  
A simple yet powerful Twig implementation for CodeIgniter.

## Key features

* Easy to install, available as library
* Support for themes
* Experimental pre-php render
* Highly customizable

## Requirements 

* PHP >= 7.0.0
* CodeIgniter 3

## Installation

1. First get Twig with **Composer** (inside your *application* folder): `composer require "twig/twig:~2.0"`

2. Open your `application/config.php` file and make sure that `$config['composer_autoload'] = TRUE`

3. Download and unzip this repo inside your *application* folder.


## Usage

Load the library in your controller(s):

```php
$this->load->library('twig');
```

Use the `render()` method for render a template:

```php
$this->twig->render('my_super_template');
```

##### Syntax:

```php
render(string $template_name, array $vars, bool $return);
```

##### Where:

* **$template_name** *(string)* is your template. Use dots (.) or slashes (/) to indicate subfolders.
* **$vars** *(array)* are your variables, in an array format. Example: `['foo' => 'var']` will be available as `{{ foo }}` in your template.
* **$return** *(bool)* return the output as a string

You can edit the *application/config/twig.php* file for customize the library behavior.

For more information, see the [wiki](https://github.com/andersonsalas/ci_twig/wiki/).
