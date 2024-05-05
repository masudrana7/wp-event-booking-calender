<?php

namespace WPEBCalender\App\Controllers\Admin;

// Do not allow directly accessing this file.
if (!defined('ABSPATH')) {
    exit('This script cannot be accessed directly.');
}

use WPEBCalender\App\Traits\SingletonTrait;

/**
 * Sub menu class
 *
 * @author Mostafa <mostafa.soufi@hotmail.com>
 */
class AdminMenu
{
    /**
     * Singleton
     */
    use SingletonTrait;

    /**
     * Autoload method
     * @return void
     */
    private function __construct()
    {
        add_action('admin_menu', array($this, 'register_sub_menu'));
    }

    /**
     * Register submenu
     * @return void
     */
    public function register_sub_menu()
    {
        add_submenu_page(
            'edit.php?post_type=wpebcalender_events',
            'Event Calendar',
            'Event Calendar',
            'manage_options',
            'wpebcalender-admin',
            [$this, 'wp_media_page_callback'],
        );
    }

    /**
     * Render submenu
     * @return void
     */
    public function wp_media_page_callback()
    {
        echo '<div class="wrap">
                <div id="wpebcalender_root">
                    <div id="calendar"></div>
                </div>
            </div>'; ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var calendarEl = document.getElementById('calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                    },
                    initialDate: '2024-05-01',
                    navLinks: true, // can click day/week names to navigate views
                    businessHours: true, // display business hours
                    editable: true,
                    selectable: true,
                    events: [
                        <?php $args = array(
                            'post_type' => 'wpebcalender_events',
                            'posts_per_page' => -1,
                        );
                        $events_query = new \WP_Query($args);
                        if ($events_query->have_posts()) {
                            while ($events_query->have_posts()) {
                                $events_query->the_post();
                                $title = get_the_title();
                                $start_date = get_post_meta(get_the_ID(), 'datepicker', true);
                                $formatted_date = date("Y-m-d", strtotime($start_date));
                                $start_date2 = get_post_meta(get_the_ID(), 'datepicker2', true);
                                $formatted_date2 = date("Y-m-d", strtotime($start_date2));
                                $url = get_the_permalink();
                                ?> {
                                    title: '<?php echo addslashes($title); ?>',
                                    url: '<?php echo addslashes($url); ?>',
                                    start: '<?php echo addslashes($formatted_date); ?>',
                                    end: '<?php echo addslashes($formatted_date2); ?>',
                                },
                            <?php }
                            wp_reset_postdata();
                        } ?>
                    ]
                });
                calendar.render();
            });
        </script>
    <?php }

}
