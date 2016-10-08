<?php
/**
 * Plugin Name: Woocommerce categories Mailchimp groups premium
 * Plugin URI: http://www.dreamfoxmedia.com/project/woocommerce-categories-to-mailchimp-groups-plugin-premium/
 * Version: 1.0.5
 * Author: Dreamfox Media
 * Author URI: http://www.dreamfoxmedia.com
 * Description: Connecting your Mailchimp groups to your WooCommerce categories. You will even be able to connect your 
 * Mailchimp group to any of your individual products. This great plugin will help you to stop sending floral discounts
 * to people who ordered kitchen appliances.
 * Requires at least: 3.7
 * Tested up to: 4.6.1
 * @Developer : Softsdev[Anand Rathi] / Hoang Xuan Hao / Marco van Loghum Slaterus ( Dreamfoxmedia )
 */
/**
 * Check if WooCommerce is active
 */
define('MAILCHIMP_SECRET_KEY', '568ef1d445c081.84974482'); //Rename this constant name so it is specific to your plugin or theme.
//
// This is the URL where API query request will be sent to. This should be the URL of the site where you have installed the main license manager plugin. Get this value from the integration help page.
define('MAILCHIMP_LICENSE_SERVER_URL', 'http://www.dreamfoxmedia.com'); //Rename this constant name so it is specific to your plugin or theme.
// This is a value that will be recorded in the license manager data so you can identify licenses for this item/product.
define('MAILCHIMP_ITEM_REFERENCE', 'Mailchimp Plugin'); //Rename this constant name so it is specific to your plugin or theme.



