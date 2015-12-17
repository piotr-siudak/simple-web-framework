<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/*
 * This file is part of Simple Web Framework.
 *
 * (c) 2008 Piotr Siudak
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Simple Web Framework
 *
 * @author Piotr Siudak <siudak@xz.pl>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link https://github.com/piotr-siudak/simple-web-framework
 */
namespace PiotrSiudak\SimpleWebFramework;

// {{{ class
/**
 * Request Class
 *
 * @author Piotr Siudak <siudak@xz.pl>
 */
class Request
{
    // {{{ properties
    /**
     * Raw URL exactly the way we were called
     *
     * @access public
     * @var string
     */
    public static $url = '';

    /**
     * Name of the action requested
     *
     * @static
     *
     * @access public
     * @var string
     */
    public $action = '';

    /**
     * Parameters of the action requested
     *
     * @access public
     * @var array
     */
    private $params = array();

    /**
     * Request object instance
     *
     * @access private
     * @var Request
     */
    private static $instance;
    // }}}properties
    // {{{ methods
    // {{{ getInstance
    /**
     * Returns instance of Request object
     *
     * @static
     *
     * @access public
     * @return Request Instance of request object
     */
    public static function getInstance()
    {
        if (! isset(self::$instance)) {
            self::$instance = new Request();
        }
        return self::$instance;
    }
    // }}} getInstance
    // {{{ getParameter
    /**
     * Returns Parameter by key
     *
     * @param string $pc_i_key name of the parameter
     *
     * @access public
     * @return string Parameter value
     */
    public function getParameter($pc_i_key)
    {
        if (! empty($this->_params[$pc_i_key])) {
            return $this->_params[$pc_i_key];
        }
    }
    // }}} getParameter
    // {{{ setParameter
    /**
     * Sets Parameter by name
     *
     * @param string $pc_i_key Name of the parameter
     * @param string $value Value of the parameter
     *
     * @access public
     * @return void
     */
    public function setParameter($pc_i_key, $value)
    {
        if (! empty($pc_i_key)) {
            $this->_params[$pc_i_key] = $value;
        }
    }
    
    // }}} setParameter
    // {{{ getRawURL
    /**
     * Returns Raw URL
     *
     * @access public
     * @return string Raw URL exactly the way we were called
     */
    public function getRawURL()
    {
        if (! empty($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on')) {
            $vc_rawURL = 'https://';
        } else {
            $vc_rawURL = 'http://';
        }
        if (! empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
            $vc_rawURL .= $_SERVER['HTTP_X_FORWARDED_HOST'];
        } else {
            $vc_rawURL .= $_SERVER['HTTP_HOST'];
        }
        // $vc_rawURL .= $_SERVER['SERVER_NAME'];
        $vc_rawURL .= $_SERVER['REQUEST_URI'];
        
        return $vc_rawURL;
    }
    // }}} getRawURL
    // {{{ __construct
    /**
     * constructor
     *
     * @access private
     * @return void
     */
    private function __construct()
    {
        if (! empty($_SERVER['REQUEST_URI'])) {
            $urlparams = explode('/', $_SERVER['REQUEST_URI']);
            array_shift($urlparams);
            if (count($urlparams) and reset($urlparams) == 'index.php') {
                array_shift($urlparams);
            }
            if (count($urlparams)) {
                if (strpos(end($urlparams), '.') !== false) {
                    list ($vc_name, $vc_ext) = explode('.', array_pop($urlparams));
                    $this->setParameter('name', $vc_name);
                    $this->setParameter('ext', $vc_ext);
                }
                if (count($urlparams)) {
                    $this->action = array_shift($urlparams);
                    if (count($urlparams)) {
                        $vn_paramNumber = count($urlparams);
                        if (count($urlparams) % 2 !== 0) {
                            $urlparams[] = '';
                        }
                        for ($i = 0; $i < count($urlparams); $i += 2) {
                            $this->setParameter($urlparams[$i], $urlparams[$i + 1]);
                        }
                    }
                }
            }
            if (! strlen($this->action)) {
                $this->action = 'index';
            }
            self::$url = $this->getRawURL();
        }
    }
    // }}} __construct
    // }}} methods
}
// }}} class
/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */
