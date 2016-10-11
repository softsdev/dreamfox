<?php
/**
 * Plugin Name: Woocommerce Product Shippings Full
 * Plugin URI: www.dreamfox.nl 
 * Version: 1.2.4
 * Author: Marco van Loghum
 * Author URI: www.dreamfox.nl 
 * Description: Extend Woocommerce plugin to add shipping methods to a product
 * Requires at least: 3.5
 * Tested up to: 4.6.1
 * @developer Softsdev <mail.softsdev@gmail.com>
 */
define('PRODUCT_SHIPPINGS_SECRET_KEY', '568ef1d445c081.84974482'); //Rename this constant name so it is specific to your plugin or theme.
// This is the URL where API query request will be sent to. This should be the URL of the site where you have installed the main license manager plugin. Get this value from the integration help page.
define('PRODUCT_SHIPPINGS_LICENSE_SERVER_URL', 'http://www.dreamfoxmedia.com'); //Rename this constant name so it is specific to your plugin or theme.
// This is a value that will be recorded in the license manager data so you can identify licenses for this item/product.
define('PRODUCT_SHIPPINGS_ITEM_REFERENCE', 'Product Shippings'); //Rename this constant name so it is specific to your plugin or theme.
/**
 * Check if WooCommerce is active
 */
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) && !function_exists('softsdev_product_shippings_settings')) {
    /* ----------------------------------------------------- */
// update checker
    require_once 'plugin-update-checker/plugin-update-checker.php';
    $MyUpdateChecker = PucFactory::buildUpdateChecker(
                    'http://www.dreamfoxmedia.com/update-plugins/?action=get_metadata&slug=woocommerce-product-shippings-full', //Metadata URL.
                    __FILE__, //Full path to the main plugin file.
                    'woocommerce-product-shippings-full' //Plugin slug. Usually it's the same as the name of the directory.
    );
// Submenu on woocommerce section
    add_action('admin_menu', 'softsdev_product_shippings_submenu_page');
    /* ----------------------------------------------------- */
    add_action('admin_enqueue_scripts', 'softsdev_product_shippings_enqueue');
    /* ----------------------------------------------------- */

    /**
     * 
     */
    function softsdev_product_shippings_submenu_page() {
        add_submenu_page('woocommerce', __('Product Shippings', 'softsdev'), __('Product Shippings', 'softsdev'), 'manage_options', 'softsdev-product-shippings', 'softsdev_product_shippings_settings');
    }

    /**
     * 
     */
    function softsdev_product_shippings_enqueue() {
        wp_enqueue_style('softsdev_product_shippings_enqueue', plugin_dir_url(__FILE__) . '/css/style.css');
    }

    /**
     * Check license
     * @return true / false
     */
    function softsdev_product_shippings_has_valid_license() {
        $license_key = get_option('product_shippings_license_key');
        $api_params = array(
            'slm_action' => 'slm_check',
            'secret_key' => PRODUCT_SHIPPINGS_SECRET_KEY,
            'license_key' => $license_key,
        );
// Send query to the license manager server
        $query = esc_url_raw(add_query_arg($api_params, PRODUCT_SHIPPINGS_LICENSE_SERVER_URL));
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
     * 
     * @param string $text
     * @return string
     */
    function softsdev_product_shippings_footer_text($text) {
        if (!empty($_GET['page']) && strpos($_GET['page'], 'softsdev-product-shippings') === 0) {
            $text = '<a href="http://www.dreamfoxmedia.com" target="_blank">www.dreamfoxmedia.com</a>';
        }
        return $text;
    }

    /**
     * 
     * @param string $text
     * @return string
     */
    function softsdev_product_shippings_update_footer($text) {
        if (!empty($_GET['page']) && strpos($_GET['page'], 'softsdev-product-shippings') === 0) {
            $text = 'Version 1.2.4';
        }
        return $text;
    }

    /**
     * 
     */
    function softsdev_product_shippings_settings() {
        add_filter('admin_footer_text', 'softsdev_product_shippings_footer_text');
        add_filter('update_footer', 'softsdev_product_shippings_update_footer');
        echo '<div class="wrap wrap-mc-paid"><div id="icon-tools" class="icon32"></div>';
        echo '<h2 class="title">' . __('Woocommerce Product Shippings', 'softsdev') . '</h2>';
        ?>
        <div class="left-mc-paid">
            <?php if (softsdev_product_shippings_has_valid_license()): ?>
                <form id="woo_dd" action="<?php echo $_SERVER['PHP_SELF'] . '?page=softsdev-product-shippings' ?>" method="post">
                    <div class="postbox " style="padding: 10px; margin: 10px 0px;">
                        <h3 class="hndle"><?php echo __('Product payment Setting', 'softsdev'); ?></h3>
                    </div> 
                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
                    <p>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>             
                    <!-- <input class="button-large button-primary" type="submit" value="Save Changes" /> -->
                </form>
                <div class="license-key-new">
                <?php else: ?>
                    <div class="license-key">
                    <?php endif; ?>            
                    <?php
                    /*                     * * License activate button was clicked ** */
                    if (isset($_REQUEST['activate_license'])) {
                        $license_key = $_REQUEST['product_shippings_license_key'];
                        // API query parameters
                        $api_params = array(
                            'slm_action' => 'slm_activate',
                            'secret_key' => PRODUCT_SHIPPINGS_SECRET_KEY,
                            'license_key' => $license_key,
                            'registered_domain' => $_SERVER['SERVER_NAME'],
                            'item_reference' => urlencode(PRODUCT_SHIPPINGS_ITEM_REFERENCE),
                        );
                        // Send query to the license manager server
                        $query = esc_url_raw(add_query_arg($api_params, PRODUCT_SHIPPINGS_LICENSE_SERVER_URL));
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
                            update_option('product_shippings_license_key', $license_key);
                        } else {
                            //Show error to the user. Probably entered incorrect license key.
                            //Uncomment the followng line to see the message that returned from the license server
                            softsdev_notice('The following message was returned from the server: ' . $license_data->message, 'error');
                        }
                    }
                    /* End of license activation */
                    /* License activate button was clicked */
                    if (isset($_REQUEST['deactivate_license'])) {
                        $license_key = $_REQUEST['product_shippings_license_key'];
                        // API query parameters
                        $api_params = array(
                            'slm_action' => 'slm_deactivate',
                            'secret_key' => PRODUCT_SHIPPINGS_SECRET_KEY,
                            'license_key' => $license_key,
                            'registered_domain' => $_SERVER['SERVER_NAME'],
                            'item_reference' => urlencode(PRODUCT_SHIPPINGS_ITEM_REFERENCE),
                        );
                        // Send query to the license manager server
                        $query = esc_url_raw(add_query_arg($api_params, PRODUCT_SHIPPINGS_LICENSE_SERVER_URL));
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
                            update_option('product_shippings_license_key', '');
                        } else {
                            //Show error to the user. Probably entered incorrect license key.
                            //Uncomment the followng line to see the message that returned from the license server
                            softsdev_notice('The following message was returned from the server: ' . $license_data->message, 'error');
                        }
                    }
                    /*                     * * End of  license deactivation ** */
                    ?>               
                    <p>Please enter the license key for this product to activate it. You were given a license key when you purchased this item.</p>
                    <form id="woo_dd_license" action="<?php echo $_SERVER['PHP_SELF'] . '?page=softsdev-product-shippings' ?>" method="post">
                        <table class="form-table">
                            <tr>
                                <th style="width:100px;"><label for="product_shippings_license_key">License Key</label></th>
                                <td ><input class="regular-text" type="text" id="product_shippings_license_key" name="product_shippings_license_key" value="<?php echo get_option('product_shippings_license_key'); ?>" ></td>
                            </tr>
                        </table>
                        <p class="submit">
                            <input type="submit" name="activate_license" value="Activate" class="button-primary" />
                            <input type="submit" name="deactivate_license" value="Deactivate" class="button" />
                        </p>
                    </form>
                </div>
                <?php softsdev_sdwps_plugin_settings(); ?>
            </div>
            <div class="right-mc-paid">
                <div style="border: 5px dashed #B0E0E6; padding: 0 20px; background: white;">
                    <!-- Begin MailChimp Signup Form -->
                    <link href="//cdn-images.mailchimp.com/embedcode/slim-081711.css" rel="stylesheet" type="text/css">
                    <style type="text/css">
                        #mc_embed_signup{/* background:#fff;  */clear:left; font:14px Helvetica,Arial,sans-serif; }
                        /* Add your own MailChimp form style overrides in your site stylesheet or in this style block
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
                    <p><?php echo sprintf(__('If your answer can not be found in the resources listed above, please use the <a href="%s">support forums on WordPress.org</a>.'), 'https://wordpress.org/support/plugin/woocommerce-shipping-gateway-per-product'); ?></p>
                </div>
            </div>
            <?php
        }

        /**
         * 
         */
        if (softsdev_product_shippings_has_valid_license()) {
            add_action('add_meta_boxes', 'wps_ship_meta_box_add', 50);
        }

        // add_action('add_meta_boxes', 'wps_ship_meta_box_add', 50);
        function wps_ship_meta_box_add() {
            add_meta_box('shippings', 'shippings', 'wps_shipping_form', 'product', 'side', 'core');
        }

        /**
         * 
         * @global type $post
         * @global type $woocommerce
         */
        function wps_shipping_form() {
            global $post, $woocommerce;
            $productIds = get_option('woocommerce_product_apply_ship', array());
            $postshippings = count(get_post_meta($post->ID, 'shippings', true)) ? get_post_meta($post->ID, 'shippings', true) : array();
            if (is_array($productIds)) {
                foreach ($productIds as $key => $product) {
                    if (!get_post($product) || !count(get_post_meta($product, 'shippings', true)))
                        unset($productIds[$key]);
                }
            }
            update_option('woocommerce_product_apply_ship', $productIds);
            $productIds = get_option('woocommerce_product_apply_ship', array());
            if ($woocommerce->shipping->load_shipping_methods())
                foreach ($woocommerce->shipping->load_shipping_methods() as $key => $method) {
                    if (apply_filters('softsdev_show_disabled_shippings', false) || $method->enabled == 'yes')
                        $shippings[$key] = $method;
                }
            foreach ($shippings as $ship) {
                if ($ship->enabled == 'yes') {
                    $checked = '';
                    if (is_array($postshippings) && in_array($ship->id, $postshippings))
                        $checked = ' checked="checked" ';
                    ?>  
                    <input type="checkbox" <?php echo $checked; ?> value="<?php echo $ship->id; ?>" name="ship[]" id="ship_<?php echo $ship->id ?>" />
                    <label for="ship_<?php echo $ship->id ?>"><?php echo $ship->method_title; ?></label>  
                    <br />  
                    <?php
                }
            }
        }

        add_action('save_post', 'wps_ship_meta_box_save', 10, 2);

        /**
         * 
         * @param type $post_id
         * @param type $post
         * @return type
         */
        function wps_ship_meta_box_save($post_id, $post) {
            // Restrict to save for autosave
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return $post_id;
            }
            // Restrict to save for revisions
            if (isset($post->post_type) && $post->post_type == 'revision') {
                return $post_id;
            }
            if (isset($_POST['post_type']) && $_POST['post_type'] == 'product' && isset($_POST['ship'])) {
                $productIds = get_option('woocommerce_product_apply_ship');
                if (is_array($productIds) && !in_array($post_id, $productIds)) {
                    $productIds[] = $post_id;
                    update_option('woocommerce_product_apply_ship', $productIds);
                }
                //delete_post_meta($post_id, 'shippings');
                $shippings = array();
                if ($_POST['ship']) {
                    foreach ($_POST['ship'] as $ship) {
                        $shippings[] = $ship;
                    }
                }
                if (count($shippings))
                    update_post_meta($post_id, 'shippings', $shippings);
                else
                    delete_post_meta($post_id, 'shippings');
            }
        }

        /**
         * 
         * @global type $woocommerce
         * @param type $available_methods
         * @return type
         */
        function wps_shipping_method_disable_country($available_methods) {
            global $woocommerce;
            if (!softsdev_product_shippings_has_valid_license()) {
                return $available_methods;
            }
            if (count($woocommerce->cart)) {
                $items = $woocommerce->cart->cart_contents;
                $itemsShips = '';
                $ships = array();
                if (is_array($items)) {
                    foreach ($items as $item) {
                        $itemsShips = get_post_meta($item['product_id'], 'shippings', true);
                        if (!empty($itemsShips) && count($itemsShips)) {
                            $ships[] = $itemsShips;
                        }
                    }
                }
            }
            /**
             * filter criteria to refresh methods
             */
            if (count($ships) > 0) {
                foreach ($woocommerce->shipping->load_shipping_methods() as $shipping_method) {
                    $_ships[] = $shipping_method->id;
                }
                $ships[] = $_ships;
                #If shipping is selected
                $filtered_ship = call_user_func_array('array_intersect', $ships);
                if (count($filtered_ship) > 0) {
                    #if common ship founds
                    /**
                     * logic for set common shipping
                     */
                    foreach ($available_methods as $key => $shipping) {
                        if (!in_array($shipping->method_id, $filtered_ship)) {
                            unset($available_methods[$key]);
                        }
                    }
                } else {
                    #if common ship not found
                    /**
                     * logic for default ship
                     * min max
                     */
                    $cost_rate = array();
                    foreach ($available_methods as $key => $shipping) {
                        $cost_rate[$key] = $shipping->cost;
                    }
                    $softsdev_wps_plugin_settings = get_option('sdwps_plugin_settings', array('default_option_mp' => 'expensive'));
                    switch ($softsdev_wps_plugin_settings['default_option_mp']) {
                        case 'expensive':
                            $aplicable_shipping = array_keys($cost_rate, max($cost_rate));
                            break;
                        case 'cheapest':
                            $aplicable_shipping = array_keys($cost_rate, min($cost_rate));
                            break;
                        default:
                            $aplicable_shipping = array();
                            break;
                    }
                    /**
                     * remove non aplicable shipping
                     */
                    foreach ($available_methods as $_key => $_shipping) {
                        if (!in_array($_key, $aplicable_shipping)) {
                            unset($available_methods[$_key]);
                        }
                    }
                }
            }
            return $available_methods;
        }

        // update new filter as depricated woocommerce_available_shipping_methods
        add_filter('woocommerce_package_rates', 'wps_shipping_method_disable_country', 99);
        add_filter('softsdev_show_disabled_shippings', function() {
            return true;
        });

        /**
         * 
         */
        function update_user_database() {
            $is_shipping_updated = get_option('is_shipping_updated');
            if (!$is_shipping_updated) {
                $args = array(
                    'posts_per_page' => -1,
                    'post_type' => 'product',
                    'fields' => 'ids'
                );
                $products = get_posts($args);
                foreach ($products as $pro_id) {
                    $itemsShips = get_post_meta($pro_id, 'shippings', true);
                    if (empty($itemsShips)) {
                        delete_post_meta($pro_id, 'shippings');
                    }
                }
                update_option('is_shipping_updated', true);
            }
        }

        add_action('wp_head', 'update_user_database');

        /**
         * Setting form
         */
        function softsdev_sdwps_plugin_settings() {
            /**
             * Settings default
             */
            if (isset($_POST['sdwps_setting'])) {
                update_option('sdwps_plugin_settings', $_POST['sdwps_setting']);
                softsdev_notice('Product Shippings setting is updated.', 'updated');
            }
            $softsdev_wps_plugin_settings = get_option('sdwps_plugin_settings', array('default_option_mp' => 'expensive'));
            $default_option_mp = $softsdev_wps_plugin_settings['default_option_mp'];
            ?>
            <form id="woo_sdwps" action="<?php echo $_SERVER['PHP_SELF'] . '?page=softsdev-product-shippings' ?>" method="post">
                <div class="postbox " style="padding: 10px 0; margin: 10px 0px;">
                    <h3 class="hndle"><?php echo __('multiple products in cart with different shipping gateway', 'softsdev'); ?></h3>
                    <select id="sdwps_default_payment" name="sdwps_setting[default_option_mp]">
                        <option value="none" <?php selected($default_option_mp, 'none') ?>>Do not show shipping gateway</option>
                        <option value="cheapest" <?php selected($default_option_mp, 'cheapest') ?>>Choose the cheapest gateway</option>
                        <option value="expensive" <?php selected($default_option_mp, 'expensive') ?>>Choose the expensive gateway</option>
                    </select>
                    <br />
                    <small><?php echo __('In case of multiple products from diffrent shipping', 'softsdev'); ?></small>
                </div>
                <input class="button-large button-primary" type="submit" value="Save changes" />
            </form>  <?php
        }

    }
    ?>