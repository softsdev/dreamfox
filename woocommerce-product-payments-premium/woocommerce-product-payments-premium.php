<?php
/**
 * Plugin Name: Woocommerce Payment Gateway Per Product Premium
 * Plugin URI: http://www.dreamfoxmedia.com/project/woocommerce-payment-gateway-per-product-premium/ 
 * Version: 1.2.6
 * Author: Dreamfox Media
 * Author URI: www.dreamfoxmedia.com 
 * Description: Extend Woocommerce plugin to add payments methods to a product
 * Requires at least: 3.7
 * Tested up to: 4.6.1
 * @Developer : Hoang Xuan Hao / Marco van Loghum Slaterus ( Dreamfoxmedia )
 */
define('PRODUCT_PAYMENTS_SECRET_KEY', '568ef1d445c081.84974482'); //Rename this constant name so it is specific to your plugin or theme.
// This is the URL where API query request will be sent to. This should be the URL of the site where you have installed the main license manager plugin. Get this value from the integration help page.
define('PRODUCT_PAYMENTS_LICENSE_SERVER_URL', 'http://www.dreamfoxmedia.com'); //Rename this constant name so it is specific to your plugin or theme.
// This is a value that will be recorded in the license manager data so you can identify licenses for this item/product.
define('PRODUCT_PAYMENTS_ITEM_REFERENCE', 'Product Payments'); //Rename this constant name so it is specific to your plugin or theme.
/**
 * Check if WooCommerce is active
 */
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) && !function_exists('softsdev_product_payments_settings')) {
    /* ----------------------------------------------------- */
    // update checker
    require_once 'plugin-update-checker/plugin-update-checker.php';
    $MyUpdateChecker = PucFactory::buildUpdateChecker(
                    'http://www.dreamfoxmedia.com/update-plugins/?action=get_metadata&slug=woocommerce-product-payments-premium', //Metadata URL.
                    __FILE__, //Full path to the main plugin file.
                    'woocommerce-product-payments-premium' //Plugin slug. Usually it's the same as the name of the directory.
    );
    // Submenu on woocommerce section
    add_action('admin_menu', 'softsdev_product_payments_submenu_page');
    /* ----------------------------------------------------- */
    add_action('admin_enqueue_scripts', 'softsdev_product_payments_enqueue');
    /* ----------------------------------------------------- */

    function softsdev_product_payments_submenu_page() {
        add_submenu_page('woocommerce', __('Product Payment', 'softsdev'), __('Product Payment', 'softsdev'), 'manage_options', 'softsdev-product-payments', 'softsdev_product_payments_settings');
    }

    function softsdev_product_payments_enqueue() {
        wp_enqueue_style('softsdev_product_payments_enqueue', plugin_dir_url(__FILE__) . '/css/style.css');
    }

    /**
     * Check license
     * @return true / false
     */
    function softsdev_product_payments_has_valid_license() {
        $license_key = get_option('product_payments_license_key');
        $api_params = array(
            'slm_action' => 'slm_check',
            'secret_key' => PRODUCT_PAYMENTS_SECRET_KEY,
            'license_key' => $license_key,
        );
        // Send query to the license manager server
        $query = esc_url_raw(add_query_arg($api_params, PRODUCT_PAYMENTS_LICENSE_SERVER_URL));
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

    function softsdev_product_payments_footer_text($text) {
        if (!empty($_GET['page']) && strpos($_GET['page'], 'softsdev-product-payments') === 0) {
            $text = '<a href="http://www.dreamfoxmedia.com" target="_blank">www.dreamfoxmedia.com</a>';
        }
        return $text;
    }

    function softsdev_product_payments_update_footer($text) {
        if (!empty($_GET['page']) && strpos($_GET['page'], 'softsdev-product-payments') === 0) {
            $text = 'Version 1.2.6';
        }
        return $text;
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

    function softsdev_product_payments_settings() {
        add_filter('admin_footer_text', 'softsdev_product_payments_footer_text');
        add_filter('update_footer', 'softsdev_product_payments_update_footer');
        echo '<div class="wrap wrap-mc-paid"><div id="icon-tools" class="icon32"></div>';
        echo '<h2 class="title">' . __('Woocommerce Product Payments', 'softsdev') . '</h2>';
        ?>
        <div class="left-mc-paid">
        <?php if (softsdev_product_payments_has_valid_license()): ?>
                <form id="woo_dd" action="<?php echo $_SERVER['PHP_SELF'] . '?page=softsdev-product-payments' ?>" method="post">
                    <div class="postbox " style="padding:10px 10px 10px 0; margin: 10px 0px;">
                        <h3 class="hndle"><?php echo __('Product payment Setting', 'softsdev'); ?></h3>
                    </div> 
                    <p>This plugin for WooCommerce Payment Gateway per Product lets you select the available payment method for each individual product.
                        This plugin will allow the admin to select the available payment gateway for each individual product.
                        Admin can select for each individual product the payment gateway that will be used by checkout. If no selection is made, then the default payment gateways are displayed.
                        If you for example only select paypal then only paypal will available for that product by checking out.</p>            
                <!-- <input class="button-large button-primary" type="submit" value="Save Changes" /> -->
                </form>
                <?php softsdev_sdwpp_plugin_settings();?>     
                <div class="license-key-new">
                    <?php else: ?>
                    <div class="license-key">
                    <?php endif; ?>            
                    <?php
                    /*                     * * License activate button was clicked ** */
                    if (isset($_REQUEST['activate_license'])) {
                        $license_key = $_REQUEST['product_payments_license_key'];
                        // API query parameters
                        $api_params = array(
                            'slm_action' => 'slm_activate',
                            'secret_key' => PRODUCT_PAYMENTS_SECRET_KEY,
                            'license_key' => $license_key,
                            'registered_domain' => $_SERVER['SERVER_NAME'],
                            'item_reference' => urlencode(PRODUCT_PAYMENTS_ITEM_REFERENCE),
                        );
                        // Send query to the license manager server
                        $query = esc_url_raw(add_query_arg($api_params, PRODUCT_PAYMENTS_LICENSE_SERVER_URL));
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
                            update_option('product_payments_license_key', $license_key);
                        } else {
                            //Show error to the user. Probably entered incorrect license key.
                            //Uncomment the followng line to see the message that returned from the license server
                            softsdev_notice('The following message was returned from the server: ' . $license_data->message, 'error');
                        }
                    }
                    /*                     * * End of license activation ** */
                    /*                     * * License activate button was clicked ** */
                    if (isset($_REQUEST['deactivate_license'])) {
                        $license_key = $_REQUEST['product_payments_license_key'];
                        // API query parameters
                        $api_params = array(
                            'slm_action' => 'slm_deactivate',
                            'secret_key' => PRODUCT_PAYMENTS_SECRET_KEY,
                            'license_key' => $license_key,
                            'registered_domain' => $_SERVER['SERVER_NAME'],
                            'item_reference' => urlencode(PRODUCT_PAYMENTS_ITEM_REFERENCE),
                        );
                        // Send query to the license manager server
                        $query = esc_url_raw(add_query_arg($api_params, PRODUCT_PAYMENTS_LICENSE_SERVER_URL));
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
                            update_option('product_payments_license_key', '');
                        } else {
                            //Show error to the user. Probably entered incorrect license key.
                            //Uncomment the followng line to see the message that returned from the license server
                            softsdev_notice('The following message was returned from the server: ' . $license_data->message, 'error');
                        }
                    }
                    /*                     * * End of  license deactivation ** */
                    ?>               
                    <p>Please enter the license key for this product to activate it. You were given a license key when you purchased this item.</p>
                    <form id="woo_dd_license" action="<?php echo $_SERVER['PHP_SELF'] . '?page=softsdev-product-payments' ?>" method="post">
                        <table class="form-table">
                            <tr>
                                <th style="width:100px;"><label for="product_payments_license_key">License Key</label></th>
                                <td ><input class="regular-text" type="text" id="product_payments_license_key" name="product_payments_license_key" value="<?php echo get_option('product_payments_license_key'); ?>" ></td>
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
                    <h4 class="mc4wp-title"><?php echo __('Looking for help?', 'Woocommerce Payment Gateway Per Product'); ?></h4>
                    <p><?php echo __('We have some resources available to help you in the right direction.', 'Woocommerce Payment Gateway Per Product'); ?></p>
                    <ul class="ul-square">
                        <li><a href="http://www.dreamfoxmedia.com/ufaq-category/wcpgpp-p/#utm_source=wp-plugin&utm_medium=wcpgpp&utm_campaign=helpbar"><?php echo __('Knowledge Base', 'Woocommerce Payment Gateway Per Product'); ?></a></li>
                    </ul>
                    <p><?php echo sprintf(__('If your answer can not be found in the resources listed above, please use our supportsystem <a href="%s">here</a>.'), 'http://support.dreamfoxmedia.com'); ?></p>
                </div>
                <?php
            }

            /**
             * Setting form of product paayment full category
             */
            if (softsdev_product_payments_has_valid_license()) {
                add_action('add_meta_boxes', 'wpp_meta_box_add');
            }

            // add_action('add_meta_boxes', 'wpp_meta_box_add');
            /**
             * 
             */
            function wpp_meta_box_add() {
                add_meta_box('payments', 'Payments', 'wpp_payments_form', 'product', 'side', 'core');
            }

            /**
             * 
             * @global type $post
             * @global WC_Payment_Gateways $woo
             */
            function wpp_payments_form() {
                global $post, $woo;
                $productIds = get_option('woocommerce_product_apply', array());
                if (is_array($productIds)) {
                    foreach ($productIds as $key => $product) {
                        if (!get_post($product) || !count(get_post_meta($product, 'payments', true))) {
                            unset($productIds[$key]);
                        }
                    }
                }
                update_option('woocommerce_product_apply', $productIds);
                $postPayments = (get_post_meta($post->ID, 'payments', true)) ? get_post_meta($post->ID, 'payments', true) : array();
                $woo = new WC_Payment_Gateways();
                //$payments = $woo->get_available_payment_gateways();
                $payments = $woo->payment_gateways;
                foreach ($payments as $pay) {
                    if (apply_filters('softsdev_show_disabled_gateways', false) || $pay->enabled == 'no') {
                        continue;
                    }
                    $checked = '';
                    if (is_array($postPayments) && in_array($pay->id, $postPayments)) {
                        $checked = ' checked="yes" ';
                    }
                    ?>  
                    <input type="checkbox" <?php echo $checked; ?> value="<?php echo $pay->id; ?>" name="pays[]" id="payment_<?php echo $pay->id; ?>" />
                    <label for="payment_<?php echo $pay->id; ?>"><?php echo $pay->title; ?></label>  
                    <br />  
                    <?php
                }
            }

            add_action('save_post', 'wpp_meta_box_save', 10, 2);

            /**
             * 
             * @param type $post_id
             * @param type $post
             * @return type
             */
            function wpp_meta_box_save($post_id, $post) {
                // Restrict to save for autosave
                if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                    return $post_id;
                }
                // Restrict to save for revisions
                if (isset($post->post_type) && $post->post_type == 'revision') {
                    return $post_id;
                }
                if (isset($_POST['post_type']) && $_POST['post_type'] == 'product' && isset($_POST['pays'])) {
                    $productIds = get_option('woocommerce_product_apply', array());
                    if (is_array($productIds) && !in_array($post_id, $productIds)) {
                        $productIds[] = $post_id;
                        update_option('woocommerce_product_apply', $productIds);
                    }
                    //delete_post_meta($post_id, 'payments');        
                    $payments = array();
                    if ($_POST['pays']) {
                        foreach ($_POST['pays'] as $pay) {
                            $payments[] = $pay;
                        }
                    }
                    update_post_meta($post_id, 'payments', $payments);
                } elseif (isset($_POST['post_type']) && $_POST['post_type'] == 'product') {
                    update_post_meta($post_id, 'payments', array());
                }
            }

            /**
             *
             * 
             * 
             * @global type $woocommerce
             * @param type $available_gateways
             * @return type
             */
            function wpppayment_gateway_disable_country($available_gateways) {
                if (!softsdev_product_payments_has_valid_license()){
                    return $available_gateways;
                }

                global $woocommerce;
                $arrayKeys = array_keys($available_gateways);
                /**
                 * default setting
                 */
                $softsdev_wpp_plugin_settings = get_option('sdwpp_plugin_settings', array('default_payment'=>''));
                $default_payment = $softsdev_wpp_plugin_settings['default_payment'];
                $is_default_pay_needed = false;
                /**
                 * checking all cart products
                 */                
                if (count($woocommerce->cart)) {
                    $items = $woocommerce->cart->cart_contents;
                    $itemsPays = '';
                    if (is_array($items)) {
                        foreach ($items as $item) {
                            $itemsPays = get_post_meta($item['product_id'], 'payments', true);
                            if (is_array($itemsPays) && count($itemsPays)) {
                                foreach ($arrayKeys as $key) {
                                    if (array_key_exists($key, $available_gateways) && !in_array($available_gateways[$key]->id, $itemsPays)) {
                                        if( $default_payment == $key ){
                                                $is_default_pay_needed = true;
                                                $default_payment_obj = $available_gateways[$key];
                                                unset($available_gateways[$key]);
                                        }else{
                                                unset($available_gateways[$key]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    /**
                     * set default payment if there is none
                     */
                    if( $is_default_pay_needed && count($available_gateways) == 0 ){
                        $available_gateways[$default_payment] = $default_payment_obj;
                    }                      
                }              
                return $available_gateways;
            }

            add_filter('woocommerce_available_payment_gateways', 'wpppayment_gateway_disable_country');
     
            /**
              * Setting form
              */
             function softsdev_sdwpp_plugin_settings() {
                 /**
                  * Settings default
                  */
                 if (isset($_POST['sdwpp_setting'])) {
                     update_option('sdwpp_plugin_settings', $_POST['sdwpp_setting']);
                     softsdev_notice('Woocommerce Payment Gateway per Product setting is updated.', 'updated');
                 }
                 $softsdev_wpp_plugin_settings = get_option('sdwpp_plugin_settings', array('default_payment'=>''));
                 $default_payment = $softsdev_wpp_plugin_settings['default_payment'];
                 ?>
                 <form id="woo_sdwpp" action="<?php echo $_SERVER['PHP_SELF'] . '?page=softsdev-product-payments' ?>" method="post">
                     <div class="postbox " style="padding: 10px 0; margin: 10px 0px;">
                         <h3 class="hndle"><?php echo __('Default Payment option( If not match any.)', 'softsdev'); ?></h3>
                         <?php
                         $woo = new WC_Payment_Gateways();
                         $payments = $woo->payment_gateways;
                         ?>
                         <select id="sdwpp_default_payment" name="sdwpp_setting[default_payment]">
                                  <option value="">None</option>
                                     <?php
                                     foreach ($payments as $pay) {
                                         /**
                                          *  skip if payment in disbled from admin
                                          */
                                         if ($pay->enabled == 'no') {
                                             continue;
                                         }
                                         echo "<option value = '" . $pay->id . "' ".selected( $default_payment, $pay->id ).">" . $pay->title . "</option>";
                                     }
                                     ?>
                         </select>
                         <br />
                         <small><?php echo __('If in some case payment option not show then this will default one set', 'softsdev'); ?></small>
                     </div>
                     <input class="button-large button-primary" type="submit" value="Save changes" />
                 </form>  <?php
             }            
}
   
        ?>