<?php
// Database
define('DATABASE_HOST', '127.0.0.1');
define('DATABASE_NAME', 'allen_frame_trial');
define('DATABASE_USER', 'root');
define('DATABASE_PASSWORD', '');
define('DATABASE_CHARSET', 'utf8');
define('DATABASE_TABLE_PREFIX', 'tbl_');

// Core Paths
define('PATH_BASE', '/wamp/www/allen_frame_trial/');
define('PATH_SYSTEM', PATH_BASE . 'system/');
define('PATH_INCLUDE', PATH_SYSTEM . 'include/');
define('PATH_CLASS', PATH_SYSTEM . 'class/');

define('PATH_ASSET', PATH_BASE . 'asset/');
define('PATH_IMAGE', PATH_ASSET . 'image/');

// Load Pre-Include Functions (Functions that Classes May Use)
// Preference (Global constant variables, can be overwritten)
include_once(PATH_INCLUDE.'preference.inc.php');
$global_preference = preference::get_instance();
$global_preference->default_entity_row_max = 100;
$global_preference->default_view_page_size = 100;
$global_preference->business_summary_view_page_size = 12;

// Database Connection, by default, all connect using a single global variable to avoid multiple db connections
include_once(PATH_INCLUDE.'db.inc.php');
$db = new db;

// Format adjust, such as friendly url, phone number, abn...
include_once(PATH_INCLUDE.'format.inc.php');

// Load Classes
// Each Entity Class represents one and only one table, handle table operations
// View Classes are read only classes, display to front end
// Index Classes are indexed tables for search only
set_include_path(PATH_CLASS.'entity/;'.PATH_CLASS.'view/;'.PATH_CLASS.'index/');
spl_autoload_extensions('.class.php');
spl_autoload_register();

// Load System Functions (Functions that may call Classes)
include_once(PATH_INCLUDE.'content.inc.php');
?>