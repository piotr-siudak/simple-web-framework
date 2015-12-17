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
 * Aplication Class
 *
 * @author Piotr Siudak <siudak@xz.pl>
 */
class Application
{
    // {{{ properties
    /**
     * Location of page definitions
     *
     * @access public
     * @var string
     */
    public $pagesLocation = ''; 
    // }}} properties
    // {{{ methods
    // {{{ __construct
    /**
     * constructor
     * 
     * @param string $pagesLocation
     *            Location on disk where page definitions are
     *            
     * @access public
     * @return void
     */
    public function __construct($pagesLocation = NULL)
    {
        if (is_null($pagesLocation)) {
            throw new FatalError('Location for page definitions unspecified.');
        }
        if (! file_exists($pagesLocation)) {
            throw new FatalError('Specified location for page definitions does not exist.');
        }
        $this->pagesLocation = $pagesLocation;
    }
    // }}} __construct
    
    // {{{ main
    /**
     * main
     *
     * @access public
     * @return void
     */
    public function main()
    {
        $request = Request::getInstance();
        session_start();
        try {
            try {
                if (! file_exists($this->getPageFileLocation('action'))) {
                    throw new ApplicationError('Not Found', 404);
                }
                try {
                    if (file_exists($this->getPageFileLocation('validator'))) {
                        include $this->getPageFileLocation('validator');
                    }
                    include $this->getPageFileLocation('action');
                    if (file_exists($this->getPageFileLocation('view'))) {
                        include $this->getPageFileLocation('view');
                    }
                    exit();
                } catch (UnauthorizedException $e) {
                    $request->referrer = $request->action;
                    $request->action = 'login';
                    if (file_exists($this->getPageFileLocation('validator'))) {
                        include $this->getPageFileLocation('validator');
                    }
                    include $this->getPageFileLocation('action');
                    if (file_exists($this->getPageFileLocation('view'))) {
                        include $this->getPageFileLocation('view');
                    }
                    exit();
                }
            } catch (FormException $e) {
                if (! file_exists($this->getPageFileLocation('form'))) {
                    throw new FatalError('no form definition');
                }
                if (file_exists($this->getPageFileLocation('form-prepare'))) {
                    include $this->getPageFileLocation('form-prepare');
                }
                include $this->getPageFileLocation('form');
                exit();
            }
        } catch (ApplicationError $e) {
            $request = Request::getInstance();
            $request->referrer = $request->action;
            $request->action = 'error';
            if (! file_exists($this->getPageFileLocation('action')) || ! file_exists($this->getPageFileLocation('view'))) {
                throw new FatalError('Error occured and another one while trying to hanle it.');
            }
            include $this->getPageFileLocation('action');
            include $this->getPageFileLocation('view');
            exit();
        }
    }
    // }}} main
    // {{{ getPageFileLocation
    /**
     * returns location of file of a given flavor of action ingredients
     *
     * @param string $type
     *            type of file, typicaly: validator, form-prepare, form, action
     *            or view
     *            
     * @access private
     * @return string file location
     */
    private function getPageFileLocation($type)
    {
        $request = Request::getInstance();
        return join(DIRECTORY_SEPARATOR, array(
            $this->pages,
            $request->action,
            $type . '.php'
        ));
    }
    // }}} getPageFileLocation
    
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
