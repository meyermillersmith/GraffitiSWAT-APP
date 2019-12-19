<?php

	final class Config
	{
		/**
		 * Global set for Website if is mirror or not
		 *
		 * @var boolean
		 * @access public
		 */
		public $has_mirror = true;

		/**
		 * Type of session
		 * Possible values:
		 * - db -> session store in database
		 * - standard -> use session standard functions.
		 * <pre><strong>
		 * ###########################################################################################
		 * #                                                                                         #
		 * #   NB!!!                                                                                 #
		 * #   All session variables will be unseen, when you use session_start() and the value of   #
		 * #   this variable is `standard`, because all session handler is pre-defined by autor.     #
		 * #   Also always use class functions to set or unset variables, or any other               #
		 * #   manipulations refering to `SESSION`.                                                  #
		 * #                                                                                         #
		 * #   The autor recommend you to include always `lib.php` in your custom files.             #
		 * #   Popup for example.                                                                    #
		 * #                                                                                         #
		 * ###########################################################################################
		 * </strong></pre>
		 *
		 * @var string
		 * @access public
		 */
		public $SESSION_TYPE = 'standard';

		/******************************************************************
		 * PUBLIC and LOCAL PATHS
		 ******************************************************************/

		/**
		 * URL path to admin
		 *
		 * @var string
		 * @access public
		 */
		public $ADMIN_PATH = 'admin';

		/**
		 * Mode for all exceptions.
		 *
		 * @var string
		 * @access public
		 */
		public $DEBUG_MODE = 'screen';

		/******************************************************************
		 * DATABASE SETTINGS
		 ******************************************************************/

		/**
		 * Type of driver
		 *
		 * @var string
		 * @access public
		 */
		public $DB_TYPE = 'MySqli';

		/**
		 * Host
		 *
		 * @var string
		 * @access public
		 */
		public $DB_HOST = 'localhost';

		/**
		 * Port. Default values are:
		 * - MySql -> 3306
		 * - Postgre -> 5432
		 *
		 * @var integer
		 * @access public
		 */
		public $DB_PORT = 3306;

		/**
		 * Name
		 *
		 * @var string
		 * @access public
		 */
		public $DB_NAME = '';

		/**
		 * Username
		 *
		 * @var string
		 * @access public
		 */
		public $DB_USER = '';

		/**
		 * Password
		 *
		 * @var string
		 * @access public
		 */
		public $DB_PASS = '';

		/**
		 * Table prefix
		 *
		 * @var string
		 * @access public
		 */
		public $DB_PREFIX = '';

		/******************************************************************
		 * SMTP SETTINGS
		 ******************************************************************/

		/**
		 * Host to SMTP server
		 * <pre><strong>
		 * ##########################################
		 * #                                        #
		 * #  NB: IT MUST NOT BE AN IP ADDRESS !!!  #
		 * #                                        #
		 * ##########################################
		 * </strong></pre>
		 *
		 * @var string
		 * @access public
		 */
		public $SMTP_HOST = '192.168.12.51';

		/**
		 * Port
		 *
		 * @var integer
		 * @access public
		 */
		public $SMTP_PORT = 25;

		/**
		 * If has an authorisation
		 *
		 * @var boolean
		 * @access public
		 */
		public $SMTP_AUTH = false;

		/**
		 * Congratulatory message to start telnet connection with the smtp server
		 *
		 * <code>
		 * HELO jungle
		 * EHLO jungle
		 * </code>
		 * @var string
		 * @access public
		 */
		public $SMTP_HELO = '192.168.12.51';

		/**
		 * Username for the e-mail account
		 *
		 * @var string
		 * @access public
		 */
		public $SMTP_USER = null;

		/**
		 * Password for the e-mail account
		 *
		 * @var string
		 * @access public
		 */
		public $SMTP_PASS = null;

		/**
		 * All possible languages for the application
		 * - keys -> shortcuts to the website
		 * - values -> according to IANA registry
		 *
		 * @var array
		 * @access public
		 */
		public $LOCALE_SHORTCUTS = array('en' => 'en-US', 'bg' => 'bg-BG');

		/**
		 * All aliases of any language
		 * - keys -> Aliases languagess
		 * - values -> some key of {@link Config::$LOCALE_SHORTCUTS}
		 *
		 * @var array
		 * @access public
		 */
		public $LOCALE_ALIASES = array();

		/**
		 * Default locale
		 * For structure look above description
		 *
		 * @var string
		 * @access public
		 */
		public $DEFAULT_LOCALE = 'en-US';

		/**
		 * path to the file command
		 *
		 * @var string
		 * @access public
		 */
		public $FILE_ROOT = '/usr/bin/';

		/**
		 * enable/disable cashing of table info
		 *
		 * @var boolean
		 * @access publi
		 */
		public $CACHE_TABLE_INFO = false;

		/**
		 * enable/disable parsed YAML locales cashing
		 *
		 * @var boolean
		 * @access publi
		 */
		public $CACHE_LOCALES = false;

		/**
		 * enable/disable showing locale in the URL
		 * Disable locale showing only if there is only 1
		 * visible language
		 *
		 * @var string
		 * @access public
		 */
		public $SHOW_LOCALE_IN_URL = false;

		public $MAIL_MODE_THUMB = 'public/images/site_images/mail_delivery_mode.png';

		public $FB_APP_ID = '';
		public $FB_SECRET = '';
	}

?>
