<?php

namespace WPEBCalender\App\Controllers;

use WPEBCalender\App\Traits\SingletonTrait;

// Do not allow directly accessing this file.
if (!defined('ABSPATH')) {
    exit('This script cannot be accessed directly.');
}

/**
 * AssetsController
 */
class AssetsController
{
    /**
     * Singleton
     */
    use SingletonTrait;

    /**
     * Plugin version
     *
     * @var string
     */
    private $version;

    /**
     * Ajax URL
     *
     * @var string
     */
    private $ajaxurl;

    /**
     * Class Constructor
     */
    public function __construct()
    {
        $this->version = (defined('WP_DEBUG') && WP_DEBUG) ? time() : WPEBCALENDER_VERSION;
        /**
         * Admin scripts.
         */
        add_action('admin_enqueue_scripts', [$this, 'backend_assets'], 1);
        add_action('admin_enqueue_scripts', array($this, 'register_admin_scripts'));
    }


    public function register_admin_scripts()
    {

        // CSS File
        wp_enqueue_style('datepicker-ui', WPEBCALENDER_URL . '/assets/css/jquery-ui.css');
        wp_enqueue_style('custom', WPEBCALENDER_URL . '/assets/css/custom.css');
        // JS File


        wp_enqueue_script('datepicker-ui', WPEBCALENDER_URL . '/assets/js/jquery-ui.js', array('jquery'), '1.0.0', true);
        wp_enqueue_script('datepicker-min', WPEBCALENDER_URL . '/assets/js/index.global.js', array('jquery'), '1.0.0', true);
        wp_enqueue_script('custom', WPEBCALENDER_URL . '/assets/js/custom.js', array('jquery'), '1.0.0', true);

    }

    /**
     * Registers Admin scripts.
     *
     * @return void
     */
    public function backend_assets($hook)
    {

        $scripts = [
            [
                'handle' => 'wpebcalender-timepicker',
                'src' => wpebcalender()->get_assets_uri('js/jquery.timepicker.min.js'),
                'deps' => [],
                'footer' => true,
            ]
        ];

        // Register public scripts.
        foreach ($scripts as $script) {
            wp_register_script($script['handle'], $script['src'], $script['deps'], $this->version, $script['footer']);
        }

        $current_screen = get_current_screen();
        if (isset($current_screen->id) && 'toplevel_page_wpebcalender-admin' === $current_screen->id) {
            wp_enqueue_style('wpebcalender-settings');
            wp_enqueue_script('wpebcalender-settings');

            wp_localize_script(
                'wpebcalender-settings',
                'wpebcalenderParams',
                [
                    'ajaxUrl' => esc_url(admin_url('admin-ajax.php')),
                    'adminUrl' => esc_url(admin_url()),
                    'restApiUrl' => esc_url_raw(rest_url()), // site_url(rest_get_url_prefix()),
                    'rest_nonce' => wp_create_nonce('wp_rest'),
                    wpebcalender()->nonceId => wp_create_nonce(wpebcalender()->nonceId),
                ]
            );

        }

    }
}
