<?php
/**
 * @wordpress-plugin
 * Plugin Name:       WP Event Booking Calender
 * Plugin URI:        
 * Description:       WP Event Booking Calendar is a WordPress plugin where shown the event.
 * Version:           1.0.0
 * Author:            WPEBCalender
 * Author URI:        
 * Text Domain:       wpebcalender
 * Domain Path:       /languages
 *
 * @package WPEBCalender\WM
 */

// Do not allow directly accessing this file.
if (!defined('ABSPATH')) {
    exit('This script cannot be accessed directly.');
}

/**
 * Define media edit Constant.
 */
define('WPEBCALENDER_VERSION', '0.0.0');

define('WPEBCALENDER_FILE', __FILE__);

define('WPEBCALENDER_BASENAME', plugin_basename(WPEBCALENDER_FILE));

define('WPEBCALENDER_URL', plugins_url('', WPEBCALENDER_FILE));

define('WPEBCALENDER_ABSPATH', dirname(WPEBCALENDER_FILE));

define('WPEBCALENDER_PATH', plugin_dir_path(__FILE__));

/**
 * App Init.
 */
require_once 'app/app.php';