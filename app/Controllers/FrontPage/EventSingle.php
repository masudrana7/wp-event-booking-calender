<?php
namespace WPEBCalender\App\Controllers\FrontPage;

class EventSingle
{
    public function __construct()
    {
        add_action('init', [$this, 'init']);
    }

    public function init()
    {
        add_filter('the_content', [$this, 'display_event_date']);
    }

    public function display_event_date($content)
    {
        if (is_single()) {
            $post_id = get_the_ID();
            $event_date = esc_html(get_post_meta($post_id, 'datepicker', true));

            $event_date2 = get_post_meta($post_id, 'datepicker2', true);
            $date_two = '';
            if (!empty($event_date2)) {
                $date_two = esc_html($event_date2 ? '-' . $event_date2 : '');
            }
            $title = esc_html__('Event Date: ', 'wpebcalender');
            $view_date = "<div style='border:1px solid #e1e1e1;padding: 30px; font-size: 20px; font-weight: 600; border-radius: 10px;' class='event_date'>" . $title . $event_date . $date_two . "</div>";
            return $content . $view_date;
        }
    }
}
