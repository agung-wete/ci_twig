<?php
/**
 * Twig library for CodeIgniter
 *
 * @author    Anderson Salas <me@andersonsalas.com.ve>
 * @copyright 2016
 * @license   MIT Licence
 * @version   1.0
 */

/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *     36 class Twig
 *    126   function __toString()
 *    138   function __construct()
 *    240   function registerSettings()
 *    455   function render($template, $vars = NULL, $return = FALSE)
 *    571   function renderPhp($file, $vars)
 *    600   function addGlobal($name, $value)
 *    615   function addDir($dir)
 *    620   function registerFunction($function_name, $callback = NULL, $is_safe = FALSE)
 *    642   function prepDir($dir)
 *    656   function setFileExtension($file_extension)
 *    668   function getFileExtension()
 *    682   function setCurrentTheme($theme)
 *    694   function getCurrentTheme()
 *
 * TOTAL FUNCTIONS: 13
 *
 */

defined('BASEPATH')   OR exit('No direct script access allowed');

class Twig
{
    const VERSION = 1.0;


    /**
     * CodeIgniter instance
     *
     * @var $CI
     *
     * @access protected
     */
    protected $CI;


    /**
     * Twig instance
     *
     * @var $twig
     *
     * @access protected
     */
    protected $twig;

    /**
     *  Twig configuration
     *
     * @var $twig_config
     *
     * @access protected
     */
    protected $twig_config;

    /**
     * Twig loader
     *
     * @var $twig_loader
     *
     * @access protected
     */
    protected $twig_loader;


    /**
     * Twig directories
     *
     * @var $twig_directories
     *
     * @access protected
     */
    protected $twig_directories;


    /**
     * File extension used by Twig
     *
     * @var $twig_file_extension
     *
     * @access protected
     */
    protected $twig_file_extension;


    /**
     * Twig cache
     *
     * @var $twig_cache
     *
     * @access protected
     */
    protected $twig_cache;


    /**
     * Current theme
     *
     * @var $current_theme
     *
     * @access protected
     */
    protected $current_theme;


    /**
     * __toString() magic method
     *
     * @return string
     *
     * @access public
     */
    public function __toString()
    {
        return 'Twig connector library Version'.self::VERSION.' by Anderson Salas';
    }

    /**
     * Class constructor
     *
     * @return void
     *
     * @access public
     */
    public function __construct()
    {
        /*
         * Core initialization, environment, cache, variables, etc.
         * -------------------------------------------------------------------------------
         */

        # Twit class init and CI instance:
        $this->CI =& get_instance();

        # Load twig library config
        $this->CI->config->load('twig',TRUE);
        $this->twig_config = (object) $this->CI->config->config['twig'];
        $twig_config =& $this->twig_config;

        # Defining cache
        if($twig_config->cache !== FALSE)
        {
            if($twig_config->cache === TRUE)
            {
                $twig_cache = APPPATH.'cache'.DS.'twig_cache';
            }
            else
            {
                $twig_cache = APPPATH.$twig_config->cache;
            }
            $this->twig_cache = $twig_cache;
        }
        else
        {
            $this->twig_cache = FALSE;
        }

        # Twig file extension:
        $this->twig_file_extension = $twig_config->file_extension;

        # Current frontend theme:
        $this->current_theme = $twig_config->default_theme;

        /*
         *  Loader & FileSysten
         * -------------------------------------------------------------------------------
         *
         * The extension will search in the following locations for templates:
         *
         * - applications/views (CI default)
         *      /layouts
         *
         * - applications/modules (if HMVC CodeIgniter extension is installed)
         *      /layouts
         *      /components
         *      /views
         *
         * - assets/themes/[current_theme] (in "config/app.php")
         *      /layouts
         *      /components
         *      /views
         */

        $search_folders = [
            'layouts', 'components', 'views'
        ];

        $dirs[] = VIEWPATH;

        if(file_exists(VIEWPATH.'layouts'))
        {
            $dirs[] = VIEWPATH.'layouts';
        }

        if(method_exists($this->CI->router,'fetch_module') && !is_null($this->CI->router->fetch_module()))
        {
            foreach($search_folders as $find)
            {
                if(file_exists( MODULEPATH.$this->CI->router->fetch_module().DS.$find))
                {
                    $dirs[] = MODULEPATH.$this->CI->router->fetch_module().DS.$find;
                }
            }
        }

        foreach($search_folders as $find)
        {
            if(file_exists(ROOTPATH.'assets'.DS.'themes'.DS.$this->current_theme.DS.$find))
            {
                $dirs[] = ROOTPATH.'assets'.DS.'themes'.DS.$this->current_theme.DS.$find;
            }
        }

        $this->twig_directories = $dirs;

        $twig_loader    = new Twig_Loader_Filesystem($this->twig_directories);
        $twig_instance  = new Twig_Environment($twig_loader, ['cache' => $this->twig_cache ]);

        $this->twig        = $twig_instance;
        $this->twig_loader = $twig_loader;
    }

