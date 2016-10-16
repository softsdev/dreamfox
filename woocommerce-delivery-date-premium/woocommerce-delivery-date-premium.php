<?php
/**
 * Plugin Name: Woocommerce Delivery Date
 * Plugin URI: www.dreamfoxmedia.nl 
 * Version: 1.1.3
 * Author URI: www.dreamfoxmedia.nl
 * Author: Marco van Loghum Slaterus
 * Description: Extend Woocommerce plugin to add delivery date on checkout
 * Requires at least: 3.7
 * Tested up to: 4.6.1
 * License: GPLv3 or later
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 * Text Domain: woocommerce-delivery-date
 * Domain Path: /lang/
 * @Developer : Anand Rathi ( Softsdev )
 */
/**
 * Check if WooCommerce is active
 */
define('DELIVERY_DATE_SECRET_KEY', '568ef1d445c081.84974482'); //Rename this constant name so it is specific to your plugin or theme.
// This is the URL where API query request will be sent to. This should be the URL of the site where you have installed the main license manager plugin. Get this value from the integration help page.
define('DELIVERY_DATE_LICENSE_SERVER_URL', 'http://www.dreamfoxmedia.com'); //Rename this constant name so it is specific to your plugin or theme.
// This is a value that will be recorded in the license manager data so you can identify licenses for this item/product.
define('DELIVERY_DATE_ITEM_REFERENCE', 'Delivery Date'); //Rename this constant name so it is specific to your plugin or theme.
if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
}
if ((in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) ||
    is_plugin_active_for_network('woocommerce/woocommerce.php')) && 
    !function_exists('softsdev_delivery_date')
) {
     /* ----------------------------------------------------- */
        // update checker
        require 'plugin-update-checker/plugin-update-checker.php';
        $MyUpdateChecker = PucFactory::buildUpdateChecker(
            'http://www.dreamfoxmedia.com/update-plugins/?action=get_metadata&slug=woocommerce-delivery-date-premium', //Metadata URL.
            __FILE__, //Full path to the main plugin file.
            'woocommerce-delivery-date-premium' //Plugin slug. Usually it's the same as the name of the directory.
        );
        /**
         *  load text domain
         */
        add_action('plugins_loaded', 'softsdev_dd_load_textdomain');
        /**
         *  Submenu on woocommerce section
         */
        add_action('admin_menu', 'softsdev_delivery_submenu_page');
        /**
         *  delivery date selection on checkout page field
         */
        add_filter('woocommerce_checkout_fields', 'softsdev_dd_checkout_field');
        /**
         * Delivery date field html
         */
        add_action('woocommerce_checkout_after_customer_details', 'softsdev_extra_checkout_fields');
        /**
         *  update delivery date
         */
        add_action('woocommerce_checkout_update_order_meta', 'softsdev_dd_checkout_field_update_order_meta', 10, 2);
        /**
         * delivery date on email
         */
        add_action('woocommerce_email_after_order_table', 'softsdev_dd_email_with_delivery_date', 15, 2);
        /**
         * delivery date on order detail admin page
         */
        add_action('woocommerce_admin_order_data_after_order_details', 'softsdev_display_order_data_in_admin');
        /**
         * delivery date on order view & thank you page
         */
        add_action('woocommerce_order_details_after_order_table', 'softsdev_dd_order_view', 20);
        /**
         * delivery date on order view & thank you page
         */
        add_action('woocommerce_thankyou', 'softsdev_dd_order_view', 20);
        /* ----------------------------------------------------- */
        /**
         * softsdev language textdomain
         */
        function softsdev_dd_load_textdomain() {
                load_plugin_textdomain('softsdev', false, dirname(plugin_basename(__FILE__)) . '/lang/');
        }
        /**
         * woocommerce delivery date menu	
         */
        function softsdev_delivery_submenu_page() {
                add_submenu_page('woocommerce', __('Delivery Date', 'softsdev'), __('Delivery Date', 'softsdev'), 'manage_options', 'delivery-date', 'softsdev_delivery_date');
        }
        /**
         * woocommerce delivery date menu	
         */
        function softsdev_dd_script_style() {
                wp_register_style('wdd_admin', plugins_url('css/wdd_admin.css', __FILE__));
                wp_register_style('wdd_front', plugins_url('css/wdd_front.css', __FILE__));
                wp_register_style('jquery-ui-css', plugins_url('css/jquery-ui.css', __FILE__));
                wp_register_style('style_new', plugins_url('css/style.css', __FILE__));
        }
        add_action('init', 'softsdev_dd_script_style');
         /* ----------------------------------------------------- */
        /**
         * Update footer version
         */
        function softsdev_delivery_date_update_footer($text) {
            if(! empty( $_GET['page'] ) && strpos( $_GET['page'], 'delivery-date' ) === 0 ) {
                $text = 'Version 1.1.3';
            }
            return $text;   
        }
        /* ----------------------------------------------------- */
        /**
         * Update footer text
         */
        function softsdev_delivery_date_footer_text( $text ) {
        if(! empty( $_GET['page'] ) && strpos( $_GET['page'], 'delivery-date' ) === 0 ) {
            $text = sprintf( '' );
        }
        return $text;
    }
        /**
     * Check license
     * @return true / false
     */
    function softsdev_delivery_date_valid_license()
    {
        $license_key = get_option('delivery_date_license_key');
        $api_params = array(
            'slm_action' => 'slm_check',
            'secret_key' => DELIVERY_DATE_SECRET_KEY,
            'license_key' => $license_key,
        );
        // Send query to the license manager server
        $query = esc_url_raw(add_query_arg($api_params, DELIVERY_DATE_LICENSE_SERVER_URL));
        $response = wp_remote_get($query, array('timeout' => 20, 'sslverify' => false));
        $has_valid_license = false;
        if (!is_wp_error($response)) {
            $license_data = json_decode(wp_remote_retrieve_body($response));
            if ($license_data->result=='success') {
                $has_valid_license = true;
            }
        }
        return $has_valid_license;
    }
    /**
     * Type: updated,error,update-nag
     */
    if (!function_exists('softsdev_notice')) {
        function softsdev_notice($message, $type)
        {
            $html = <<<EOD
<div class="{$type} notice">
<p>{$message}</p>
</div>
EOD;
            echo $html;
        }
    }
        /* ----------------------------------------------------- */
        /**
         * Delivery date setting
         */
        function softsdev_delivery_date() {
                add_filter( 'admin_footer_text', 'softsdev_delivery_date_footer_text' );
                add_filter( 'update_footer', 'softsdev_delivery_date_update_footer' );
                echo '<div class="wrap wrap-dd-paid"><div id="icon-tools" class="icon32"></div>';
                echo '<h2>' . __('Delivery Date', 'softsdev') . '</h2>';
                $args = array(
                    'hide_empty' => 0,
                    'orderby' => 'slug',
                    'order' => 'ASC',
                );
                $product_categories = get_terms('product_cat', $args);
                // fwt and set settings
                if (isset($_POST['delivery_date'])) {
                        $dd_setting = $_POST['delivery_date'];
                        if (isset($_POST['delivery_date']['cat']) && isset($_POST['delivery_date']['cat']['day'])) {
                                $categories = $_POST['delivery_date']['cat']['day'];
                                foreach ($categories as $term_id => $cat_day) {
                                        update_woocommerce_term_meta($term_id, 'softsdev_dd_days', $cat_day);
                                }
                        }
                        update_option('delivery_date_full_setting', $dd_setting);
                } else {
                        $dd_setting = get_option('delivery_date_full_setting', array());
                }
                $no_of_days_to_deliver = ( $dd_setting ) && array_key_exists('no_of_days_to_deliver', $dd_setting) ? $dd_setting['no_of_days_to_deliver'] : 0;
                $applicable_categories = ( $dd_setting ) && array_key_exists('categories', $dd_setting) ? $dd_setting['categories'] : array();
                ?>
                <div class="left-dd-paid ">
                <?php if (softsdev_delivery_date_valid_license()): ?>
                <form id="woo_dd" action="<?php echo $_SERVER['PHP_SELF'] . '?page=delivery-date' ?>" method="post">
                    <div class="postbox " style="padding: 10px; margin: 10px 0px;">
                        <h3 class="hndle"><?php echo __('No of day\'s to Delivery', 'softsdev'); ?></h3>				
                        <input type="number"  max="999" min="0" step="1" value="<?php echo $no_of_days_to_deliver ?>" name="delivery_date[no_of_days_to_deliver]" id="no_of_days_to_deliver" /><br />
                        <small><?php echo __('How many days user can select delivery date', 'softsdev'); ?></small>
                    </div>
                    <div class="postbox" style="padding: 10px; margin: 10px 0px;">				
                        <h3 class="hndle"><?php echo __('Applicable Categories', 'softsdev'); ?></h3>
                        <small><?php echo __('Select categories for to choose delivery date on checkout', 'softsdev'); ?></small><br>					
                        <small><?php echo __('-- Set blank >> use global setting.', 'softsdev'); ?></small><br>				
                        <small><?php echo __('-- Set 0 >> For today\'s delivery date.', 'softsdev'); ?></small>					
                        <div>
                            <ul id="applicable_category">
                                <?php
                                echo '<li class="dd_heading"><span>Category</span><span style="float:right">Days</span></li>';
                                foreach ($product_categories as $category) {
                                        if (in_array($category->term_id, $applicable_categories)) {
                                                $checked = 'checked="checked"';
                                                $class = 'checked';
                                        } else {
                                                $class = '';
                                                $checked = '';
                                        }
                                        echo '<li class="' . $class . '"><input ' . $checked . ' id="pro_cat_' . $category->term_id . '" name="delivery_date[categories][]" type="checkbox" value="' . $category->term_id . '" /><label for="pro_cat_' . $category->term_id . '"> ' . ucwords($category->name) . '</label><input ' . $checked . ' id="pro_cat_day_' . $category->term_id . '" name="delivery_date[cat][day][' . $category->term_id . ']" type="number"  max="999" min="0" step="1" value="' . get_woocommerce_term_meta($category->term_id, 'softsdev_dd_days') . '" /></li>';
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                    <?php
                    $date_format = get_option('date_format');
                    $js_date_format = softsdev_date_format_php_to_js(get_option('date_format'));
                    $count_only_working_days = is_array($dd_setting['holyday_weekends']) && array_key_exists('count_only_working_days', $dd_setting['holyday_weekends']) ? $dd_setting['holyday_weekends']['count_only_working_days'] : '';
                    $is_dd_required = array_key_exists('other_settings', $dd_setting) && is_array($dd_setting['other_settings']) && array_key_exists('is_dd_required', $dd_setting['other_settings']) ? $dd_setting['other_settings']['is_dd_required'] : '';
                    $show_empty_dd = array_key_exists('other_settings', $dd_setting) && is_array($dd_setting['other_settings']) && array_key_exists('show_empty_dd', $dd_setting['other_settings']) ? $dd_setting['other_settings']['show_empty_dd'] : '';
                    $disable_days = is_array($dd_setting['holyday_weekends']) && array_key_exists('disable_days', $dd_setting['holyday_weekends']) ? $dd_setting['holyday_weekends']['disable_days'] : array();
                    $holydays = is_array($dd_setting['holyday_weekends']) && array_key_exists('holydays', $dd_setting['holyday_weekends']) ? $dd_setting['holyday_weekends']['holydays'] : array();
                    $start_date_1 = !empty($holydays[1]['start_date']) ? mysql2date($date_format, $holydays[1]['start_date']) : '';
                    $end_date_1 = !empty($holydays[1]['end_date']) ? mysql2date($date_format, $holydays[1]['end_date']) : '';
                    $start_date_2 = !empty($holydays[2]['start_date']) ? mysql2date($date_format, $holydays[2]['start_date']) : '';
                    $end_date_2 = !empty($holydays[2]['end_date']) ? mysql2date($date_format, $holydays[2]['end_date']) : '';
                    $start_date_3 = !empty($holydays[3]['start_date']) ? mysql2date($date_format, $holydays[3]['start_date']) : '';
                    $end_date_3 = !empty($holydays[3]['end_date']) ? mysql2date($date_format, $holydays[3]['end_date']) : '';
                    $start_date_4 = !empty($holydays[4]['start_date']) ? mysql2date($date_format, $holydays[4]['start_date']) : '';
                    $end_date_4 = !empty($holydays[4]['end_date']) ? dmysql2date($date_format, $holydays[4]['end_date']) : '';
                    $start_date_5 = !empty($holydays[5]['start_date']) ? mysql2date($date_format, $holydays[5]['start_date']) : '';
                    $end_date_5 = !empty($holydays[5]['end_date']) ? mysql2date($date_format, $holydays[5]['end_date']) : '';
                    $delivery_date_label = array_key_exists('other_settings', $dd_setting) && is_array($dd_setting['other_settings']) && array_key_exists('delivery_date_label', $dd_setting['other_settings']) ? $dd_setting['other_settings']['delivery_date_label'] : __('Select delivery date', 'softsdev');
                    $delivery_date_field_desc = array_key_exists('other_settings', $dd_setting) && is_array($dd_setting['other_settings']) && array_key_exists('delivery_date_field_desc', $dd_setting['other_settings']) ? $dd_setting['other_settings']['delivery_date_field_desc'] : __('We will try our best to deliver your order on the specified date', 'softsdev');
                    ?>
                    <div class="postbox " style="padding: 10px; margin: 10px 0px;">
                        <h3 class="hndle"><?php echo __('Holidays & Weekends', 'softsdev'); ?></h3>
                        <table width="100%" class="form-table">
                            <tr>
                                <th width="170px">
                                    <label for="count_only_working_days"><?php echo __('Count Only Working Days', 'softsdev') ?> </label>
                                    <img width="16" height="16" src="<?php echo plugins_url('images/help.png', __FILE__) ?>" class="help_tip" title="<?php echo __('Set Delivery dates only on weekends', 'softsdev'); ?>">
                                </th>
                                <td>
                                    <input id="count_only_working_days" name="delivery_date[holyday_weekends][count_only_working_days]" type="checkbox" value="1" <?php echo checked('1', $count_only_working_days) ?> />
                                    <br />
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label for="disable_days"><?php echo __('Disable Days for Delivery', 'softsdev') ?></label>
                                    <img width="16" height="16" src="<?php echo plugins_url('images/help.png', __FILE__) ?>" class="help_tip" title="<?php echo __('Disable days to not delivery product on perticuler day', 'softsdev'); ?>">								
                                </th>
                                <td>
                                    <input id="monday" name="delivery_date[holyday_weekends][disable_days][]" type="checkbox" value="2" <?php echo in_array('2', $disable_days) ? "checked='checked'" : '' ?> />
                                    <label for="monday"><?php echo __('Monday', 'softsdev'); ?></label> 
                                    <input id="tuesday" name="delivery_date[holyday_weekends][disable_days][]" type="checkbox" value="3" <?php echo in_array('3', $disable_days) ? "checked='checked'" : '' ?> />
                                    <label for="tuesday"><?php echo __('Tuesday', 'softsdev'); ?></label> 
                                    <input id="wednesday" name="delivery_date[holyday_weekends][disable_days][]" type="checkbox" value="4" <?php echo in_array('4', $disable_days) ? "checked='checked'" : '' ?> />
                                    <label for="wednesday"><?php echo __('Wednesday', 'softsdev'); ?></label> 
                                    <input id="thursday" name="delivery_date[holyday_weekends][disable_days][]" type="checkbox" value="5" <?php echo in_array('5', $disable_days) ? "checked='checked'" : '' ?> />
                                    <label for="thursday"><?php echo __('Thursday', 'softsdev'); ?></label>  
                                    <input id="friday" name="delivery_date[holyday_weekends][disable_days][]" type="checkbox" value="6" <?php echo in_array('6', $disable_days) ? "checked='checked'" : '' ?> />
                                    <label for="friday"><?php echo __('Friday', 'softsdev'); ?></label> 
                                    <input id="saturday" name="delivery_date[holyday_weekends][disable_days][]" type="checkbox" value="7" <?php echo in_array('7', $disable_days) ? "checked='checked'" : '' ?> />
                                    <label for="saturday"><?php echo __('Saturday', 'softsdev'); ?></label> 
                                    <input id="sunday" name="delivery_date[holyday_weekends][disable_days][]" type="checkbox" value="1" <?php echo in_array('1', $disable_days) ? "checked='checked'" : '' ?> />
                                    <label for="sunday"><?php echo __('Sunday', 'softsdev'); ?></label> 
                                    <br />
                                </td>
                            </tr>
                            <tr class="holyday_range">
                                <th>
                                    <label><?php echo __('Holidays', 'softsdev') ?></label>
                                    <img width="16" height="16" src="<?php echo plugins_url('images/help.png', __FILE__) ?>" class="help_tip" title="<?php echo __('Set holidays', 'softsdev'); ?>">					
                                </th>
                                <td>
                                    <label style="display: inline-block; width: 20px;">I] </label>
                                    <label for="start_date_1"><?php echo __('Start Date', 'softsdev') ?></label> <input id="start_date_1" class="start_date" name="delivery_date[holyday_weekends][holydays][1][start_date]" type="text" value="<?php echo $start_date_1 ?>" />
                                    <label for="end_date_1"><?php echo __('End Date', 'softsdev') ?></label> <input id="end_date_1" class="end_date" name="delivery_date[holyday_weekends][holydays][1][end_date]" type="text" value="<?php echo $end_date_1 ?>"/>
                                </td>
                            </tr>
                            <tr class="holyday_range">
                                <th></th>
                                <td>
                                    <label style="display: inline-block; width: 20px;">II] </label>
                                    <label for="start_date_2"><?php echo __('Start Date', 'softsdev') ?></label> <input id="start_date_2" class="start_date" name="delivery_date[holyday_weekends][holydays][2][start_date]" type="text" value="<?php echo $start_date_2 ?>" />
                                    <label for="end_date_2"><?php echo __('End Date', 'softsdev') ?></label> <input id="end_date_2" class="end_date" name="delivery_date[holyday_weekends][holydays][2][end_date]" type="text" value="<?php echo $end_date_2 ?>"/>
                                </td>
                            </tr>                    
                            <tr class="holyday_range">
                                <th></th>
                                <td>
                                    <label style="display: inline-block; width: 20px;">III] </label>
                                    <label for="start_date_3"><?php echo __('Start Date', 'softsdev') ?></label> <input id="start_date_3" class="start_date" name="delivery_date[holyday_weekends][holydays][3][start_date]" type="text" value="<?php echo $start_date_3 ?>" />
                                    <label for="end_date_3"><?php echo __('End Date', 'softsdev') ?></label> <input id="end_date_3" class="end_date" name="delivery_date[holyday_weekends][holydays][3][end_date]" type="text" value="<?php echo $end_date_3 ?>"/>
                                </td>
                            </tr>                    
                            <tr class="holyday_range">
                                <th></th>
                                <td>
                                    <label style="display: inline-block; width: 20px;">IV] </label>                            
                                    <label for="start_date_4"><?php echo __('Start Date', 'softsdev') ?></label> <input id="start_date_4" class="start_date" name="delivery_date[holyday_weekends][holydays][4][start_date]" type="text" value="<?php echo $start_date_4 ?>" />
                                    <label for="end_date_4"><?php echo __('End Date', 'softsdev') ?></label> <input id="end_date_4" class="end_date" name="delivery_date[holyday_weekends][holydays][4][end_date]" type="text" value="<?php echo $end_date_4 ?>"/>
                                </td>
                            </tr>                    
                            <tr class="holyday_range">
                                <th></th>
                                <td>
                                    <label style="display: inline-block; width: 20px;">V] </label>
                                    <label for="start_date_5"><?php echo __('Start Date', 'softsdev') ?></label> <input id="start_date_5" class="start_date" name="delivery_date[holyday_weekends][holydays][5][start_date]" type="text" value="<?php echo $start_date_5 ?>" />
                                    <label for="end_date_5"><?php echo __('End Date', 'softsdev') ?></label> <input id="end_date_5" class="end_date" name="delivery_date[holyday_weekends][holydays][5][end_date]" type="text" value="<?php echo $end_date_5 ?>"/>
                                </td>
                            </tr>                    
                        </table>
                    </div>	
                    <div class="postbox " style="padding: 10px; margin: 10px 0px;">
                        <h3 class="hndle"><?php echo __('Other Settings', 'softsdev'); ?></h3>
                        <table width="100%" class="form-table">
                            <tr>
                                <th>
                                    <label><?php echo __('Delivery date a required', 'softsdev') ?></label>
                                    <img width="16" height="16" src="<?php echo plugins_url('images/help.png', __FILE__) ?>" class="help_tip" title="<?php echo __('Delivery date a required field Yes/No', 'softsdev'); ?>">								
                                </th>
                                <td>
                                    <input id="is_dd_required" name="delivery_date[other_settings][is_dd_required]" type="checkbox" value="1" <?php echo checked('1', $is_dd_required) ?> />
                                </td>
                            </tr> 
                            <tr>
                                <th>
                                    <label><?php echo __('Show Empty Delivery date on Checkout Page default', 'softsdev') ?></label>
                                    <img width="16" height="16" src="<?php echo plugins_url('images/help.png', __FILE__) ?>" class="help_tip" title="<?php echo __('Show Empty Delivery date on Checkout Page default Yes/No', 'softsdev'); ?>">								
                                </th>
                                <td>
                                    <input id="is_dd_required" name="delivery_date[other_settings][show_empty_dd]" type="checkbox" value="1" <?php echo checked('1', $show_empty_dd) ?> />
                                </td>
                            </tr>        
                            <tr>
                                <th>
                                    <label><?php echo __('Delivery Date Label on Checkout Page', 'softsdev') ?></label>
                                    <img width="16" height="16" src="<?php echo plugins_url('images/help.png', __FILE__) ?>" class="help_tip" title="<?php echo __('Default text: Select delivery date', 'softsdev'); ?>">					
                                </th>
                                <td>
                                    <input name="delivery_date[other_settings][delivery_date_label]" type="text" value="<?php echo $delivery_date_label ?>" placeholder="<?php echo __('Select delivery date', 'softsdev'); ?>" style="width: 500px" />
                                </td>
                            </tr> 
                            <tr>
                                <th>
                                    <label><?php echo __('Delivery Date Field Description on Checkout Page', 'softsdev') ?></label>
                                    <img width="16" height="16" src="<?php echo plugins_url('images/help.png', __FILE__) ?>" class="help_tip" title="<?php echo __('Default text: We will try our best to deliver your order on the specified date', 'softsdev'); ?>">								
                                </th>
                                <td>
                                    <input name="delivery_date[other_settings][delivery_date_field_desc]" type="text" value="<?php echo $delivery_date_field_desc; ?>" placeholder="<?php echo __('We will try our best to deliver your order on the specified date', 'softsdev'); ?>" style="width: 500px"  />
                                </td>
                            </tr>                     
                        </table>
                    </div> 
                    <input class="button-large button-primary" type="submit" value="Save changes" />
                </form>
                <div class="license-key-new">
                <?php else: ?>
                <div class="license-key">
                <?php endif; ?>            
                <?php 
                    /*** License activate button was clicked ***/
                        if (isset($_REQUEST['activate_license'])) {
                            $license_key = $_REQUEST['delivery_date_license_key'];
                            // API query parameters
                            $api_params = array(
                                'slm_action' => 'slm_activate',
                                'secret_key' => DELIVERY_DATE_SECRET_KEY,
                                'license_key' => $license_key,
                                'registered_domain' => $_SERVER['SERVER_NAME'],
                                'item_reference' => urlencode(DELIVERY_DATE_ITEM_REFERENCE),
                            );
                            // Send query to the license manager server
                            $query = esc_url_raw(add_query_arg($api_params, DELIVERY_DATE_LICENSE_SERVER_URL));
                            $response = wp_remote_get($query, array('timeout' => 20, 'sslverify' => false));
                            // Check for error in the response
                            if (is_wp_error($response)){
                                softsdev_notice("Unexpected Error! The query returned with an error.", 'error');
                            }
                            //var_dump($response);//uncomment it if you want to look at the full response
                            // License data.
                            $license_data = json_decode(wp_remote_retrieve_body($response));
                            // TODO - Do something with it.
                            //var_dump($license_data);//uncomment it to look at the data
                            if($license_data->result == 'success'){//Success was returned for the license activation
                                //Uncomment the followng line to see the message that returned from the license server
                                softsdev_notice('The following message was returned from the server: '.$license_data->message. '. You must reload the page to see result!', 'updated');
                                //Save the license key in the options table
                                update_option('delivery_date_license_key', $license_key); 
                            }
                            else{
                                //Show error to the user. Probably entered incorrect license key.
                                //Uncomment the followng line to see the message that returned from the license server
                                softsdev_notice('The following message was returned from the server: '.$license_data->message, 'error');
                            }
                        }
                        /*** End of license activation ***/
                        /*** License activate button was clicked ***/
                        if (isset($_REQUEST['deactivate_license'])) {
                            $license_key = $_REQUEST['delivery_date_license_key'];
                            // API query parameters
                            $api_params = array(
                                'slm_action' => 'slm_deactivate',
                                'secret_key' => DELIVERY_DATE_SECRET_KEY,
                                'license_key' => $license_key,
                                'registered_domain' => $_SERVER['SERVER_NAME'],
                                'item_reference' => urlencode(DELIVERY_DATE_ITEM_REFERENCE),
                            );
                            // Send query to the license manager server
                            $query = esc_url_raw(add_query_arg($api_params, DELIVERY_DATE_LICENSE_SERVER_URL));
                            $response = wp_remote_get($query, array('timeout' => 20, 'sslverify' => false));
                            // Check for error in the response
                            if (is_wp_error($response)){
                                softsdev_notice("Unexpected Error! The query returned with an error.", 'error');
                            }
                            // License data.
                            $license_data = json_decode(wp_remote_retrieve_body($response));
                            // TODO - Do something with it.
                            if($license_data->result == 'success'){//Success was returned for the license activation
                                //Uncomment the followng line to see the message that returned from the license server
                                softsdev_notice('The following message was returned from the server: '.$license_data->message, 'updated');
                                //Remove the licensse key from the options table. It will need to be activated again.
                                update_option('delivery_date_license_key', '');
                            }
                            else{
                                //Show error to the user. Probably entered incorrect license key.
                                //Uncomment the followng line to see the message that returned from the license server
                                softsdev_notice('The following message was returned from the server: '.$license_data->message, 'error');
                            }
                        }
                        /*** End of  license deactivation ***/
                ?>               
                    <p>Please enter the license key for this product to activate it. You were given a license key when you purchased this item.</p>
                    <form id="woo_dd_license" action="<?php echo $_SERVER['PHP_SELF'] . '?page=delivery-date' ?>" method="post">
                        <table class="form-table">
                            <tr>
                                <th style="width:100px;"><label for="delivery_date_license_key">License Key</label></th>
                                <td ><input class="regular-text" type="text" id="delivery_date_license_key" name="delivery_date_license_key" value="<?php echo get_option('delivery_date_license_key'); ?>" ></td>
                            </tr>
                        </table>
                        <p class="submit">
                            <input type="submit" name="activate_license" value="Activate" class="button-primary" />
                            <input type="submit" name="deactivate_license" value="Deactivate" class="button" />
                        </p>
                    </form>
                </div>
                 </div>
                 <div class="right-dd-paid ">
                <div style="border: 5px dashed #B0E0E6; padding: 0 20px; background: white;">
                    <h3><?php echo __('Woocommerce Delivery Date','softsdev'); ?></h3>
                    <p><?php echo __('This plugin has also a Premium version with several powerful features.', 'softsdev') ?><a href="http://www.dreamfoxmedia.com/shop/woocommerce-delivery-date-pro/"><?php echo __('Take a look here', 'softsdev') ?></a>!</p>
                </div>
                <?php $user = wp_get_current_user(); ?>
                <!-- Begin Paypal donation Signup Form -->
                <form style="text-align:center; border-bottom:1px solid #ccc; border-top:1px solid #ccc; padding:20px 0; margin:20px 0;" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                    <center><?php echo __('Like this product we’ve made and want to contribute to its future development? Donate however much you’d like with the below donate button.', 'softsdev') ?><br><br></center>    
                    <input type="hidden" name="cmd" value="_s-xclick">
                    <input type="hidden" name="hosted_button_id" value="UNTLWQSLRH85U">
                    <input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
                    <img alt="" border="0" src="https://www.paypalobjects.com/nl_NL/i/scr/pixel.gif" width="1" height="1">
                </form>
                <link href="//cdn-images.delivery_date.com/embedcode/slim-081711.css" rel="stylesheet" type="text/css">
                <style type="text/css">
                    #mc_embed_signup{clear:left; font:14px Helvetica,Arial,sans-serif; }
                </style>
                <div id="mc_embed_signup" style="border-bottom:1px solid #ccc">
                    <form action="//dreamfoxmedia.us3.list-manage.com/subscribe/post?u=a0293a6a24c69115bd080594e&amp;id=131c5e0c11" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
                        <div id="mc_embed_signup_scroll">
                        <label for="mce-EMAIL"><?php echo __('Subscribe to our mailing list', 'softsdev') ?></label>
                        <input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="email address" required>
                        <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                        <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_a0293a6a24c69115bd080594e_131c5e0c11" tabindex="-1" value=""></div>
                        <div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
                        </div>
                    </form>
                </div>
                <div class="wctmg-box" >
                    <h4 class="wctmg-title"><?php echo __( 'Looking for help?', 'softsdev' ); ?></h4>
                    <p><?php echo __( 'We have some resources available to help you in the right direction.', 'softsdev' ); ?></p>
                    <ul class="ul-square">
                        <li>
                            <a href="http://www.dreamfoxmedia.com/ufaq-category/wctmg-f/#utm_source=wp-plugin&utm_medium=wctmg&utm_campaign=helpbar"><?php echo __( 'Knowledge Base', 'softsdev' ); ?></a>
                        </li>
                        <li>
                            <a href="https://wordpress.org/plugins/woocommerce-delivery-date/faq/"><?php echo __( 'Frequently Asked Questions', 'softsdev' ); ?></a>
                        </li>
                    </ul>
                    <p><?php echo sprintf( __( 'If your answer can not be found in the resources listed above, please use the <a href="%s">support forums on WordPress.org</a>.', 'softsdev' ), 'https://wordpress.org/support/plugin/woocommerce-delivery-date' ); ?></p>
                    <p><?php echo sprintf( __( 'Found a bug? Please <a href="%s">open an issue on GitHub</a>.', 'softsdev' ), 'https://github.com/dreamfoxnl' ); ?></p>
                </div>
            </div>
                <script type="text/javascript"> 
                        jQuery(document).ready(function () {
                            jQuery("#applicable_category input:checkbox").change(function () {
                                var is_checked = jQuery(this).is(':checked');
                                if (is_checked)
                                    jQuery(this).attr('checked', 'checked').parents('li').addClass('checked');
                                else
                                    jQuery(this).parents('li').removeClass('checked');
                            });
                        });
                </script>
                <?php
                $locale = getJqueryUII18nLocale();
                if ($locale) {
                        $lang_path = plugins_url('/js/datepicker-native/datepicker-' . $locale . '.js', __FILE__);
                        wp_enqueue_script('softsdev-jquery-ui-i18n-' . $locale, $lang_path, array('jquery-ui-datepicker'));
                }  else {
                        wp_enqueue_script('jquery-ui-datepicker');
                }
                wp_enqueue_style('wdd_admin');
                wp_enqueue_style('style_new');
                wp_enqueue_style('jquery-ui-css');
                $current_date = date($date_format, strtotime(current_time('mysql')));
                echo '<script language="javascript">jQuery(document).ready(function(){
					jQuery(".start_date, .end_date").width("150px");
					 jQuery( ".start_date" ).datepicker({
						defaultDate: "+1w",
						changeMonth: true,
						changeYear: true,
						minDate: "'.$current_date.'",
						dateFormat: "' . $js_date_format . '",
                                                numberOfMonths: 2,
						yearRange: "' . date('Y') . ':' . (date('Y') + 4) . '",						
					});
					jQuery( ".end_date" ).datepicker({
						defaultDate: "+1w",
						changeMonth: true,
						changeYear: true,												
                        minDate: "'.$current_date.'",
						dateFormat: "' . $js_date_format . '",						
						numberOfMonths: 2,
						yearRange: "' . date('Y') . ':' . (date('Y') + 4) . '",						
					});	
				});</script>';
                echo '</div>';
        }
        /**
         * Get the locale according to the format available in the jquery ui i18n file list
         * @url https://github.com/jquery/jquery-ui/tree/master/ui/i18n
         * @return string ex: "fr" ou "en-GB"
         */
        function getJqueryUII18nLocale() {
                //replace _ by - in "en_GB" for example
                $locale = str_replace('_', '-', get_locale());
                switch ($locale) {
                        case 'ar-DZ':
                        case 'cy-GB':
                        case 'en-AU':
                        case 'en-GB':
                        case 'en-NZ':
                        case 'fr-CH':
                        case 'nl-BE':
                        case 'nl-BE':
                        case 'pt-BR':
                        case 'sr-SR':
                        case 'zh-CN':
                        case 'zh-HK':
                        case 'zh-TW':
                                //For all this locale do nothing the file already exist
                                break;
                        default:
                                //for other locale keep the first part of the locale (ex: "fr-FR" -> "fr")
                                $locale = substr($locale, 0, strpos($locale, '-'));
                                //English is the default locale
                                $locale = ($locale == 'en') ? '' : $locale;
                                break;
                }
                return $locale;
        }
        /* ----------------------------------------------------- */
        /**
         * @global type $woocommerce
         * @param type $fields
         * @return string
         */
        function softsdev_dd_checkout_field($fields) {
                $date_format = get_option('date_format');
                $js_date_format = softsdev_date_format_php_to_js(get_option('date_format'));
                $show_delivery_datepicker = false;
                $max_day = '';
                $dd_setting = get_option('delivery_date_full_setting', array());
                $applicable_categories = ( $dd_setting ) && array_key_exists('categories', $dd_setting) ? $dd_setting['categories'] : array();
                global $woocommerce;
                $cart_products = $woocommerce->cart->get_cart();
                foreach ($cart_products as $_product) {
                        $category_list = wp_get_post_terms($_product['product_id'], 'product_cat', array('fields' => 'ids'));
                        foreach ($category_list as $category_id) {
                                $softsdev_dd_day_cat = get_woocommerce_term_meta($category_id, 'softsdev_dd_days');
                                /**
                                 * Finding maximum days for category
                                 */
                                if ($softsdev_dd_day_cat !== '' && $softsdev_dd_day_cat > $max_day) {
                                        $max_day = $softsdev_dd_day_cat;
                                }
                        }
                        $is_common = array_intersect($category_list, $applicable_categories);
                        if (count($is_common) && $show_delivery_datepicker == false) {
                                $show_delivery_datepicker = true;
                        }
                }
                if ($show_delivery_datepicker === false) {
                        return $fields;
                } else {
                        $dates_to_deliver = $max_day !== '' ? $max_day : ( isset($dd_setting['no_of_days_to_deliver']) && is_numeric($dd_setting['no_of_days_to_deliver']) ? $dd_setting['no_of_days_to_deliver'] : 0 );
                        $locale = getJqueryUII18nLocale();
                        if ($locale) {
                            $lang_path = plugins_url('/js/datepicker-native/datepicker-' . $locale . '.js', __FILE__);
                            wp_enqueue_script('softsdev-jquery-ui-i18n-' . $locale, $lang_path, array('jquery-ui-datepicker'));
                        }  else {
                                wp_enqueue_script('jquery-ui-datepicker');
                        }
                        wp_enqueue_style('wdd_front');
                        wp_enqueue_style('jquery-ui-css');
                        $is_dd_required = array_key_exists('other_settings', $dd_setting) && is_array($dd_setting['other_settings']) && array_key_exists('is_dd_required', $dd_setting['other_settings']) ? $dd_setting['other_settings']['is_dd_required'] : '';
                        $show_empty_dd = array_key_exists('other_settings', $dd_setting) && is_array($dd_setting['other_settings']) && array_key_exists('show_empty_dd', $dd_setting['other_settings']) ? $dd_setting['other_settings']['show_empty_dd'] : '';
                        $count_only_working_days = array_key_exists('count_only_working_days', $dd_setting['holyday_weekends']) ? $dd_setting['holyday_weekends']['count_only_working_days'] : 0;
                        $disable_days = array_key_exists('disable_days', $dd_setting['holyday_weekends']) ? $dd_setting['holyday_weekends']['disable_days'] : array();
                        $holydays = array_key_exists('holydays', $dd_setting['holyday_weekends']) ? $dd_setting['holyday_weekends']['holydays'] : array();
                        $count = 0;
                        // combine weekends and disable_days
                        $disable_days[] = 999;
                        $_dates_of_holydays = array();
                        if (is_array($holydays) && $holydays) {
                                foreach ($holydays as $holiday) {
                                    if($holiday['start_date'] != ""){
                                        $_dates_of_holydays[] = softsdev_date_range($holiday['start_date'], $holiday['end_date'], '+1 day','Y-m-d');
                                    }
                                }
                        }
                        $dates_of_holydays = array_flatten($_dates_of_holydays);
                        //$dates_of_holydays = ( $holydays ) ? softsdev_date_range($holydays['start_date'], $holydays['end_date'], '+1 day', $date_format) : array();
                        $week_ends = array(1, 7);
                        $holy = 0;
                        $current_date = date($date_format, strtotime(current_time('mysql')));
                        while ($dates_to_deliver >= $count) {
                                $min_delivery_date = softsdev_create_from_format($date_format, $current_date, $count);
                                $day = date('w', softsdev_strtotime($min_delivery_date)) + 1;
                                $count++;
                        }
                        $dates_to_deliver = $dates_to_deliver + $holy;
                        if($dates_to_deliver == 0){
                            $dates_to_deliver = $current_date;
                        }else{
                            $current_date_f = date('m/d/Y',strtotime($current_date));
                            $dates_to_deliver = date($date_format, strtotime($current_date_f."+$dates_to_deliver days"));
                        }
                        $_min_delivery_date = $min_delivery_date;
                        // Skip if date is disable
                        for ($i = 0; $i < 7; $i++) {
                                $min_delivery_date = softsdev_create_from_format($date_format, $_min_delivery_date, $i);
                                $day = date('w', softsdev_strtotime($min_delivery_date)) + 1;
                                if (!in_array($day, $disable_days))
                                        break;
                                else {
                                        $min_delivery_date = softsdev_create_from_format($date_format, $min_delivery_date, $i);
                                }
                        }
                        // REgister script for checkout
                        wp_register_script('dd_checkout_script', plugins_url('/js/dd_checkout_script.js', __FILE__));
                        wp_enqueue_script('dd_checkout_script');
                        $delivery_date_label = array_key_exists('other_settings', $dd_setting) && is_array($dd_setting['other_settings']) && array_key_exists('delivery_date_label', $dd_setting['other_settings']) && $dd_setting['other_settings']['delivery_date_label'] ? $dd_setting['other_settings']['delivery_date_label'] : __('Select delivery date', 'softsdev');
                        $delivery_date_field_desc = array_key_exists('other_settings', $dd_setting) && is_array($dd_setting['other_settings']) && array_key_exists('delivery_date_field_desc', $dd_setting['other_settings']) && $dd_setting['other_settings']['delivery_date_field_desc'] ? $dd_setting['other_settings']['delivery_date_field_desc'] : __('We will try our best to deliver your order on the specified date', 'softsdev');
                        // Setting all JavaScript variables
                        $translation_array = array(
                            'holydays' => implode(',', $dates_of_holydays),
                            'days_dis' => implode(',', $disable_days),
                            'date_format' => $js_date_format,
                            'dates_to_deliver' => $dates_to_deliver,
                            'year_range' => date('Y') . ':' . (date('Y') + 4),
                            'msg_text' => $delivery_date_field_desc
                        );
                        wp_localize_script('dd_checkout_script', 'dd', $translation_array);
                        $fields['delivery_date'] = array(
                            'delivery_date' => apply_filters('checkout_delivery_filed_setting', array(
                                'type' => 'text',
                                'class' => array('delivery-date form-row-wide'),
                                'label' => $delivery_date_label,
                                'default' => $show_empty_dd != 1 ? $min_delivery_date : '',
                                'required' => $is_dd_required == 1 ? true : false,
                            ))
                        );
                }
                return $fields;
        }
        /**
         * Update the order meta with field value
         * @param type $order_id
         */
        function softsdev_dd_checkout_field_update_order_meta($order_id) {
                if (isset($_POST['delivery_date']) && !empty($_POST['delivery_date'])) {
                        update_post_meta($order_id, 'Delivery Date', esc_attr($_POST['delivery_date']));
                }
        }
        /**
         * 
         * @param type $order
         */
        function softsdev_dd_order_view($order) {
                $delivery_date = @get_post_meta($order->id, 'Delivery Date', true);
                if (!empty($delivery_date)) {
                        $date_format = get_option('date_format');
                        $delivery_date = mysql2date($date_format, $delivery_date);
                        echo '<div>';
                        echo '<header class="title"><h3>' . __('Delivery Date', 'softsdev') . '</h3></header>';
                        echo '<dl>' . $delivery_date . '</dl>';
                        echo '</div>';
                }
        }
        /**
         * 
         * @param type $order
         * @param type $is_admin_email
         */
        function softsdev_dd_email_with_delivery_date($order, $is_admin_email) {
                $delivery_date = @get_post_meta($order->id, 'Delivery Date', true);
                if (!empty($delivery_date)) {
                        $date_format = get_option('date_format');
                        $delivery_date = mysql2date($date_format, $delivery_date);
                        echo '<p><strong>' . __('Delivery Date', 'softsdev') . ':</strong> ' . $delivery_date . '</p>';
                }
        }

        /**
         * display the extra field on the checkout form
         */
        function softsdev_extra_checkout_fields() {
                $checkout = WC()->checkout();
                if (!array_key_exists('delivery_date', $checkout->checkout_fields))
                        return false;
                ?>
                <div class="delivery-date" id="dd__checkout_field">
                    <h3><?php echo __('Delivery Info', 'softsdev'); ?></h3>
                    <?php foreach ($checkout->checkout_fields['delivery_date'] as $key => $field) : ?>
                            <?php woocommerce_form_field($key, $field, $checkout->get_value($key)); ?>
                <?php endforeach; ?>
                </div>
                <?php
        }
        /**
         * display the extra data in the order admin panel
         * @param type $order
         */
        function softsdev_display_order_data_in_admin($order) {
                ?>
                <div class="order_data_column">
                    <h4><?php __('Delivery Date', 'softsdev'); ?></h4>
                <?php echo '<p>' . get_post_meta($order->id, 'Delivery Date', true) . '</p>'; ?>
                </div>
                <?php
        }
        /* --------COMMON FUNCTION---------- */
        /**
         * 
         * @param type $first
         * @param type $last
         * @param type $step
         * @param type $format
         * @return type
         */
        function softsdev_date_range($first, $last, $step = '+1 day', $format = 'd-m-Y') {
                $dates = array();
                $current = strtotime($first);
                $last = strtotime($last);
                /**
                 * 
                 */
                while ($current <= $last) {
                        $dates[] = date($format, $current);
                        $current = strtotime($step, $current);
                }
                return $dates;
        }
        /**
         * 
         * @param type $dateString
         * @return type
         */
        function softsdev_date_format_php_to_js($dateString) {
                $pattern = array(
                    //day
                    'd', //day of the month
                    'j', //3 letter name of the day
                    'l', //full name of the day
                    'z', //day of the year
                    //month
                    'F', //Month name full
                    'M', //Month name short
                    'n', //numeric month no leading zeros
                    'm', //numeric month leading zeros
                    //year
                    'Y', //full numeric year
                    'y' //numeric year: 2 digit
                );
                $replace = array(
                    'dd', 'd', 'DD', 'o',
                    'MM', 'M', 'm', 'mm',
                    'yy', 'y'
                );
                foreach ($pattern as &$p) {
                        $p = '/' . $p . '/';
                }
                return preg_replace($pattern, $replace, $dateString);
        }
        /**
         * 
         * @param type $date_format
         * @param type $date
         * @param type $days
         * @return type
         */
        function softsdev_create_from_format($date_format, $date, $days = '') {
                $dt = SoftsdevDDDateTime::createFromFormat($date_format, $date);
                $date = $days ? $dt->modify('+' . ( $days ) . ' day')->format($date_format) : $dt->format($date_format);
                return $date;
        }
        /**
         * 
         * @param type $array
         * @return boolean
         */
        function array_flatten($array) {
                if (!is_array($array)) {
                        return FALSE;
                }
                $result = array();
                foreach ($array as $key => $value) {
                        if (is_array($value)) {
                                $result = array_merge($result, array_flatten($value));
                        } else {
                                $result[$key] = $value;
                        }
                }
                return array_unique($result);
        }
        /**
         * 
         * @param type $date
         * @return type
         */
        function softsdev_strtotime($date) {
                $timestamp = strtotime($date);
                if ($timestamp === FALSE) {
                        $timestamp = strtotime(str_replace('/', '-', $date));
                }
                return $timestamp;
        }
        
        
        
 function admin_print_js() {
    global $post, $wp_locale;
 
    //add the jQuery UI elements shipped with WP
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'jquery-ui-datepicker' );

    //localize our js
    $aryArgs = array(
        'closeText'         => __( 'Done', UNIQUE_TEXT_DOMAIN ),
        'currentText'       => __( 'Today', UNIQUE_TEXT_DOMAIN ),
        'monthNames'        => strip_array_indices( $wp_locale->month ),
        'monthNamesShort'   => strip_array_indices( $wp_locale->month_abbrev ),
        'monthStatus'       => __( 'Show a different month', UNIQUE_TEXT_DOMAIN ),
        'dayNames'          => strip_array_indices( $wp_locale->weekday ),
        'dayNamesShort'     => strip_array_indices( $wp_locale->weekday_abbrev ),
        'dayNamesMin'       => strip_array_indices( $wp_locale->weekday_initial ),
        // set the date format to match the WP general date settings
        'dateFormat'        => date_format_php_to_js( $this->wp_date_format ),
        // get the start of week from WP general setting
        'firstDay'          => get_option( 'start_of_week' ),
        // is Right to left language? default is false
        'isRTL'             => $wp_locale->is_rtl,
    );
 
    // Pass the array to the enqueued JS
    wp_localize_script( 'myplugin-admin', 'objectL10n', $aryArgs );
}        
        
        
        /**
         * SoftsdevDDDateTime Class
         */
        class SoftsdevDDDateTime extends DateTime {
                public static function createFromFormat($format, $time, $timezone = null) {
                        $version = phpversion();
                        if (!$timezone) {
                                $timezone = new DateTimeZone(date_default_timezone_get());
                        }
                        if (version_compare($version, "5.2.17", ">=")) {
                                return parent::createFromFormat($format, $time, $timezone);
                        }
                        return new DateTime(date($format, strtotime($time)), $timezone);
                }
        }
}
?>