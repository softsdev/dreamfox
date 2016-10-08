<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * CHelper function
 */
/**
 * Checking if class exits
 */
if (!class_exists('CHelper')) :

        class CHelper {

                /**
                 * Clean Debug method
                 * @param type $variable
                 */
                public static function dump($variable, $var_dump = false) {
                        echo "<pre class='clean_debug'>";
                        if ($var_dump) {
                                var_dump($variable);
                        } else {
                                print_r($variable);
                        }
                        echo "</pre>";
                }

                /**
                 * Clean Debug method
                 * @param type $variable
                 */
                public static function clean_debug($variable, $var_dump = false) {
                        CHelper::dump($variable, $var_dump);
                        die;
                }

                /**
                 * Generating tabs
                 * <pre>$tab_settings = array(
                 *           'id'   => 'sdem-setting-tabs',
                 *           'type' => 'vertical',
                 *           'tabs' => array(
                 *               'basic_event_info' => array(
                 *                   'title'    => 'Basic Info',
                 *                   'function' => array($this, 'sdem_event_basic_info', array($sd_event)),
                 *              ),
                 *               'event_timing'     => array(
                 *                   'title'    => 'Timing',
                 *                   'function' => array($this, 'sdem_event_timing', array($sd_event)),
                 *               ),
                 *               
                 *           )
                 *       );</pre>
                 * @param type $tab_settings
                 * @return boolean
                 */
                public static function tabs_generator($tab_settings = array()) {
                        /**
                         * Return if id is not set
                         */
                        if (!isset($tab_settings['id']) || !isset($tab_settings['tabs']) || !is_array($tab_settings['tabs'])) {
                                return false;
                        }
                        /**
                         * Get class using type
                         */
                        $tab_default_class = ( isset($tab_settings['type']) && $tab_settings['type'] == 'vertical' ) ? 'softsdev-vertical-tabs' : 'softsdev-tabs';

                        $tabs = $tab_settings['tabs'];
                        /**
                         * Initialize tab nav and data
                         */
                        $tabs_nav = $tabs_data = '';
                        /**
                         * Collecting all tabs and its content
                         */
                        foreach ($tabs as $tab_id => $tab_data) {

                                $event_object = $tab_data['function'][0];
                                $event_function = $tab_data['function'][1];
                                $parameters = $tab_data['function'][2] ? : array();
                                /**
                                 * Call to function
                                 */
                                $tab_info = CHelper::call_user_func_array(array($event_object, $event_function), $parameters);
                                $tabs_nav .= '<li><a href="#sdt-' . $tab_id . '">' . $tab_data['title'] . '</a></li>' . "\n";
                                $tabs_data .= '<div id="sdt-' . $tab_id . '">' . $tab_info . '</div>' . "\n";
                        }
                        return '<div id="' . $tab_settings['id'] . '" class="' . $tab_default_class . '">
                                        <ul>' . $tabs_nav . '</ul>
                                        ' . $tabs_data . '
                                </div>';
                }

                /**
                 * call_user_func_array with return method
                 * @param type $obj_n_method
                 * @param type $parameters
                 * @return type
                 */
                public static function call_user_func_array($obj_n_method, $parameters) {
                        ob_start();
                        ob_clean();
                        call_user_func_array($obj_n_method, $parameters);
                        return ob_get_clean();
                }

                /**
                 * Render View
                 * @param type $path
                 * @param type $parameters
                 */
                public static function renderView($path, $parameters = array()) {
                        extract($parameters);
                        include($path);
                }

                /**
                 * 
                 * @param type $tooltip
                 * @return type
                 */
                public static function helpIcon($tooltip) {
                        return '<img width="16" height="16" src="' . SD_EVENTS_IMAGES . '/help.png" class="help_tip mr-rt-5" title="' . $tooltip . '">';
                }

                /**
                 * 
                 * @param type $width
                 * @param type $height
                 * @return type
                 */
                public static function missingImage($width = 150, $height = 50) {
                        return '<img width="' . $width . '" height="' . $width . '" src="' . SD_EVENTS_IMAGES . '/missing-image.png" title="missing-image">';
                }

        }

        endif;
?>