    /**
     * Register functions/globals defined in config file
     *
     * @return void
     *
     * @access private
     */
    private function registerSettings()
    {
        $twig_config =& $this->twig_config;

        // Register Twig's external class defined methods:
        if($twig_config->register_twig_class_methods)
        {
            foreach($this->CI as $lib_name => $lib)
            {
                if(substr(get_class($lib),0,3) == 'CI_' || !is_object($lib))
                    continue;

                if(isset($lib->_twig_methods) && is_array($lib->_twig_methods))
                {
                    foreach($lib->_twig_methods as $method)
                    {
                        $function = new Twig_SimpleFunction($method, function() use($lib_name, $method){
                            return $this->CI->$lib_name->$method();
                        });
                        $this->twig->addFunction($function);
                    }
                }
            }
        }

        // Register configuration files as globals
        foreach($twig_config->register_config as $config)
        {
            $this->CI->config->load($config,TRUE);
            foreach($this->CI->config->config[$config] as $name => $value)
            {
                $this->addGlobal($name,$value);
            }
        }

        // Register normal (escaped) functions
        foreach($twig_config->register as $function)
        {
            if(is_string($function))
            {
                $name = $callback = $function;
            }
            elseif( is_array($function) && count($function) == 1)
            {
                $name     = key($function);
                $callback = $function[$name];
            }
            else
            {
                continue;
            }

            $this->registerFunction($name,$callback);
        }

        // Register raw functions
        foreach($twig_config->register_safe as $function)
        {
            if(is_string($function))
            {
                $name = $callback = $function;
            }
            elseif( is_array($function) && count($function) == 1)
            {
                $name     = key($function);
                $callback = $function[$name];
            }
            else
            {
                continue;
            }

            $this->registerFunction($name,$callback,TRUE);
        }

        // By default, this library comes with a set of useful assets functions to handle
        // all website css, js, images, etc.:
        if($twig_config->register_assets_functions)
        {
            $config =& $this->twig_config;
            $this->CI->load->helper('url');
            $theme = $this->current_theme;

            $this->registerFunction('url','base_url');

            $asset = function($file = '') use($config){
                return base_url().$config->assets_folder.'/'.$file;
            };

            $this->registerFunction('asset', $asset);

            $this->registerFunction('css', function($file) use($asset, $config){
                return $asset().$config->css_folder.'/'.$file;
            });

            $this->registerFunction('js', function($file) use($asset, $config){
                return $asset().$config->js_folder.'/'.$file;
            });

            $this->registerFunction('img', function($file) use($asset, $config){
                return $asset().$config->img_folder.'/'.$file;
            });

            $this->registerFunction('font', function($file) use($asset, $config){
                return $asset().$config->fonts_folder.'/'.$file;
            });

            $theme_asset = function($file = '') use($asset, $config, $theme)
            {
                return $asset().$config->theme_folder.'/'.$theme.'/'.$file;
            };

            $this->registerFunction('theme_asset', $theme_asset);

            $this->registerFunction('theme_css', function($file) use($theme_asset,$config)
            {
                return $theme_asset().$config->css_folder.'/'.$file;
            });

            $this->registerFunction('theme_js', function($file) use($theme_asset,$config)
            {
                return $theme_asset().$config->js_folder.'/'.$file;
            });

            $this->registerFunction('theme_img', function($file) use($theme_asset,$config)
            {
                return $theme_asset().$config->img_folder.'/'.$file;
            });

            $this->registerFunction('theme_font', function($file) use($theme_asset,$config)
            {
                return $theme_asset().$config->fonts_folder.'/'.$file;
            });

            $this->registerFunction('component', function($file) use($asset,$config){
                return $asset().$config->components_folder.'/'.$file;
            });
        }

        if(function_exists('route'))
        {
            $this->registerFunction('route');
        }

        if($twig_config->enable_debug)
        {
            $this->registerFunction('__debug', function($expr){
                var_dump($expr);
            });
        }

        if($twig_config->enable_call)
        {
            $this->registerFunction('__call', function($function_name)
            {
                $args = func_get_args();
                $func_args = array();

                if(!empty($args))
                {
                    unset($args[0]); // I don't need the function name
                    if(isset($args[1]) && is_array($args[1]) && !empty($args[1]))
                    {
                        $func_args = $args[1];
                    }
                    else
                    {
                        foreach($args as $arg)
                        {
                            $func_args[] = $arg;
                        }
                        if(count($func_args) == 1)
                        {
                            $arg = $func_args[0];
                            $func_args = $arg;
                        }
                    }
                }

                if(!is_callable([$function_name, $func_args]) && !is_callable($function_name))
                {
                    show_error('The function "'.$function_name.'" is not a valid callable '.(is_array($func_args) ? '`array`' : '`function({argument})`'));
                }

                if(is_array($func_args) && !empty($func_args))
                {
                    return call_user_func_array($function_name, $func_args);
                }
                else
                {
                    if(is_string($func_args))
                    {
                        return call_user_func($function_name,$func_args);
                    }
                    else
                    {
                        return call_user_func($function_name);
                    }
                }
            }, TRUE);
        };
    }