/**
 * Check if WooCommerce is active
 */
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) && !function_exists('softsdev_mailchimp_settings')) {
    /* ----------------------------------------------------- */
    // update checker
    require 'plugin-update-checker/plugin-update-checker.php';
    $MyUpdateChecker = PucFactory::buildUpdateChecker(
                    'http://www.dreamfoxmedia.com/update-plugins/?action=get_metadata&slug=woocommerce-mailchimp-premium', //Metadata URL.
                    __FILE__, //Full path to the main plugin file.
                    'woocommerce-mailchimp-premium' //Plugin slug. Usually it's the same as the name of the directory.
    );

    // Submenu on woocommerce section
    add_action('admin_menu', 'softsdev_mailchimp_submenu_page');

    /* ----------------------------------------------------- */
    add_action('admin_enqueue_scripts', 'softsdev_mailchimp_enqueue');

    /* ----------------------------------------------------- */

    /**
     * Check license
     * @return true / false
     */
    function softsdev_has_valid_license() {
        $license_key = get_option('mailchimp_license_key');
        $api_params = array(
            'slm_action' => 'slm_check',
            'secret_key' => MAILCHIMP_SECRET_KEY,
            'license_key' => $license_key,
        );
        // Send query to the license manager server
        $query = esc_url_raw(add_query_arg($api_params, MAILCHIMP_LICENSE_SERVER_URL));
        $response = wp_remote_get($query, array('timeout' => 20, 'sslverify' => false));
        $has_valid_license = false;
        if (!is_wp_error($response)) {
            $license_data = json_decode(wp_remote_retrieve_body($response));
            if ($license_data->result == 'success') {
                $has_valid_license = true;
            }
        }
        return $has_valid_license;
    }

    /**
     * Update footer text
     */
    function softsdev_mailchimp_footer_text($text) {
        if (!empty($_GET['page']) && strpos($_GET['page'], 'softsdev-mailchimp') === 0) {
            $text = '<a href="http://www.dreamfoxmedia.com" target="_blank">www.dreamfoxmedia.com</a>';
        }
        return $text;
    }

    /* ----------------------------------------------------- */

    /**

     * Update footer version

     */
    function softsdev_mailchimp_update_footer($text) {
        if (!empty($_GET['page']) && strpos($_GET['page'], 'softsdev-mailchimp') === 0) {
            $text = 'Version 1.0.5';
        }
        return $text;
    }

    /* ----------------------------------------------------- */

    /**
     * Add mailchimp enqueue
     */
    function softsdev_mailchimp_enqueue() {
        wp_enqueue_style('softsdev_mailchimp_enqueue', plugin_dir_url(__FILE__) . '/css/style.css');
    }

    /**
     * Menu of mailchimp page
     */
    function softsdev_mailchimp_submenu_page() {
        add_submenu_page('woocommerce', __('Mailchimp Group premium', 'softsdev'), __('Mailchimp Group premium', 'softsdev'), 'manage_options', 'softsdev-mailchimp', 'softsdev_mailchimp_settings');
    }

    /**
     * Type: updated,error,update-nag
     */
    if (!function_exists('softsdev_notice')) {

        function softsdev_notice($message, $type) {
            $html = <<<EOD
<div class="{$type} notice">
<p>{$message}</p>
</div>
EOD;
            echo $html;
        }
    }

    /**
     * Setting form of mailchimp category
     */
    function softsdev_mailchimp_settings() {
        add_filter('admin_footer_text', 'softsdev_mailchimp_footer_text');
        add_filter('update_footer', 'softsdev_mailchimp_update_footer');
        if (!class_exists('SoftsdevMCAPI')) {
            require 'inc/softsdev_mcapi.class.php';
        }

        // fwt and set settings
        if (isset($_POST['softsdev_mailchimp'])) {
            $softsdev_wc_mc_setting = get_softsdev_wc_mc_setting($_POST['softsdev_mailchimp']);
            $api = new SoftsdevMCAPI($softsdev_wc_mc_setting['api']);
            if (!$api->ping()) {
                $softsdev_wc_mc_setting = '';
            }
            update_option('softsdev_wc_mc_full_setting', $softsdev_wc_mc_setting);
        } else {
            $softsdev_wc_mc_setting = get_softsdev_wc_mc_setting();
        }

        // Getting lists of mailchimp from api
        $lists = array();
        if (isset($softsdev_wc_mc_setting['api'])) {
            $api = new SoftsdevMCAPI($softsdev_wc_mc_setting['api']);
            $listing = $api->lists();
            $lists = $listing['data'];
        }
        echo '<div class="wrap wrap-mc-paid"><div id="icon-tools" class="icon32"></div>';
        echo '<h2 class="title">' . __('Woocommerce categories Mailchimp groups Premium', 'softsdev') . '</h2>';
        ?>
        <div class="left-mc-paid">
            <?php if (softsdev_has_valid_license()): ?>
                <form id="woo_dd" action="<?php echo $_SERVER['PHP_SELF'] . '?page=softsdev-mailchimp' ?>" method="post">
                    <div class="postbox " style="padding: 10px; margin: 10px 0px;">
                        <h3 class="hndle"><?php echo __('Mailchimp Setting', 'softsdev'); ?></h3>
                        <table width="100%" class="form-table">
                            <tr>
                                <th width="170px">
                                    <label for="softsdev_mailchimp_api"><?php echo __('Status', 'softsdev') ?> </label>
                                    <!-- <img width="16" height="16" src="<?php //echo plugins_url('images/help.png', __FILE__)   ?>" class="help_tip" title="<?php //echo __('Mailchimp API Key', 'softsdev');   ?>"> -->
                                </th>
                                <td>
                                    <?php if ($softsdev_wc_mc_setting['api']) { ?>
                                        <span class="status positive"><?php _e('VERBONDEN', 'softsdev'); ?></span>
                                        <span>
                                            <a href = "http://www.dreamfoxmedia.com/ufaq-category/wctmg-p/#utm_source=wp-plugin&utm_medium=wctmg-p&utm_campaign=questionmark" target="_blank">
                                                <img style="margin-left:5px; vertical-align:middle" width="18" height="18" src="<?php echo plugins_url('images/help.png', __FILE__) ?>" class="help_tip" title="<?php echo __('Mailchimp API Key', 'softsdev'); ?>">
                                            </a>
                                            </th>
                                        </span>
                                    <?php } else { ?>
                                        <span class="status neutral"><?php _e('OUT OF SYNC', 'softsdev'); ?></span>
                                        <a href = "http://www.dreamfoxmedia.com/ufaq-category/wctmg-p/#utm_source=wp-plugin&utm_medium=wctmg-p&utm_campaign=questionmark" target="_blank">
                                            <img style="margin-left:5px; vertical-align:middle" width="18" height="18" src="<?php echo plugins_url('images/help.png', __FILE__) ?>" class="help_tip" title="<?php echo __('Mailchimp API Key', 'softsdev'); ?>">
                                        </a>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <th style="width:330px !important">
                                    <label for="softsdev_mailchimp_api"><?php echo __('Mailchimp API', 'softsdev') ?> </label>
                                    <a href = "http://www.dreamfoxmedia.com/ufaq-category/wctmg-p/#utm_source=wp-plugin&utm_medium=wctmg-p&utm_campaign=questionmark" target="_blank">
                                        <img style="margin-left:5px; vertical-align:middle" width="18" height="18" src="<?php echo plugins_url('images/help.png', __FILE__) ?>" class="help_tip" title="<?php echo __('Mailchimp API Key', 'softsdev'); ?>">
                                    </a>
                                </th>
                                <td>
                                    <input id="softsdev_mailchimp_api" name="softsdev_mailchimp[api]" type="text" value="<?php echo @$softsdev_wc_mc_setting['api'] ?>" size="40"/>
                                    <br />
                                    <p>The API key for connecting with your MailChimp account. <a href="https://admin.mailchimp.com/account/api">Get your API key here.</a></p>
                                </td>
                            </tr>
                            <?php if (@$softsdev_wc_mc_setting['api']) { ?>
                                <tr>
                                    <th style="width:330px !important">
                                        <label for="softsdev_mailchimp_list"><?php echo __('Mailchimp List Name', 'softsdev') ?> </label>
                                        <a href = "http://www.dreamfoxmedia.com/ufaq-category/wctmg-p/#utm_source=wp-plugin&utm_medium=wctmg-p&utm_campaign=questionmark" target="_blank">
                                            <img style="margin-left:5px; vertical-align:middle" width="18" height="18" src="<?php echo plugins_url('images/help.png', __FILE__) ?>" class="help_tip" title="<?php echo __('Mailchimp List', 'softsdev'); ?>">
                                        </a>
                                    </th>
                                    <td>
                                        <select id="softsdev_mailchimp[list_mgroup_id]" name="softsdev_mailchimp[list_mgroup_id]">
                                            <option value=""><?php echo __('None', 'softsdev'); ?></option>
                                            <?php
                                            /**
                                             * ":" is seprator
                                             */
                                            foreach ($lists as $list) {
                                                // get all groups of list
                                                $groups = $api->listInterestGroupings($list['id']);
                                                echo "<optgroup label='" . $list['name'] . "'>";
                                                foreach ($groups as $group) {
                                                    echo "<option value='" . $list['id'] . ':' . $group['id'] . "' '" . selected($list['id'] . ':' . $group['id'], $softsdev_wc_mc_setting['list_mgroup_id']) . "'>" . $group['name'] . "</option>";
                                                }
                                                echo "</optgroup>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr> 
                                <tr>
                                    <th style="width:330px !important">
                                        <label for="softsdev_subscribe_customer_event"><?php echo __('Subscribe customers Event', 'softsdev') ?> </label>
                                        <a href = "http://www.dreamfoxmedia.com/ufaq-category/wctmg-p/#utm_source=wp-plugin&utm_medium=wctmg-p&utm_campaign=questionmark" target="_blank">
                                            <img style="margin-left:5px; vertical-align:middle" width="18" height="18" src="<?php echo plugins_url('images/help.png', __FILE__) ?>" class="help_tip" title="<?php echo __('Subscribe customers to MailChimp Event', 'softsdev'); ?>">
                                        </a>								
                                    </th>
                                    <td>
                                        <select id="softsdev_mailchimp[event]" name="softsdev_mailchimp[event]">
                                            <option value="after_order_place" <?php echo selected('after_order_place', @$softsdev_wc_mc_setting['event']); ?>>After Order Place</option>
                                            <option value="after_order_completion" <?php echo selected('after_order_completion', @$softsdev_wc_mc_setting['event']); ?>>After Order Completion</option>
                                        </select>
                                        <br />
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width:330px !important">
                                        <label for="double_opt_in"><?php echo __('Enable Double Opt-In', 'softsdev') ?> </label>
                                        <a href = "http://www.dreamfoxmedia.com/ufaq-category/wctmg-p/#utm_source=wp-plugin&utm_medium=wctmg-p&utm_campaign=questionmark" target="_blank">
                                            <img style="margin-left:5px; vertical-align:middle" width="18" height="18" src="<?php echo plugins_url('images/help.png', __FILE__) ?>" class="help_tip" title="<?php echo __('Enable Double Opt-In confirmation message to customer.', 'softsdev'); ?>">
                                        </a>
                                    </th>
                                    <td>
                                        <input id="double_opt_in" name="softsdev_mailchimp[double_opt_in]" type="checkbox" <?php checked(@$softsdev_wc_mc_setting['double_opt_in'], 1); ?> value="1" />
                                        <br />
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width:330px !important">
                                        <label for="opt_in_on_checkout"><?php echo __('Display Double Opt-in on checkout page', 'softsdev') ?> </label>
                                        <a href = "http://www.dreamfoxmedia.com/ufaq-category/wctmg-p/#utm_source=wp-plugin&utm_medium=wctmg-p&utm_campaign=questionmark" target="_blank">
                                            <img style="margin-left:5px; vertical-align:middle" width="18" height="18" src="<?php echo plugins_url('images/help.png', __FILE__) ?>" class="help_tip" title="<?php echo __('Display an opt-in checkbox on the checkout page.', 'softsdev'); ?>">
                                        </a>
                                    </th>
                                    <td>
                                        <input id="opt_in_on_checkout" name="softsdev_mailchimp[opt_in_on_checkout]" type="checkbox" <?php checked(@$softsdev_wc_mc_setting['opt_in_on_checkout'], 1); ?> value="1" />
                                        <br />
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width:330px !important">
                                        <label for="checked_opt_in_on_checkout"><?php echo __('Default checked Double Opt-in on checkout.', 'softsdev') ?> </label>
                                        <a href = "http://www.dreamfoxmedia.com/ufaq-category/wctmg-p/#utm_source=wp-plugin&utm_medium=wctmg-p&utm_campaign=questionmark" target="_blank">
                                            <img style="margin-left:5px; vertical-align:middle" width="18" height="18" src="<?php echo plugins_url('images/help.png', __FILE__) ?>" class="help_tip" title="<?php echo __('Default checked Double Opt-in on checkout.', 'softsdev'); ?>">
                                        </a>
                                    </th>
                                    <td>
                                        <input id="checked_opt_in_on_checkout" name="softsdev_mailchimp[checked_opt_in_on_checkout]" type="checkbox" <?php checked(@$softsdev_wc_mc_setting['checked_opt_in_on_checkout'], 1); ?> value="1" />
                                        <br />
                                    </td>
                                </tr>                    
                                <tr>
                                    <th style="width:330px !important">
                                        <label for="opt_in_on_checkout_label"><?php echo __('Label next to Double Opt-in on checkout.', 'softsdev') ?> </label>
                                        <a href = "http://www.dreamfoxmedia.com/ufaq-category/wctmg-p/#utm_source=wp-plugin&utm_medium=wctmg-p&utm_campaign=questionmark" target="_blank">
                                            <img style="margin-left:5px; vertical-align:middle" width="18" height="18" src="<?php echo plugins_url('images/help.png', __FILE__) ?>" class="help_tip" title="<?php echo __('Label next to Double Opt-in on checkout.', 'softsdev'); ?>">
                                        </a>
                                    </th>
                                    <td>
                                        <input id="opt_in_on_checkout_label" name="softsdev_mailchimp[opt_in_on_checkout_label]" type="text" value="<?php echo @$softsdev_wc_mc_setting['opt_in_on_checkout_label'] ?>" />
                                        <br />
                                    </td>
                                </tr>                    
                            <?php } ?>
                        </table>
                    </div>              
                    <input class="button-large button-primary" type="submit" value="<?php echo __('Save Changes', 'softsdev'); ?>" />
                </form>
                <div class="license-key-new">
                <?php else: ?>
                    <div class="license-key">
                    <?php endif; ?>            
                    <?php
                    /*                     * * License activate button was clicked ** */
                    if (isset($_REQUEST['activate_license'])) {
                        $license_key = $_REQUEST['mailchimp_license_key'];
                        // API query parameters
                        $api_params = array(
                            'slm_action' => 'slm_activate',
                            'secret_key' => MAILCHIMP_SECRET_KEY,
                            'license_key' => $license_key,
                            'registered_domain' => $_SERVER['SERVER_NAME'],
                            'item_reference' => urlencode(MAILCHIMP_ITEM_REFERENCE),
                        );

                        // Send query to the license manager server
                        $query = esc_url_raw(add_query_arg($api_params, MAILCHIMP_LICENSE_SERVER_URL));
                        $response = wp_remote_get($query, array('timeout' => 20, 'sslverify' => false));
                        // Check for error in the response
                        if (is_wp_error($response)) {
                            softsdev_notice("Unexpected Error! The query returned with an error.", 'error');
                        }
                        //var_dump($response);//uncomment it if you want to look at the full response
                        // License data.
                        $license_data = json_decode(wp_remote_retrieve_body($response));

                        // TODO - Do something with it.
                        //var_dump($license_data);//uncomment it to look at the data                      
                        if ($license_data->result == 'success') {//Success was returned for the license activation
                            //Uncomment the followng line to see the message that returned from the license server
                            softsdev_notice('The following message was returned from the server: ' . $license_data->message . '. You must reload the page to see result!', 'updated');
                            //Save the license key in the options table
                            update_option('mailchimp_license_key', $license_key);
                        } else {
                            //Show error to the user. Probably entered incorrect license key.
                            //Uncomment the followng line to see the message that returned from the license server
                            softsdev_notice('The following message was returned from the server: ' . $license_data->message, 'error');
                        }
                    }
                    /*                     * * End of license activation ** */
                    /*                     * * License activate button was clicked ** */
                    if (isset($_REQUEST['deactivate_license'])) {
                        $license_key = $_REQUEST['mailchimp_license_key'];
                        // API query parameters
                        $api_params = array(
                            'slm_action' => 'slm_deactivate',
                            'secret_key' => MAILCHIMP_SECRET_KEY,
                            'license_key' => $license_key,
                            'registered_domain' => $_SERVER['SERVER_NAME'],
                            'item_reference' => urlencode(MAILCHIMP_ITEM_REFERENCE),
                        );
                        // Send query to the license manager server
                        $query = esc_url_raw(add_query_arg($api_params, MAILCHIMP_LICENSE_SERVER_URL));
                        $response = wp_remote_get($query, array('timeout' => 20, 'sslverify' => false));
                        // Check for error in the response
                        if (is_wp_error($response)) {
                            softsdev_notice("Unexpected Error! The query returned with an error.", 'error');
                        }
                        // License data.
                        $license_data = json_decode(wp_remote_retrieve_body($response));
                        // TODO - Do something with it.
                        if ($license_data->result == 'success') {//Success was returned for the license activation
                            //Uncomment the followng line to see the message that returned from the license server
                            softsdev_notice('The following message was returned from the server: ' . $license_data->message, 'updated');
                            //Remove the licensse key from the options table. It will need to be activated again.
                            update_option('mailchimp_license_key', '');
                        } else {
                            //Show error to the user. Probably entered incorrect license key.
                            //Uncomment the followng line to see the message that returned from the license server
                            softsdev_notice('The following message was returned from the server: ' . $license_data->message, 'error');
                        }
                    }
                    /*                     * * End of  license deactivation ** */
                    ?>               
                    <p><?php echo __('Please enter the license key for this product to activate it. You were given a license key when you purchased this item.', 'softsdev'); ?></p>
                    <form id="woo_dd_license" action="<?php echo $_SERVER['PHP_SELF'] . '?page=softsdev-mailchimp' ?>" method="post">
                        <table class="form-table">
                            <tr>
                                <th style="width:100px;"><label for="mailchimp_license_key"><?php echo __('License Key', 'softsdev'); ?></label></th>
                                <td ><input class="regular-text" type="text" id="mailchimp_license_key" name="mailchimp_license_key" value="<?php echo get_option('mailchimp_license_key'); ?>" ></td>
                            </tr>
                        </table>
                        <p class="submit">
                            <input type="submit" name="activate_license" value="Activate" class="button-primary" />
                            <input type="submit" name="deactivate_license" value="Deactivate" class="button" />
                        </p>
                    </form>
                </div>
            </div>
            <div class="right-mc-paid">
                <div style="border: 5px dashed #B0E0E6; padding: 0 20px; background: white;">
                    <!-- Begin MailChimp Signup Form -->
                    <link href="//cdn-images.mailchimp.com/embedcode/slim-081711.css" rel="stylesheet" type="text/css">
                    <style type="text/css">
                        #mc_embed_signup{/* background:#fff;  */clear:left; font:14px Helvetica,Arial,sans-serif; }
                        /* Add your own MailChimp form style overrides in your site stylesheet or in this style block.
                           We recommend moving this block and the preceding CSS link to the HEAD of your HTML file. */
                    </style>
                    <div id="mc_embed_signup" style="border-bottom:1px solid #ccc">
                        <form action="//dreamfoxmedia.us3.list-manage.com/subscribe/post?u=a0293a6a24c69115bd080594e&amp;id=131c5e0c11" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
                            <div id="mc_embed_signup_scroll">
                                <label for="mce-EMAIL">Subscribe to our mailing list</label>
                                <input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="email address" required>
                                <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                                <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_a0293a6a24c69115bd080594e_131c5e0c11" tabindex="-1" value=""></div>
                                <div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="mc4wp-box" >
                    <h4 class="mc4wp-title"><?php echo __('Looking for help?', 'mailchimp-for-wp'); ?></h4>
                    <p><?php echo __('We have some resources available to help you in the right direction.', 'mailchimp-for-wp'); ?></p>
                    <ul class="ul-square">
                        <li>
                            <a href="http://www.dreamfoxmedia.com/ufaq-category/wctmg-p/#utm_source=wp-plugin&utm_medium=wctmg&utm_campaign=helpbar"><?php echo __('Knowledge Base', 'mailchimp-for-wp'); ?></a>
                        </li>
                    </ul>
                    <p><?php echo sprintf(__('If your answer can not be found in the resources listed above, please use the <a href="%s">support forums on WordPress.org</a>.'), 'https://wordpress.org/support/plugin/woocommerce-mailchimp-plugin'); ?></p>
                </div>
            </div>
            <?php
        }

        /**
         * 
         * @param type $array
         * @param type $fields1
         * @param type $fields2
         * @return type
         */
        function softsdev_mc_list_data($array, $fields1, $fields2) {
            if (!is_array($array) || count($array) < 1)
                return array();
            $listData = array();
            foreach ($array as $key => $value) {
                $listData[$value[$fields1]] = $value[$fields2];
            }
            return $listData;
        }

        /**
         * Get Mailchimp Groups 
         */
        function softsdev_get_mc_groups() {
            if (!class_exists('SoftsdevMCAPI')) {
                require 'inc/softsdev_mcapi.class.php';
            }
            $softsdev_wc_mc_setting = get_softsdev_wc_mc_setting();
            if (isset($softsdev_wc_mc_setting['api']) && isset($softsdev_wc_mc_setting['list_id']) && $softsdev_wc_mc_setting['list_id'] != '') {
                $api = new SoftsdevMCAPI($softsdev_wc_mc_setting['api']);
                if ($api->ping()) {
                    $mgroups = $api->listInterestGroupings($softsdev_wc_mc_setting['list_id']);
                    foreach ($mgroups as $groups) {
                        if ($groups['id'] == $softsdev_wc_mc_setting['mgroup_id'])
                            return $groups['groups'];
                    }
                }else {
                    return array();
                }
            }
            return array();
        }

        /**
         * Add Extra colum to woocommerce product category
         * @return string
         */
        function softsdev_mc_product_cat_add_new_meta_field() {
            $softsdev_wc_mc_setting = get_softsdev_wc_mc_setting();
            if (!@$softsdev_wc_mc_setting['api'])
                return '';
            $groups = softsdev_get_mc_groups();
            // this will add the custom meta field to the add new term page
            ?>
            <tr class="form-field">
                <th scope="row" valign="top"><label for="softsdev_mc_group"><?php _e('Mailchimp Group', 'softsdev_mc'); ?></label></th>
                <td>
                    <?php
                    echo @woocommerce_wp_select(
                            array(
                                'value' => '',
                                'label' => '',
                                'id' => 'softsdev_mc_group',
                                'options' => array('1' => 'Select Group') + softsdev_mc_list_data($groups, 'name', 'name')
                            )
                    );
                    ?>
                    <p class="description"><?php _e('Select mailchimp group.', 'softsdev'); ?></p>
                </td>
            </tr>
            <?php
        }

        add_action('product_cat_add_form_fields', 'softsdev_mc_product_cat_add_new_meta_field', 10, 2);

        /**
         * Render Edit page of product category
         * @param type $term
         * @return string
         */
        function softsdev_mc_product_cat_edit_meta_field($term) {
            $softsdev_wc_mc_setting = get_softsdev_wc_mc_setting();
            if (!@$softsdev_wc_mc_setting['api'])
                return '';
            $groups = softsdev_get_mc_groups();
            // put the term ID into a variable
            $t_id = $term->term_id;
            // retrieve the existing value(s) for this meta field. This returns an array
            $softsdev_mc_terms_groups = get_option('softsdev_mc_term_group');
            ?>
            <tr class="form-field">
                <th scope="row" valign="top"><label for="softsdev_mc_group"><?php _e('Mailchimp Group', 'softsdev'); ?></label></th>
                <td>
                    <?php
                    echo @woocommerce_wp_select(
                            array(
                                'value' => @$softsdev_mc_terms_groups['term' . $softsdev_wc_mc_setting['list_mgroup_id'] . '_' . $t_id],
                                'id' => 'softsdev_mc_group',
                                'options' => array('' => 'Select Group') + softsdev_mc_list_data($groups, 'name', 'name')
                            )
                    );
                    ?>
                    <p class="description"><?php _e('Select mailchimp group.', 'softsdev'); ?></p>
                </td>
            </tr>
            <?php
        }

        add_action('product_cat_edit_form_fields', 'softsdev_mc_product_cat_edit_meta_field', 10, 2);

        /**
         * Save Custom field softsdev_mc_group got woocommerce product category
         * @param type $term_id
         */
        function save_softsdev_mc_product_cat_custom_meta($term_id) {
            if (isset($_POST['softsdev_mc_group'])) {
                $t_id = $term_id;
                $softsdev_wc_mc_setting = get_softsdev_wc_mc_setting();
                $softsdev_mc_terms_groups = get_option('softsdev_mc_term_group');
                $softsdev_mc_terms_groups['term' . $softsdev_wc_mc_setting['list_mgroup_id'] . '_' . $t_id] = $_POST['softsdev_mc_group'];
                // Save the option array.
                update_option(softsdev_mc_term_group, $softsdev_mc_terms_groups);
            }
        }

        add_action('edited_product_cat', 'save_softsdev_mc_product_cat_custom_meta', 10, 2);
        add_action('create_product_cat', 'save_softsdev_mc_product_cat_custom_meta', 10, 2);
        /*         * ************************************** */

        /**
         * 
         * @param type $setting
         * @return string
         */
        function get_softsdev_wc_mc_setting($setting = '') {
            if (!$setting) {
                $setting = get_option('softsdev_wc_mc_full_setting');
            }
            if (!$setting) {
                return array();
            }
            if ($setting['list_mgroup_id']) {
                list($setting['list_id'], $setting['mgroup_id']) = explode(':', $setting['list_mgroup_id']);
            } else {
                $setting['list_id'] = $setting['mgroup_id'] = '';
            }
            if (!isset($setting['event'])) {
                $setting['event'] = 'after_order_place';
            }
            return $setting;
        }

        /**
         * 
         */
        $softsdev_wc_mc_setting = get_softsdev_wc_mc_setting();
        if (@$softsdev_wc_mc_setting['event'] !== 'after_order_completion') {
            add_action('woocommerce_thankyou', 'softsdev_mc_subscribe', 20);
        } else {
            add_action('woocommerce_order_status_completed', 'softsdev_mc_subscribe');
        }

        /**
         * 
         * @param type $order_id
         * @return boolean|string
         */
        function softsdev_mc_subscribe($order_id) {
            $softsdev_wc_mc_setting = get_softsdev_wc_mc_setting();
            if (!@$softsdev_wc_mc_setting['api'])
                return '';
            $softsdev_mc_terms_groups = get_option('softsdev_mc_term_group');
            if (!$softsdev_mc_terms_groups)
                return false;
            // get order details
            $order = new WC_Order($order_id);
            // getting all products
            $products = $order->get_items();
            // define group variable
            $groups = array();
            // Collecting all group whose assign to product and category
            foreach ($products as $product) {
                // For product on priority
                if (get_post_meta($product['product_id'], 'softsdev_mc_group', true)) {
                    $groups[] = get_post_meta($product['product_id'], 'softsdev_mc_group', true);
                } else {
                    // Get terms of product
                    $terms = get_the_terms($product['product_id'], 'product_cat');
                    // getting all groups of term
                    foreach ($terms as $term) {
                        if (array_key_exists('term' . $softsdev_wc_mc_setting['list_mgroup_id'] . '_' . $term->term_id, $softsdev_mc_terms_groups)) {
                            $groups[] = $softsdev_mc_terms_groups['term' . $softsdev_wc_mc_setting['list_mgroup_id'] . '_' . $term->term_id];
                        }
                    }
                }
            }
            $double_opt_in = get_post_meta($order_id, 'double_opt_in', true);
            // subscribe to mailchimp
            softsdev_subscribe_to_mc($groups, $double_opt_in);
        }

        /**
         * 
         * @param type $groups
         * @param type $double_opt_in
         */
        function softsdev_subscribe_to_mc($groups, $double_opt_in) {
            // Check group count
            if (count($groups) > 0) {
                $softsdev_wc_mc_setting = get_softsdev_wc_mc_setting();
                if (!class_exists('SoftsdevMCAPI')) {
                    require 'inc/softsdev_mcapi.class.php';
                }
                $api = new SoftsdevMCAPI($softsdev_wc_mc_setting['api']);
                if (isset($softsdev_wc_mc_setting['list_id'])) {
                    // getting current user data
                    $current_user = wp_get_current_user();
                    $my_email = $current_user->user_email;
                    $merge_vars = Array(
                        'EMAIL' => $current_user->user_email,
                        'FNAME' => $current_user->user_firstname,
                        'LNAME' => $current_user->user_lastname,
                        'GROUPINGS' => /* implode(',', $groups) */array(
                            array('id' => $softsdev_wc_mc_setting['mgroup_id'], 'groups' => implode(',', $groups))
                        )
                    );
                    //send subscription to mailchimp 
                    if (isset($softsdev_wc_mc_setting['opt_in_on_checkout']) && $softsdev_wc_mc_setting['opt_in_on_checkout'] == 1) {
                        $double_optin = $double_opt_in;
                    } else {
                        $double_optin = ( isset($softsdev_wc_mc_setting['double_opt_in']) && $softsdev_wc_mc_setting['double_opt_in'] == 1 ) ? true : false;
                    }

                    $api->listSubscribe($softsdev_wc_mc_setting['list_id'], $my_email, $merge_vars, 'html', $double_optin, true, false);
                }
            }
        }

        /* ----------------------------Meta Box -------------------------- */
        add_action('add_meta_boxes', 'softsdev_product_mailchimp_meta_box_add', 60);

        /**
         * 
         */
        function softsdev_product_mailchimp_meta_box_add() {
            add_meta_box('softsdev-mailchimp-groups', 'Mailchimp Groups', 'softsdev_product_mailchimp_meta_form', 'product', 'side', 'core');
        }

        /**
         * 
         * @global type $post
         * @return string
         */
        function softsdev_product_mailchimp_meta_form() {
            global $post;
            $softsdev_wc_mc_setting = get_softsdev_wc_mc_setting();
            if (!@$softsdev_wc_mc_setting['api'])
                return '';
            $groups = softsdev_get_mc_groups();
            // this will add the custom meta field to the add new term page
            ?>
            <p><label for="softsdev_mc_group"><?php _e('Mailchimp Group', 'softsdev_mc'); ?></label></p>
            <?php
            echo @woocommerce_wp_select(
                    array(
                        'value' => get_post_meta($post->ID, 'softsdev_mc_group', true),
                        'id' => 'softsdev_mc_group',
                        'options' => array('' => 'Select Group') + softsdev_mc_list_data($groups, 'name', 'name')
                    )
            );
            ?>
            <p class="description"><?php _e('Select mailchimp group.', 'softsdev'); ?></p>
            <?php
        }

        /**
         * 
         */
        add_action('save_post', 'softsdev_product_mailchimp_meta_box_save', 10, 2);

        /**
         * 
         * @param type $post_id
         * @param type $post
         * @return type
         */
        function softsdev_product_mailchimp_meta_box_save($post_id, $post) {
            // Restrict to save for autosave
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
                return $post_id;
            // Restrict to save for revisions
            if (isset($post->post_type) && $post->post_type == 'revision')
                return $post_id;
            if (isset($_POST['post_type']) && $_POST['post_type'] == 'product') {
                // Check if product meta is
                if (isset($_POST['softsdev_mc_group']) && $_POST['softsdev_mc_group'] != '')
                    update_post_meta($post_id, 'softsdev_mc_group', $_POST['softsdev_mc_group']);
                else
                    delete_post_meta($post_id, 'softsdev_mc_group');
            }
        }

        /* ----------------------------END Meta Box------------------------- */
        add_filter('woocommerce_checkout_fields', 'custom_override_checkout_fields');

        // Our hooked in function - $fields is passed via the filter!
        /**
         * 
         * @param type $fields
         * @return string
         */
        function custom_override_checkout_fields($fields) {
            $softsdev_wc_mc_setting = get_softsdev_wc_mc_setting();
            if (array_key_exists('double_opt_in', $softsdev_wc_mc_setting) && $softsdev_wc_mc_setting['double_opt_in'] == 1 && array_key_exists('opt_in_on_checkout', $softsdev_wc_mc_setting) && $softsdev_wc_mc_setting['opt_in_on_checkout'] == 1) {
                $fields['billing']['double_opt_in'] = array(
                    'label' => __(isset($softsdev_wc_mc_setting['opt_in_on_checkout_label']) && !empty($softsdev_wc_mc_setting['opt_in_on_checkout_label']) ? $softsdev_wc_mc_setting['opt_in_on_checkout_label'] : 'Enable Double Opt-In', 'woocommerce'),
                    'type' => 'checkbox',
                    'default' => isset($softsdev_wc_mc_setting['checked_opt_in_on_checkout']) ? $softsdev_wc_mc_setting['checked_opt_in_on_checkout'] : 0,
                    'value' => '1',
                    'class' => array('form-row-wide'),
                    'clear' => true
                );
            }
            return $fields;
        }

        add_action('woocommerce_checkout_update_order_meta', 'actionWooCheckoutUpdateOrderMeta');

        /**
         * 
         * @param type $order_id
         */
        function actionWooCheckoutUpdateOrderMeta($order_id) {
            $subscribe = isset($_POST['double_opt_in']) ? 1 : 0;
            update_post_meta($order_id, 'double_opt_in', $subscribe);
        }

    }