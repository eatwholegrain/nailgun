<?php
/**
 * Server path
 */
define("PATH", dirname(__FILE__));
/**
 * Web path
 */
define("ROOT", "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/");

/**
 * Database settings
 */
define("DB_TYPE", "mysql");
define("DB_HOST", "127.0.0.1");
define("DB_NAME", "nailgunapp.com");
define("DB_USER", "root");
define("DB_PASS", "");


/**
 * Application title
 */
define("APPLICATION_TITLE", "Nail Gun");

/**
 * Encryption key
 */
define("ENC_KEY", "N!@#$%^&*gun");

/**
 * Salt for password encryption
 */
define("HASH_PASSWORD_KEY", "nailgun");

/**
 * Reply to / From email address
 */
define("REPLY_EMAIL", "admin@nailgunapp.com");

/**
 * Upload folder
 */
define("UPLOAD", "uploads/");

/**
 * Display short date
 */
define("SHORT_DATE_FORMAT", "d. F Y");

/**
 * Display long date
 */
define("LONG_DATE_FORMAT", "l, d. F Y");

/**
 * Display time
 */
define("TIME_FORMAT", "g:i A");

/**
 * Display shortcuts bar
 */
define("SHORTCUTS", true);

/**
 * Display project info bar
 */
define("PROJECT_INFO", true);

/**
 * Display task update counter
 */
define("UPDATE_COUNTER", true);

/**
 * Autoscroll updates page
 */
define("AUTOSCROLL", true);

/**
 * Display zopim chat client
 */
define("CHAT", false);

/**
 * Zopim chat ID
 */
define("ZOPIM_ID", "jlhIVAzIs9TZWY8JEQMDtJ1w2mA8Hpq3");

/**
 * Display disqus comments
 */
define("DISQUS", true);

/**
 * Disqus ID
 */
define("DISQUS_ID", "milan-projects");

/**
 * Colors for tasks
 */
$colors = array(
    "#1A4E66", 
    "#23566D",
    "#2B5E75",
    "#34667D",
    "#3C6E85",
    "#45768C",
    "#4D7E94",
    "#56869C",
    "#5E8EA4",
    "#6796AB",
    "#6F9EB3",
    "#78A6BB",
    "#80AEC3",
    "#89B6CA",
    "#91BED2",
    "#91BED2",
    "#91BED2",
    "#91BED2",
    "#91BED2",
    "#91BED2",
    "#91BED2",
    "#91BED2",
    "#91BED2",
    "#91BED2",
    "#91BED2",
    "#91BED2",
    "#91BED2",
    "#91BED2",
    "#91BED2",
    "#91BED2",
    "#91BED2",
    "#91BED2",
    "#91BED2",
    "#91BED2",
    "#91BED2",
    "#91BED2",
    "#91BED2",
    "#91BED2",
    "#91BED2",
    "#91BED2",
    "#91BED2",
    "#91BED2",
    "#91BED2",
    "#91BED2",
    "#91BED2",
    "#91BED2",
    "#91BED2",
    "#91BED2"
);

/**
 * Error codes
 */
$errors = array( 
    "NOT_FOUND",
    "INVALID_PID_SPECIFIED",
    "INVALID_TID_SPECIFIED",
    "INVALID_UID_SPECIFIED",
    "INVALID_PID_OR_TID_SPECIFIED",
    "INSUFFICIENT_PERMISSIONS",
    "INVALID_PROJECT_ID_SPECIFIED",
    "INVALID_TASK_ID_SPECIFIED",
    "INVALID_USER_ID_SPECIFIED",
    "UNKNOWN_ERROR",
    "PAGE_NOT_FOUND",
    "DATABASE ERROR",
    "USER NOT LOGED IN"
);

/**
 * Disable error display
 */
//error_reporting("E_NONE");

?>