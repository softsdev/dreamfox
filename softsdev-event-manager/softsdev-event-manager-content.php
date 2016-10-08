<?php

if (!class_exists('Softsdev_Events_Content')) :

        class Softsdev_Events_Content {

                /**
                 * Constructor
                 */
                function __construct() {
                        // Add support for translations
                        load_plugin_textdomain('softsdev', false, dirname(plugin_basename(__FILE__)) . '/lang/');

                        // Front End Style script
                        add_action('wp_enqueue_scripts', array(&$this, 'load_softsdev_events_front_style'));

                        //add_filter('the_post', array(&$this, 'softsdev_event_list'), 20);
                }

                /**
                 * 
                 */
                function load_softsdev_events_front_style() {
                        global $post_type;

                        if ($post_type == 'softsdev_events') {

                                /**
                                 * Datetime picker
                                 */
                                wp_register_style('jquery-datetimepicker_css', plugin_dir_url(__FILE__) . 'css/jquery.datetimepicker.css');
                                wp_enqueue_style('jquery-datetimepicker_css');
                                wp_register_script('jquery-datetimepicker_js', plugin_dir_url(__FILE__) . 'js/jquery.datetimepicker.js', array(
                                    'jquery',
                                    'jquery-ui-core',
                                    'jquery-ui-tabs')
                                );
                                wp_enqueue_script('jquery-datetimepicker_js');

                                /**
                                 * Front CSS with front
                                 */
                                wp_register_style('sdem_front_css', plugin_dir_url(__FILE__) . 'css/sdem-admin.css', '1.0.0');
                                wp_enqueue_style('sdem_front_css');

                                /**
                                 * Lightbox CSS and Script
                                 */
                                wp_register_style('sdem_lightbox_css', plugin_dir_url(__FILE__) . 'css/lightbox.css', '1.0.0');
                                wp_enqueue_style('sdem_lightbox_css');
                                wp_register_script('sdem-lightbox-js', plugin_dir_url(__FILE__) . 'js/lightbox.js', array('jquery'));
                                wp_enqueue_script('sdem-lightbox-js');

                                /**
                                 * Google Map API
                                 */
                                wp_enqueue_script('sdem-google-map', 'http://maps.googleapis.com/maps/api/js?sensor=false');
                                wp_enqueue_script('sdem-maps', plugin_dir_url(__FILE__) . 'js/sdem_maps.js');

                                /**
                                 * Event Script
                                 */
                                wp_register_script('sdem-front-js', plugin_dir_url(__FILE__) . 'js/sdem-front.js', array(
                                    'jquery',
                                    'jquery-ui-core',
                                    'jquery-ui-tabs')
                                );
                                wp_enqueue_script('sdem-front-js');
                        }

                        wp_register_style('sdem_fullcalendar_css', plugin_dir_url(__FILE__) . 'css/fullcalendar.css', '1.0.0');
                        wp_register_script('sdem_fullcalendar', plugin_dir_url(__FILE__) . 'js/fullcalendar.js', array(
                            'jquery',
                            'jquery-ui-core',
                            'jquery-ui-widget',
                            'jquery-ui-button',
                                ), '1.0.0', false);

                        wp_enqueue_script('sdem-moment', plugin_dir_url(__FILE__) . 'js/moment.min.js');
                        wp_enqueue_script('sdem_fullcalendar');
                        wp_enqueue_style('sdem_fullcalendar_css');
                }

                // returns the content of $GLOBALS['post']
                // if the page is called 'debug'
                function softsdev_event_list($post_object) {
                        if (is_archive()) {
                                die;
                                // Add image to the beginning of each page
                                $content = sprintf(
                                        '<img class="post-icon" src="%s/images/post_icon.png" alt="Post icon" title=""/>%s', get_bloginfo('stylesheet_directory'), $content
                                );
                        }
                        // otherwise returns the database content
                        return $content;
                }

        }

        /**
         * Create object for Softsdev_Events_Content
         */
        new Softsdev_Events_Content;
endif;
?>