    /**
     * Render a template
     *
     * @param  String $template path to template
     * @param  $vars   (Optional)
     * @param  $return (Optional)
     *
     * @return mixed
     *
     * @access public
     */
    public function render($template, $vars = NULL, $return = FALSE)
    {
        $this->registerSettings();

        $founded    = FALSE;
        $is_php     = FALSE;
        $extensions = [$this->twig_file_extension.'.php', $this->twig_file_extension];
        $template   = str_ireplace('.',DS,$template);

        foreach($this->twig->getLoader()->getPaths() as $path)
        {
            foreach($extensions as $extension)
            {
                $search = $path.DS.$template.$extension;
                if(file_exists($search))
                {
                    $founded = $search;

                    # Check if requested template is a php-hybrid template ( a ".twig.php" file)
                    $is_php = $path.DS.$template.$this->twig_file_extension.'.php' == $search;
                    break;
                }
            }
        }

        # If not founded: throw an 404 error:
        if($founded === FALSE)
        {
            if(ENVIRONMENT == "production")
            {
                show_404();
            }
            else
            {
                $error_msg = 'Template <strong>"'.$template.'"</strong> not found in:';
                foreach($this->twig->getLoader()->getPaths() as $path)
                {
                    $error_msg .= '<br>  "'.$path.'"';
                }
                show_error($error_msg,500,'Twig error');
            }
        }

        if(is_null($vars))
        {
            $vars = array();
        }

        # Make avaibable the $_GET and $_POST variables in all templates
        $request['request'] = array();

        if($_POST)
        {
            $request['request']['post'] = $_POST;
        }

        if($_GET)
        {
            $request['request']['get'] = $_GET;
        }

        $vars = array_merge($vars,$request);

        try
        {
            if(!$is_php)
            {
                $template .= $this->twig_file_extension;
                $twig = $this->twig->loadTemplate($template);

                if(!$return)
                {
                    echo $twig->render($vars);
                }
                else
                {
                    return $twig->render($vars);
                }
            }
            else
            {
                # If is an hybrid template, first render as an PHP file an take his output:
                $php_output = $this->renderPhp($founded, $vars);
                # Then, make a blank template of that output
                $twig = $this->twig->createTemplate($php_output);

                # And finally send it!:
                if(!$return)
                {
                    echo $twig->render($vars);
                }
                else
                {
                    return $twig->render($vars);
                }
            }
        }
        catch(Exception $e)
        {
            show_error($e->getMessage(),500,'Twig exception');
        }
    }


    /**
     * Pre-render php file
     *
     * This allow hybrid twig-php templates (experimental)
     *
     * @param  String $file
     * @param  array $vars
     *
     * @return string
     *
     * @access private
     */
    private function renderPhp($file, $vars)
    {
        foreach($vars as $var_name => $value)
        {
            if($var_name == 'request')
                continue; # Since the $_GET and $_POST vars are globals by default in PHP, we'll skip it

            $$var_name = $value;
        }

        ob_start();
            require($file);
            $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }


    /**
     * Add global to Twig instance
     *
     * @param  string $name
     * @param  string $value
     *
     * @return void
     *
     * @access public
     */
    public function addGlobal($name, $value)
    {
        $this->twig->addGlobal($name, $value);
    }


    /**
     * Add directory to Twig loader filesystem
     *
     * @param  string $dir
     *
     * @return void
     *
     * @access public
     */
    public function addDir($dir)
    {
        $this->twig_loader->addPath($dir);
    }

    public function registerFunction($function_name, $callback = NULL, $is_safe = FALSE)
    {
        if(is_null($callback))
            $callback = $function_name;

        $function = new Twig_SimpleFunction($function_name, function() use($callback){
            $args = func_get_args();
            return call_user_func_array($callback, $args);
        }, ($is_safe ? ['is_safe' => ['html']] : array() ));

        $this->twig->addFunction($function);
    }

    /**
     * Prepend directory to Twig loader filesystem
     *
     * @param  string $dir
     *
     * @return void
     *
     * @access public
     */
    public function prepDir($dir)
    {
        $this->twig_loader->prependPath($dir);
    }

    /**
     * Get the Twig file extension
     *
     * @param  string $file_extension
     *
     * @return void
     *
     * @access public
     */
    public function setFileExtension($file_extension)
    {
        $this->twig_file_extension = $file_Extension;
    }

    /**
     * Set the current Twig file extension
     *
     * @return string
     *
     * @access public
     */
    public function getFileExtension()
    {
        return $this->twig_file_extension;
    }

    /**
     * Set current theme
     *
     * @param  string   $theme
     *
     * @return void
     *
     * @access public
     */
    public function setCurrentTheme($theme)
    {
        $this->current_theme = $theme;
    }

    /**
     * Get current theme
     *
     * @return string
     *
     * @access public
     */
    public function getCurrentTheme()
    {
        return $this->current_theme;
    }

}