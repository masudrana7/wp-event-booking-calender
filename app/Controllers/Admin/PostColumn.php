<?php
namespace WPEBCalender\App\Controllers\Admin;

class PostColumn
{
    public function __construct()
    {
        add_action('init', [$this, 'init']);
    }
    public function init()
    {
        //add event date  manage_posts_custom_column
        add_filter('manage_wpebcalender_events_posts_columns', [$this, 'event_date_colum']);
        add_action('manage_wpebcalender_events_posts_custom_column', [$this, 'display_event_date'], 10, 2);

        //shortcode sortable columns
        add_filter('manage_edit-wpebcalender_events_sortable_columns', [$this, 'add_sortable_column']);
    }


    public function add_sortable_column($columns)
    {
        $columns['eventdate'] = __('Event Date', 'postviewcount');
        return $columns;
    }

    public function display_event_date($column, $post_id)
    {
        if ($column === 'eventdate') {
            $views = get_post_meta($post_id, 'datepicker', true);
            echo esc_html($views);
        }
    }


    public function event_date_colum($columns)
    {
        $new_columns = [];
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            if ($key == 'title') {
                $new_columns['eventdate'] = __('Event Date', 'wpebcalender');
            }
        }
        return $new_columns;
    }
}





