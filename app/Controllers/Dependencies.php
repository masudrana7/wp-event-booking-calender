<?php

namespace WPEBCalender\App\Controllers;

use WPEBCalender\App\Traits\SingletonTrait;
use WPEBCalender\App\Helpers\Fns;

// Do not allow directly accessing this file.
if (!defined('ABSPATH')) {
	exit('This script cannot be accessed directly.');
}

/**
 * Dependencies
 */
class Dependencies
{
	/**
	 * Singleton
	 */
	use SingletonTrait;

	const PLUGIN_NAME = 'Cpt WPEBCalender';

	const MINIMUM_PHP_VERSION = '7.4';

	private $missing = [];
	/**
	 * @var bool
	 */
	private $allOk = true;

	/**
	 * @return bool
	 */
	public function check()
	{

		add_action('wp_ajax_wpebcalender_plugin_activation', [__CLASS__, 'activate_plugin']);
		// TODO:: AJax plugin installation will do later.
		self::notice();

		if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
			add_action('admin_notices', [$this, 'minimum_php_version']);
			$this->allOk = false;
		}

		if (!function_exists('is_plugin_active')) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		if (!function_exists('wp_create_nonce')) {
			require_once ABSPATH . 'wp-includes/pluggable.php';
		}

		if (!empty($this->missing)) {
			add_action('admin_notices', [$this, '_missing_plugins_warning']);

			$this->allOk = false;
		}

		return $this->allOk;
	}

	/**
	 * Admin Notice For Required PHP Version
	 */
	public function minimum_php_version()
	{
		if (isset($_GET['activate'])) {
			unset($_GET['activate']);
		}
		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'wpebcalender'),
			'<strong>' . esc_html__('Custom Post Type Woocommerce Integration', 'wpebcalender') . '</strong>',
			'<strong>' . esc_html__('PHP', 'wpebcalender') . '</strong>',
			self::MINIMUM_PHP_VERSION
		);
		printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
	}


	/**
	 * Adds admin notice.
	 */
	public function _missing_plugins_warning()
	{
		$missingPlugins = '';
		$counter = 0;
		foreach ($this->missing as $plugin) {
			$counter++;
			if ($counter == sizeof($this->missing)) {
				$sep = '';
			} elseif ($counter == sizeof($this->missing) - 1) {
				$sep = ' ' . esc_html__('and', 'wpebcalender') . ' ';
			} else {
				$sep = ', ';
			}
			if (current_user_can('activate_plugins')) {
				$button = '<p><a data-plugin="' . esc_attr(json_encode($plugin)) . '" href="' . esc_url($plugin['url']) . '" class="button-primary plugin-install-by-ajax">' . esc_html($plugin['button_txt']) . '</a></p>';
				// $plugin['message'] Already used escaping function
				printf('<div class="error notice_error"><p>%1$s</p>%2$s</div>', $plugin['message'], $button); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				$missingPlugins .= '<strong>' . esc_html($plugin['name']) . '</strong>' . $sep;
			}
		}
	}

	/**
	 * @param $plugin_file_path
	 *
	 * @return bool
	 */
	public function is_plugins_installed($plugin_file_path = null)
	{
		$installed_plugins_list = get_plugins();

		return isset($installed_plugins_list[$plugin_file_path]);
	}

	/**
	 * Undocumented function.
	 *
	 * @return void
	 */
	public static function notice()
	{
		add_action('admin_enqueue_scripts', function () {
			wp_enqueue_script('jquery');
			wp_enqueue_script('updates');
		});

		add_action('admin_footer', function () { ?>
			<style>
				.wp-core-ui .plugin-install-by-ajax {
					display: inline-flex;
					align-items: center;
					gap: 20px;
				}

				.cptwint-loader {
					border: 4px solid #f3f3f3;
					border-radius: 50%;
					border-top: 4px solid #3498db;
					width: 10px;
					height: 10px;
					-webkit-animation: spin 2s linear infinite;
					animation: spin 2s linear infinite;
					margin-left: 5px;
				}

				/* Safari */
				@-webkit-keyframes spin {
					0% {
						-webkit-transform: rotate(0deg);
					}

					100% {
						-webkit-transform: rotate(360deg);
					}
				}

				@keyframes spin {
					0% {
						transform: rotate(0deg);
					}

					100% {
						transform: rotate(360deg);
					}
				}
			</style>
			<script type="text/javascript">
				(function ($) {

					function ajaxActive(that, plugin) {

						if (that.attr("disabled")) {
							return;
						}

						$.ajax({
							url: '<?php echo admin_url('admin-ajax.php'); ?>',
							data: {
								action: 'wpebcalender_plugin_activation',
								plugin_slug: plugin.slug ? plugin.slug : null,
								activation_file: plugin.file_name,
								wpebcalender_wpnonce: '<?php echo wp_create_nonce(wpebcalender()->nonceId); ?>',
							},
							type: 'POST',
							beforeSend() {
								that.html('Activation Prosses Running... <div class="cptwint-loader"></div>');
							},
							success(response) {
								that.html('Activation Prosses Done');
								that.removeClass('plugin-install-by-ajax');
								that.attr('disabled', 'disabled');
							},
							error(e) { },
						});
					}
					setTimeout(function () {
						$('.plugin-install-by-ajax')
							.on('click', function (e) {
								e.preventDefault();
								var that = $(this);
								if (that.attr("disabled")) {
									return;
								}
								var plugin = $(this).data('plugin');
								console.log(plugin.file_name)
								if (plugin.slug) {
									wp.updates.installPlugin({
										slug: plugin.slug,
										success: function (pluginData) {
											console.log(pluginData, 'Plugin installed successfully!');
											if (pluginData.activateUrl) {
												that.html('Activation Prosses Running... <div class="cptwint-loader"></div>');
												ajaxActive(that, plugin);
											}
										},
										error: function (error) {
											console.log('An error occurred: ' + error.statusText);
										},
										installing: function () {
											that.html('Installing plugin... <div class="cptwint-loader"></div>');
											console.log('Installing plugin...!');
										}
									});
								} else {
									ajaxActive(that, plugin)
								}

							});
					}, 1000);


				})(jQuery);
			</script>
			<?php
		});
	}

	public static function activate_plugin()
	{
		$return = [
			'success' => false,
		];
		if (!Fns::verify_nonce()) {
			wp_send_json_error($return);
		}
		if (!empty($_REQUEST['activation_file']) && is_plugin_inactive($_REQUEST['activation_file'])) {
			activate_plugin(sanitize_text_field($_REQUEST['activation_file']));
			$return['success'] = true;
		}
		if ($return['success']) {
			return wp_send_json_success($return);
		} else {
			wp_send_json_error($return);
		}
		wp_die();
	}
}
