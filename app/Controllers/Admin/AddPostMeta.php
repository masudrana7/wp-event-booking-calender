<?php
namespace WPEBCalender\App\Controllers\Admin;

class AddPostMeta
{
    public function __construct()
    {
        add_action('add_meta_boxes', array($this, 'wpd_add_meta'));
        add_action('save_post', array($this, 'wpd_save_metabox'));
    }

    private function is_secured($nonce_field, $action, $post_id)
    {
        $nonce = isset($_POST[$nonce_field]) ? $_POST[$nonce_field] : '';
        if ($nonce == '') {
            return false;
        }
        if (!wp_verify_nonce($nonce, $action)) {
            return false;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return false;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return false;
        }

        return true;
    }

    public function wpd_save_metabox($post_id)
    {
        if (!$this->is_secured('wpd_noce_field', 'wpd_action', $post_id)) {
            return $post_id;
        }

        $date_picker = isset($_POST['datepicker']) ? sanitize_text_field($_POST['datepicker']) : '';
        update_post_meta($post_id, 'datepicker', $date_picker);

        $date_picker2 = isset($_POST['datepicker2']) ? sanitize_text_field($_POST['datepicker2']) : '';
        update_post_meta($post_id, 'datepicker2', $date_picker2);
    }

    public function wpd_add_meta()
    {
        add_meta_box(
            'wpd_wpebcalendar_information',
            __('Event Date', 'wpebcalender'),
            array($this, 'wpd_display_wpebcalendar_name'),
            'wpebcalender_events',
            'normal',
            'default'
        );
    }

    public function wpd_display_wpebcalendar_name($post)
    {
        $date_picker = get_post_meta($post->ID, 'datepicker', true);
        $date_picker2 = get_post_meta($post->ID, 'datepicker2', true);

        wp_nonce_field('wpd_action', 'wpd_noce_field'); ?>
        <div class="wpd-field">
            <label for="datepicker"><?php esc_attr_e('Start Date', 'wpebcalender'); ?></label>
            <input class="input-box datepicker_format" type="text" name="datepicker" id="datepicker"
                value="<?php echo esc_attr($date_picker); ?>" />

            <label for="datepicker2"><?php esc_attr_e('End Date', 'wpebcalender'); ?></label>
            <input class="input-box datepicker_format" type="text" name="datepicker2" id="datepicker2"
                value="<?php echo esc_attr($date_picker2); ?>" />
        </div>
        <?php
    }
}
