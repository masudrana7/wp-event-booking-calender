<?php
/**
 * Fns Helpers class
 *
 * @package  WPEBCalender\wpebcalender
 */

namespace WPEBCalender\App\Helpers;

// Do not allow directly accessing this file.
if (!defined('ABSPATH')) {
	exit('This script cannot be accessed directly.');
}

/**
 * Fns class
 */
class Fns
{

	/**
	 *  Verify nonce.
	 *
	 * @return bool
	 */
	public static function verify_nonce()
	{
		$nonce = isset($_REQUEST[wpebcalender()->nonceId]) ? $_REQUEST[wpebcalender()->nonceId] : null;
		if (wp_verify_nonce($nonce, wpebcalender()->nonceId)) {
			return true;
		}

		return false;
	}

	/**
	 * @param $plugin_file_path
	 *
	 * @return bool
	 */
	public static function is_plugins_installed($plugin_file_path = null)
	{
		$installed_plugins_list = get_plugins();

		return isset($installed_plugins_list[$plugin_file_path]);
	}


	/**
	 * @return false|string
	 */
	public static function get_options()
	{
		$defaults = array(

		);
		$options = get_option('wpebcalender_settings');
		return wp_parse_args($options, $defaults);
	}


}
