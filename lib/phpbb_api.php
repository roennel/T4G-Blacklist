<?php

global $phpbb_root_path, $phpEx;
global $db, $user, $auth, $cache, $config;

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '/var/www/forum.tools4games.com/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

// common.php overwrites error handler and includes additional stuff we don't need like startup.php, hooks, extensions etc
// so require all modules manually here instead of letting common.php do it

// require($phpbb_root_path . 'common.' . $phpEx);

require($phpbb_root_path . 'includes/startup.' . $phpEx);
require($phpbb_root_path . 'config.' . $phpEx);

// Include files
require($phpbb_root_path . 'includes/acm/acm_' . $acm_type . '.' . $phpEx);
require($phpbb_root_path . 'includes/cache.' . $phpEx);
require($phpbb_root_path . 'includes/template.' . $phpEx);
require($phpbb_root_path . 'includes/session.' . $phpEx);
require($phpbb_root_path . 'includes/auth.' . $phpEx);

require($phpbb_root_path . 'includes/functions.' . $phpEx);
require($phpbb_root_path . 'includes/functions_content.' . $phpEx);

require($phpbb_root_path . 'includes/constants.' . $phpEx);
require($phpbb_root_path . 'includes/db/' . $dbms . '.' . $phpEx);
require($phpbb_root_path . 'includes/utf/utf_tools.' . $phpEx);

require($phpbb_root_path . 'includes/functions_user.' . $phpEx);
require($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);

// Instantiate some basic classes
$user		= new user();
$auth		= new auth();
$template	= new template();
$cache		= new cache();
$db			= new $sql_db();

// Connect to DB
$db->sql_connect($dbhost, $dbuser, $dbpasswd, $dbname, $dbport, false, defined('PHPBB_DB_NEW_LINK') ? PHPBB_DB_NEW_LINK : false);

// We do not need this any longer, unset for safety purposes
unset($dbpasswd);

// Grab global variables, re-cache if necessary
$config = $cache->obtain_config();

// Makes phpBB use the server_name, script_path etc variables defined in config (forum.tools4games.com/)
// instead of using the current host/path (blacklist.tools4games.com) for urls (for PM email notifications and such)
$config['force_server_vars'] = 1;

?>