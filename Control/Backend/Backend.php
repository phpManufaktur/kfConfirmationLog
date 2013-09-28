<?php

/**
 * ConfirmationLog
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://addons.phpmanufaktur.de/event
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\ConfirmationLog\Control\Backend;


class Backend {

    protected $app = null;
    protected static $usage = null;
    protected static $usage_param = null;
    protected static $message = '';

    /**
     * Constructor
     */
    public function __construct($app=null) {
        if (!is_null($app)) {
            $this->initialize($app);
        }
    }

    /**
     * Initialize the class with the needed parameters
     *
     * @param Application $app
     */
    protected function initialize($app)
    {
        $this->app = $app;
        if (defined('SYNCDATA_PATH')) {
            // executed from SyncData installation
            self::$usage = CMS_TYPE;
            $app['translator']->setLocale(strtolower(LANGUAGE));
        }
        else {
            // executed from kitFramework installation
            self::$usage = $this->app['request']->get('usage', 'framework');
            // set the locale from the CMS locale
            if (self::$usage != 'framework') {
                $app['translator']->setLocale($this->app['session']->get('CMS_LOCALE', 'en'));
            }
        }
        self::$usage_param = (self::$usage != 'framework') ? '?usage='.self::$usage : '';

    }

    /**
     * Get the toolbar for all backend dialogs
     *
     * @param string $active dialog
     * @return multitype:multitype:string boolean
     */
    public function getToolbar($active) {
        $toolbar_array = array(
            'event_list' => array(
                'text' => 'Event list',
                'hint' => 'List of all active events',
                'link' => FRAMEWORK_URL.'/admin/event/list'.self::$usage_param,
                'active' => ($active == 'event_list')
            ),
        );
        return $toolbar_array;
    }

    /**
     * @return the $message
     */
    public function getMessage ()
    {
        return self::$message;
    }

      /**
     * @param string $message
     */
    public function setMessage($message, $params=array())
    {
        self::$message .= $this->app['twig']->render($this->app['utils']->getTemplateFile('@phpManufaktur/ConfirmationLog/Template', 'backend/message.twig'),
            array('message' => $this->app['translator']->trans($message, $params)));
    }

    public function clearMessage()
    {
        self::$message = '';
    }

    /**
     * Check if a message is active
     *
     * @return boolean
     */
    public function isMessage()
    {
        return !empty(self::$message);
    }
 }
