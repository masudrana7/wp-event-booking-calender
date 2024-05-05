<?php
/**
 * Main initialization class.
 *
 * @package WPEBCalender\app
 */

// Do not allow directly accessing this file.
if (!defined('ABSPATH')) {
	exit('This script cannot be accessed directly.');
}
require_once WPEBCALENDER_PATH . 'vendor/autoload.php';

use WPEBCalender\App\Traits\SingletonTrait;
use WPEBCalender\App\Controllers\Installation;
use WPEBCalender\App\Controllers\Dependencies;
use WPEBCalender\App\Controllers\AssetsController;
use WPEBCalender\App\Controllers\Hooks\FilterHooks;
use WPEBCalender\App\Controllers\Hooks\ActionHooks;
use WPEBCalender\App\Controllers\Admin\AdminMenu;
use WPEBCalender\App\Controllers\Admin\Api;
use WPEBCalender\App\Controllers\Admin\AddPostType;
use WPEBCalender\App\Controllers\Admin\AddPostMeta;
use WPEBCalender\App\Controllers\Admin\PostColumn;
use WPEBCalender\App\Controllers\FrontPage\EventSingle;

if (!class_exists(WPEBCalender::class)) {
	/**
	 * Main initialization class.
	 */
	final class WPEBCalender
	{

		/**
		 * Nonce id
		 *
		 * @var string
		 */
		public $nonceId = 'wpebcalender_wpnonce';

		/**
		 * Post Type.
		 *
		 * @var string
		 */
		//		public $current_theme;
		/**
		 * Post Type.
		 *
		 * @var string
		 */
		public $category = 'wpebcalender_category';
		/**
		 * Singleton
		 */
		use SingletonTrait;

		/**
		 * Class Constructor
		 */
		private function __construct()
		{

			add_action('init', [$this, 'language']);
			add_action('plugins_loaded', [$this, 'init'], 100);
			// Register Plugin Active Hook.
			register_activation_hook(WPEBCALENDER_FILE, [Installation::class, 'activate']);
			// Register Plugin Deactivate Hook.
			register_deactivation_hook(WPEBCALENDER_FILE, [Installation::class, 'deactivation']);

		}

		/**
		 * Assets url generate with given assets file
		 *
		 * @param string $file File.
		 *
		 * @return string
		 */
		public function get_assets_uri($file)
		{
			$file = ltrim($file, '/');
			return trailingslashit(WPEBCALENDER_URL . '/assets') . $file;
		}

		/**
		 * Get the template path.
		 *
		 * @return string
		 */
		public function get_template_path()
		{
			return apply_filters('wpebcalender_template_path', 'templates/');
		}

		/**
		 * Get the plugin path.
		 *
		 * @return string
		 */
		public function plugin_path()
		{
			return untrailingslashit(plugin_dir_path(WPEBCALENDER_FILE));
		}

		/**
		 * Load Text Domain
		 */
		public function language()
		{
			load_plugin_textdomain('wpebcalender', false, WPEBCALENDER_ABSPATH . '/languages/');
		}

		/**
		 * Init
		 *
		 * @return void
		 */
		public function init()
		{
			if (!Dependencies::instance()->check()) {
				return;
			}

			do_action('wpebcalender/before_loaded');
			// Event Post
			new AddPostType();
			new AddPostMeta();
			new EventSingle();
			new PostColumn();

			// Include File.
			AssetsController::instance();
			AdminMenu::instance();
			FilterHooks::init_hooks();
			ActionHooks::init_hooks();
			Api::instance();
			do_action('wpebcalender/after_loaded');



		}

		/**
		 * Checks if Pro version installed
		 *
		 * @return boolean
		 */
		public function has_pro()
		{
			return function_exists('wpebcalenderp');
		}

		/**
		 * PRO Version URL.
		 *
		 * @return string
		 */
		public function pro_version_link()
		{
			return '#';
		}
	}

	/**
	 * @return WPEBCalender
	 */
	function wpebcalender()
	{
		return WPEBCalender::instance();
	}
	wpebcalender();
}
