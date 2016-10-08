<?php

if (!class_exists('Softsdev_Events_Post_Type')) :

        class Softsdev_Events_Post_Type {

                /**
                 *
                 * @var type 
                 */
                var $version = 1;

                /**
                 * Constructor
                 */
                function __construct() {
                        // Runs when the plugin is activated
                        register_activation_hook(__FILE__, array(&$this, 'plugin_activation'));
                        // Add support for translations
                        load_plugin_textdomain('softsdev', false, dirname(plugin_basename(__FILE__)) . '/lang/');
                        // Adds the softsdev events post type and taxonomies
                        add_action('init', array(&$this, 'softsdev_events_init'));
                        // Thumbnail support for softsdev events posts
                        add_theme_support('post-thumbnails', array('softsdev_events'));
                        // Adds columns in the admin view for thumbnail and taxonomies
                        add_filter('manage_edit-softsdev_events_columns', array(&$this, 'softsdev_events_edit_columns'));
                        // Custom column
                        add_action('manage_posts_custom_column', array(&$this, 'softsdev_events_column_display'), 10, 2);
                        // Admin script
                        add_action('admin_enqueue_scripts', array(&$this, 'load_softsdev_events_admin_style'));

                        // Register Taxonomy
                        add_action('init', array(&$this, 'sofsdev_resister_event_taxaonomy'), 0);
                        /**
                         * Admin panel
                         */
                        add_action('admin_init', array(&$this, 'softsdev_event_admin'));
                        /**
                         * Single template add
                         */
                        add_filter('template_include', array(&$this, 'softsdev_include_event_template_function'), 1);

                        add_action('save_post', array(&$this, 'softsdev_event_saving_fields'), 10, 2);
                }

                /**
                 * Flushes rewrite rules on plugin activation to ensure softsdev events posts don't 404
                 * http://codex.wordpress.org/Function_Reference/flush_rewrite_rules
                 */
                function plugin_activation() {
                        $this->softsdev_events_init();
                        flush_rewrite_rules();
                }

                /**
                 * <b>#Filter</b>
                 * softsdev_events_args<br>
                 * Softsdev Event INIT
                 */
                function softsdev_events_init() {
                        /**
                         * Enable the Softsdev Events custom post type
                         * http://codex.wordpress.org/Function_Reference/register_post_type
                         */
                        $labels = array(
                            'name' => __('Softsdev Events', 'softsdev'),
                            'singular_name' => __('Events', 'softsdev'),
                            'add_new' => __('Add New Event', 'softsdev'),
                            'add_new_item' => __('Add New Events', 'softsdev'),
                            'edit_item' => __('Edit Events', 'softsdev'),
                            'new_item' => __('Add New Events', 'softsdev'),
                            'view_item' => __('View Event', 'softsdev'),
                            'search_items' => __('Search Events', 'softsdev'),
                            'not_found' => __('No events found', 'softsdev'),
                            'not_found_in_trash' => __('No events found in trash', 'softsdev')
                        );

                        $args = array(
                            'labels' => $labels,
                            'public' => true,
                            'rewrite' => true,
                            'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'comments', 'revisions'),
                            'capability_type' => 'post',
                            'rewrite' => array('slug' => 'softsdev_events'), // Permalinks format
                            'menu_position' => 5,
                            'has_archive' => true
                        );

                        $args = apply_filters('softsdev_events_args', $args);
                        /**
                         * Register Post Type Softsdev Event
                         */
                        register_post_type('softsdev_events', $args);
                }

                /**
                 * Add Columns to Softsdev Events Edit Screen
                 * http://wptheming.com/2010/07/column-edit-pages/
                 * @param type $softsdev_events_columns
                 * @return string
                 */
                function softsdev_events_edit_columns($softsdev_events_columns) {
                        $softsdev_events_columns = array(
                            "cb" => "<input type=\"checkbox\" />",
                            "title" => _x('Title', 'column name'),
                            "softsdev_events_thumbnail" => __('Thumbnail', 'softsdev'),
                            "softsdev_event_category" => __('Events Category', 'softsdev'),
                            "softsdev_event_tags" => __('Event Tags', 'softsdev'),
                            "author" => __('Author', 'softsdev'),
                            "comments" => __('Comments', 'softsdev'),
                            "date" => __('Date', 'softsdev'),
                        );
                        $softsdev_events_columns['comments'] = '<div class="vers"><img alt="Comments" src="' . esc_url(admin_url('images/comment-grey-bubble.png')) . '" /></div>';
                        return $softsdev_events_columns;
                }

                /**
                 * 
                 * @param type $softsdev_events_columns
                 * @param type $post_id
                 */
                function softsdev_events_column_display($softsdev_events_columns, $post_id) {
                        // Code from: http://wpengineer.com/display-post-thumbnail-post-page-overview
                        switch ($softsdev_events_columns) {
                                // Display the thumbnail in the column view
                                case "softsdev_events_thumbnail":
                                        $width = (int) 80;
                                        $height = (int) 80;
                                        $thumbnail_id = get_post_meta($post_id, '_thumbnail_id', true);
                                        // Display the featured image in the column view if possible
                                        if ($thumbnail_id) {
                                                $thumb = wp_get_attachment_image($thumbnail_id, array($width, $height), true);
                                        }
                                        if (isset($thumb)) {
                                                echo $thumb;
                                        } else {
                                                echo __('None', 'softsdev');
                                        }
                                        break;
                                // Display the softsdev events tags in the column view
                                case "softsdev_event_category":
                                        if ($category_list = get_the_term_list($post_id, 'softsdev_event_category', '', ', ', '')) {
                                                echo $category_list;
                                        } else {
                                                echo __('None', 'softsdev');
                                        }
                                        break;
                                // Display the softsdev events tags in the column view
                                case "softsdev_event_tags":
                                        if ($tag_list = get_the_term_list($post_id, 'softsdev_event_tags', '', ', ', '')) {
                                                echo $tag_list;
                                        } else {
                                                echo __('None', 'softsdev');
                                        }
                                        break;
                        }
                }

                /**
                 * 
                 */
                function load_softsdev_events_admin_style() {
                        global $post_type;

                        if ($post_type == 'softsdev_events') {

                                /**
                                 * Datetime picker
                                 */
                                wp_register_style('jquery-datetimepicker_css', plugin_dir_url(__FILE__) . 'css/jquery.datetimepicker.css');
                                wp_enqueue_style('jquery-datetimepicker_css');
                                wp_register_script('jquery-datetimepicker_js', plugin_dir_url(__FILE__) . 'js/jquery.datetimepicker.js', array('jquery', 'jquery-ui-core', 'jquery-ui-tabs'));
                                wp_enqueue_script('jquery-datetimepicker_js');

                                /**
                                 * Admin CSS with Enabling tab features
                                 */
                                wp_register_style('jquery-ui-smoothness', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/themes/smoothness/jquery-ui.css', false, null);
                                wp_register_style('sdem_admin_css', plugin_dir_url(__FILE__) . 'css/sdem-admin.css', array('jquery-ui-smoothness'), '1.0.0');
                                wp_enqueue_style('sdem_admin_css');
                                wp_enqueue_style('jquery-ui-tabs');

                                /**
                                 * Google Map API
                                 */
                                wp_enqueue_script('sdem-google-map', 'http://maps.googleapis.com/maps/api/js?sensor=false');
                                wp_enqueue_script('sdem-maps', plugin_dir_url(__FILE__) . 'js/sdem_maps.js');

                                /**
                                 * Wordpress Media
                                 */
                                wp_enqueue_media();

                                /**
                                 * Event Script
                                 */
                                wp_register_script('sdem-admin-js', plugin_dir_url(__FILE__) . 'js/sdem-admin.js', array('jquery', 'jquery-ui-core', 'jquery-ui-tabs'));
                                wp_enqueue_script('sdem-admin-js');
                        }
                }

                /**
                 * Register Event Taxonomy
                 *      - Category
                 *      - Tags
                 */
                function sofsdev_resister_event_taxaonomy() {
                        // Add new taxonomy, make it hierarchical (like categories)
                        $labels = array(
                            'name' => _x('Event Categories', 'softsdev'),
                            'singular_name' => _x('Event Category', 'softsdev'),
                            'search_items' => __('Search Event Category', 'softsdev'),
                            'all_items' => __('All Event Category', 'softsdev'),
                            'parent_item' => __('Parent Event Category', 'softsdev'),
                            'parent_item_colon' => __('Parent Event Categories:', 'softsdev'),
                            'edit_item' => __('Edit Event Category', 'softsdev'),
                            'update_item' => __('Update Event Category', 'softsdev'),
                            'add_new_item' => __('Add New Event Category', 'softsdev'),
                            'new_item_name' => __('New  Event Category Name', 'softsdev'),
                            'menu_name' => __('Category'),
                        );

                        $args = array(
                            'hierarchical' => true,
                            'labels' => $labels,
                            'show_ui' => true,
                            'show_admin_column' => true,
                            'query_var' => true,
                            'rewrite' => array('slug' => 'genre'),
                        );
                        /**
                         * Event Category
                         */
                        register_taxonomy('softsdev_event_category', array('softsdev_events'), $args);
                        $labels = array(
                            'name' => _x('Event Tags', 'softsdev'),
                            'singular_name' => _x('Event Tag', 'softsdev'),
                            'search_items' => __('Search Event Tags'),
                            'popular_items' => __('Popular Event Tags', 'softsdev'),
                            'all_items' => __('All Event Tags', 'softsdev'),
                            'parent_item' => null,
                            'parent_item_colon' => null,
                            'edit_item' => __('Edit Event Tag', 'softsdev'),
                            'update_item' => __('Update Event Tag', 'softsdev'),
                            'add_new_item' => __('Add New Event Tag', 'softsdev'),
                            'new_item_name' => __('New Event Tag Name', 'softsdev'),
                            'separate_items_with_commas' => __('Separate Event Tags with commas', 'softsdev'),
                            'add_or_remove_items' => __('Add or remove Event Tags', 'softsdev'),
                            'choose_from_most_used' => __('Choose from the most used Event Tags', 'softsdev'),
                            'not_found' => __('No Event Tags found.', 'softsdev'),
                            'menu_name' => __('Tags', 'softsdev'),
                        );

                        $args = array(
                            'hierarchical' => false,
                            'labels' => $labels,
                            'show_ui' => true,
                            'show_admin_column' => true,
                            'update_count_callback' => '_update_post_term_count',
                            'query_var' => true,
                            'rewrite' => array('slug' => 'softsdev_event_tags'),
                        );
                        /**
                         * Event Tags
                         */
                        register_taxonomy('softsdev_event_tags', 'softsdev_events', $args);
                }

                /**
                 * 
                 */
                public function softsdev_event_admin() {
                        /**
                         * Extra detail meta box
                         */
                        add_meta_box('softsdev_events_detail_meta_box', 'Extra Details', array(&$this, 'softsdev_events_detail_meta_box'), 'softsdev_events', 'normal', 'high');

                        /**
                         * Image Gallary Meta Box
                         */
                        add_meta_box('softsdev_events_image_meta_box', 'Image', array(&$this, 'softsdev_events_image_meta_box'), 'softsdev_events', 'normal', 'high');
                }

                /**
                 * <b>#Filters( Tab Settings)</b>
                 * <ul>
                 *      <li> sdem_tab_settings_filter </li>
                 *      <li> sdem_tab_setting_tabs_filter</li>
                 * </ul>
                 * @param type $sd_event
                 */
                public function softsdev_events_detail_meta_box($sd_event) {
                        /**
                         * Tab Settings Filter
                         */
                        $tab_settings = apply_filters('sdem_tab_settings_filter', array(
                            'id' => 'sdem-setting-tabs',
                            'type' => 'vertical',
                            /**
                             * Tabl Filter "sdem_tab_setting_tabs_filter"
                             */
                            'tabs' => apply_filters('sdem_tab_setting_tabs_filter', array(
                                'general_event_info' => array(
                                    'title' => __('General', 'softsdev'),
                                    'function' => array($this, 'sdem_general_event_info', array($sd_event)),
                                ),
                                'event_timing' => array(
                                    'title' => __('Timing', 'softsdev'),
                                    'function' => array($this, 'sdem_event_timing', array($sd_event)),
                                ),
                                'event_discount_charges' => array(
                                    'title' => __('Charges & Discount', 'softsdev'),
                                    'function' => array($this, 'sdem_event_charges_and_discount', array($sd_event)),
                                ),
                                'location' => array(
                                    'title' => __('Location', 'softsdev'),
                                    'function' => array($this, 'sdem_event_location', array($sd_event)),
                                ),
                                'register_user_list' => array(
                                    'title' => __('Registered User', 'softsdev'),
                                    'function' => array($this, 'sdem_event_resister_user', array($sd_event)),
                                )
                                    ), $sd_event)
                                ), $sd_event
                        );
                        /**
                         * Calling Tabs
                         */
                        echo CHelper::tabs_generator($tab_settings);
                }

                /**
                 * 
                 * @param type $sd_event
                 */
                public function sdem_general_event_info($sd_event) {
                        $general_settings = $this->softsdev_event_meta_data($sd_event->ID, 'general');
                        $general_settings['sd_event'] = $sd_event;
                        /**
                         * Render General information
                         */
                        CHelper::renderView(SD_EVENTS_ADMIN_VIEWS . '/tab-general_info.php', $general_settings);
                }

                /**
                 * 
                 * @param type $sd_event
                 */
                public function sdem_event_timing($sd_event) {
                        $timing_settings = $this->softsdev_event_meta_data($sd_event->ID, 'timing');
                        $timing_settings['sd_event'] = $sd_event;

                        /**
                         * Render Event timing setting
                         */
                        CHelper::renderView(SD_EVENTS_ADMIN_VIEWS . '/tab-event_timing.php', $timing_settings);
                }

                /**
                 * 
                 * @param type $sd_event
                 */
                public function sdem_event_charges_and_discount($sd_event) {
                        $charges_and_discount_settings = $this->softsdev_event_meta_data($sd_event->ID, 'registration');
                        $charges_and_discount_settings['sd_event'] = $sd_event;
                        /**
                         * Render Event Charges and Discount
                         */
                        CHelper::renderView(SD_EVENTS_ADMIN_VIEWS . '/tab-event_char_n_disc.php', $charges_and_discount_settings);
                }

                /**
                 * 
                 * @param type $sd_event
                 */
                public function sdem_event_location($sd_event) {
                        $location_settings = $this->softsdev_event_meta_data($sd_event->ID, 'location');
                        $location_settings['sd_event'] = $sd_event;
                        /**
                         * Render Event Loaction
                         */
                        CHelper::renderView(SD_EVENTS_ADMIN_VIEWS . '/tab-event_location.php', $location_settings);
                }

                /**
                 * 
                 * @param type $sd_event
                 */
                public function sdem_event_resister_user($sd_event) {
                        /**
                         * Render Registered Users
                         */
                        CHelper::renderView(
                                SD_EVENTS_ADMIN_VIEWS . '/tab-registered_users.php', array(
                            'sd_event' => $sd_event
                                )
                        );
                }

                /**
                 * 
                 * @param type $sd_event
                 */
                public function softsdev_events_image_meta_box($sd_event) {
                        $gallary_settings = $this->softsdev_event_meta_data($sd_event->ID, 'gallery');

                        /**
                         * Render Upload Image Meta Box
                         */
                        CHelper::renderView(
                                SD_EVENTS_ADMIN_VIEWS . '/mb-event_images.php', array(
                            'sd_event' => $sd_event,
                            'gallery' => $gallary_settings
                                )
                        );
                }

                /**
                 * 
                 * @param type $template_path
                 * @return string
                 */
                public function softsdev_include_event_template_function($template_path) {
                        if (get_post_type() == 'softsdev_events') {
                                if (is_single()) {
                                        // checks if the file exists in the theme first,
                                        // otherwise serve the file from the plugin
                                        if ($theme_file = locate_template(array('single-softsdev_events.php'))) {
                                                $template_path = $theme_file;
                                        } else {
                                                $template_path = SD_EVENTS_TEMPLATES . '/single-softsdev_events.php';
                                        }
                                }
                                if ( is_archive()) {
                                        // checks if the file exists in the theme first,
                                        // otherwise serve the file from the plugin
                                        if ($theme_file = locate_template(array('archive-softsdev_events.php'))) {
                                                $template_path = $theme_file;
                                        } else {
                                                $template_path = SD_EVENTS_TEMPLATES . '/archive-softsdev_events.php';
                                        }
                                }                                
                        }
                        return $template_path;
                }

                /**
                 * Save Event Settings
                 * @param type $event_id
                 * @param type $sd_event
                 */
                public function softsdev_event_saving_fields($event_id, $sd_event) {
                        // Restrict to save for autosave
                        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                                return $event_id;
                        }

                        // Restrict to save for revisions
                        if (isset($sd_event->post_type) && $sd_event->post_type == 'revision') {
                                return $event_id;
                        }

                        // Check post type for movie reviews

                        if ($sd_event->post_type == 'softsdev_events') {
                                // Store data in post meta table if present in post data
                                if (isset($_POST['sdem']) && $_POST['sdem'] != '') {
                                        update_post_meta($event_id, 'sdem_data', $_POST['sdem']);
                                }
                        }
                }

                /**
                 * Get Settings
                 * @param type $event_id
                 * @param type $type
                 * @return type
                 */
                public function softsdev_event_meta_data($event_id, $type = null) {
                        $default_setting = $this->softsdev_default_event_post_meta();
                        $_settings = get_post_meta($event_id, 'sdem_data', true);
                        $settings = $_settings ? $_settings : $default_setting;

                        return ( $type != null ) ? ( array_key_exists($type, $settings) ? $settings[$type] : null ) : $settings;
                }

                /**
                 * 
                 * @return type
                 */
                public function softsdev_default_event_post_meta() {
                        return array
                            (
                            'general' => array
                                (
                                'organized_by' => null,
                                'people_capacity' => null,
                                'special_guest' => null,
                                'event_type' => null,
                                'is_enable_registration' => null,
                                'registration_type' => null
                            ),
                            'timing' => array
                                (
                                'start_date' => null,
                                'end_date' => null,
                                'week_day' => array(),
                                'till_date' => null,
                                'registration_opening_date' => null,
                                'cut_off_date' => null
                            ),
                            'registration' => array
                                (
                                'price_per_ticket' => null,
                                'discount_on_ticket' => null,
                                'coupon_code' => null,
                                'coupon_discount' => null
                            ),
                            'location' => array
                                (
                                'title' => null,
                                'address_1' => null,
                                'address_2' => null,
                                'city' => null,
                                'state' => null,
                                'zip' => null,
                                'country' => null,
                                'contact_number' => null,
                                'contact_email' => null
                            ),
                            'gallery' => null,
                            'lat' => null,
                            'long' => null
                        );
                }

        }

        /**
         * Create object for Softsdev_Events_Post_Type
         */
        $sdem_post_type = new Softsdev_Events_Post_Type;
endif;
?>