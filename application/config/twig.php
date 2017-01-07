<?php
/**
 * Twig settings
 *
 * @author    Anderson Salas <me@andersonsalas.com.ve>
 * @copyright 2016
 * @license   MIT Licence
 * @version   1.0
 */

/*
 * Environment and file system
 *------------------------------------------------------------------------------------- */

// File extension (Default: ".twig")
$config['file_extension'] = '.twig';

// Twig cache (Default: FALSE)
$config['cache'] = FALSE;

/*
 * Debug
 *
 * WARNING: Make sure this functions are disabled on production!
 *------------------------------------------------------------------------------------- */

// Enable/disable __debug() alias function of var_dump() (Default: FALSE)
$config['enable_debug'] = TRUE;

// Defines a __call() generic function  (Default: FALSE)
// WARNING: Make sure this function is disabled in production
$config['enable_call'] = FALSE;

/*
 * Registering functions and constants
 *------------------------------------------------------------------------------------- */

// Register simple (XSS safe) functions:
//
// Examples:
// $config['register_safe'] = array('foo', 'bar');
//
// You can use 'alias' of your callbacks using an
// key pair array:
//
// $config['register_safe']  = array(
//     ['function_alias' => 'actual_callback'],
//     ['another_alias'  => 'another_callback']
// );
$config['register'] = array(
    'route',
);

// Register simple functions with raw output:
// Be careful to make the output 'clean' of XSS
//
// Examples:
// $config['register_safe'] = array('foo', 'bar');
//
// You can use 'alias' here too:
//
// $config['register_safe']  = array(
//     ['function_alias' => 'actual_callback']
// );
$config['register_safe'] = array(

);

// Register configuration files:
// It will include them and register as global each config item
$config['register_config'] = array(
    'app',
);

// Auto register twig methods
//
// This allow to automatically register all __twig_methods() functions as a twig functions
// available globally.
//
// Example:
//
// class MyLibrary
// {
//    public $_twig_methods = array('foo','bar');
//
//    public function foo()
//    {
//        echo 'This will be accessed as {{ foo() }} in your templates!';
//    }
//
//    public function bar()
//    {
//        echo 'This too! (as {{ bar() }}';
//    }
// }

$config['register_twig_class_methods'] = TRUE;

// Enable/disable built-in assets functions (Default: TRUE)
$config['register_assets_functions'] = TRUE;

/*
 * Themes and paths
 *------------------------------------------------------------------------------------- */

// Default theme:
//
// This library is theme-oriented, so you can have multiples 'skins' of your website and
// switch them changing this value here, or dynamically via Twig::setCurrentTheme() in
// your controller
//
// When you call the theme_* built-in assets funcions, they will use this value for
// retrieve the assets path for the current theme
$config['default_theme'] = 'default';


$config['assets_folder']     = 'assets';
$config['components_folder'] = 'components';
$config['theme_folder']      = 'themes';
$config['css_folder']        = 'stylesheets';
$config['js_folder']         = 'javascripts';
$config['img_folder']        = 'images';
$config['fonts_folder']      = 'fonts';



