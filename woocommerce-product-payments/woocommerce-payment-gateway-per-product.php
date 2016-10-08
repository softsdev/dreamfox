<?php
/**
 * Plugin Name: Woocommerce Payment Gateway Per Product
 * Plugin URI: http://www.dreamfoxmedia.com/project/woocommerce-payment-gateway-per-product-premium/ 
 * Version: 1.2.6
 * Author: Dreamfox Media
 * Author URI: www.dreamfoxmedia.com 
 * Description: Extend Woocommerce plugin to add payments methods to a product
 * Requires at least: 3.7
 * Tested up to: 4.6.1
 * @Developer : Hoang Xuan Hao / Marco van Loghum Slaterus ( Dreamfoxmedia )
 */
//require_once ABSPATH . WPINC . '/pluggable.php';;
//require_once dirname(dirname(__FILE__)).'/woocommerce/classes/class-wc-payment-gateways.php';
//require_once dirname(dirname(__FILE__)).'/woocommerce/classes/class-wc-cart.php';
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    add_action('admin_enqueue_scripts', 'softsdev_product_payments_enqueue');
    add_action('admin_menu', 'softdev_product_payments_submenu_page');

    function softsdev_product_payments_enqueue() {
        wp_enqueue_style('softsdev_pd_payments_enqueue', plugin_dir_url(__FILE__) . '/css/style.css');
    }

    function softdev_product_payments_submenu_page() {
        add_submenu_page('woocommerce', __('Product Payments', 'softsdev'), __('Product Payments', 'softsdev'), 'manage_options', 'softsdev-product-payments', 'softsdev_product_payments_settings');
    }

    function softsdev_product_payments_footer_text($text) {
        if (!empty($_GET['page']) && strpos($_GET['page'], 'softsdev-product-payments') === 0) {
            $text = sprintf('If you enjoy using <strong>Woocommerce Payments Gateway per Product</strong>, please <a href="%s" target="_blank">leave us a ★★★★★ rating</a>. A <strong style="text-decoration: underline;">huge</strong> thank you in advance!', 'https://wordpress.org/support/view/plugin-reviews/woocommerce-product-payments');
        }
        return $text;
    }

    function softdev_product_payments_update_footer($text) {
        if (!empty($_GET['page']) && strpos($_GET['page'], 'softsdev-product-payments') === 0) {
            $text = 'Version 1.2.6';
        }
        return $text;
    }

    function softsdev_product_payments_settings() {
        add_filter('admin_footer_text', 'softsdev_product_payments_footer_text');
        add_filter('update_footer', 'softdev_product_payments_update_footer');
        ?>
        <?php
        echo '<div class="wrap "><div id="icon-tools" class="icon32"></div>';
        echo '<h2 style="padding-bottom:15px; margin-bottom:20px; border-bottom:1px solid #ccc">' . __('Woocommerce Payment Gateway per Product', 'softsdev') . '</h2>';
        ?>
        <div class="left-mc-setting">
            <p>This plugin for WooCommerce Payment Gateway per Product lets you select the available payment method for each individual product.
                This plugin will allow the admin to select the available payment gateway for each individual product.
                Admin can select for each individual product the payment gateway that will be used by checkout. If no selection is made, then the default payment gateways are displayed.
                If you for example only select paypal then only paypal will available for that product by checking out.</p>
            <p>This version is limited in features (you can only select gateways for 10 products). For a small fee you can get the Premium version with <a href="http://www.dreamfoxmedia.com/shop/woocommerce-payment-gateway-per-product-premium/#utm_source=plugin&utm_medium=premium_left&utm_campaign=wcpgpp">no limitations</a>!</p>
        
            <?php softsdev_sdwpp_plugin_settings();?>           

        
        
        </div>
        <div class="right-mc-setting">
            <div style="border: 5px dashed #B0E0E6; padding: 0 20px; background: white;">
                <h3>WooCommerce Payment per Product Premium</h3>
                <p>This plugin has a Premium version with no limitations. <a href="http://www.dreamfoxmedia.com/shop/woocommerce-payment-gateway-per-product-premium/#utm_source=plugin&utm_medium=premium_right&utm_campaign=wcpgpp">Have a look at its benefits</a>!</p>
            </div>
        <?php $user = wp_get_current_user(); ?>
            <!-- Begin MailChimp Signup Form -->
            <form style="text-align:center; border-bottom:1px solid #ccc; border-top:1px solid #ccc; padding:20px 0; margin:20px 0;" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                <center>Like this product we’ve made and want to contribute to its future development? Donate however much you’d like with the below donate button.<br><br></center>	
                <input type="hidden" name="cmd" value="_s-xclick">
                <input type="hidden" name="hosted_button_id" value="UNTLWQSLRH85U">
                <input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
                <img alt="" border="0" src="https://www.paypalobjects.com/nl_NL/i/scr/pixel.gif" width="1" height="1">
            </form>
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
                        <label for="mce-EMAIL">Subscribe to our mailinglist</label>
                        <input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="email address" required>
                        <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                        <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_a0293a6a24c69115bd080594e_131c5e0c11" tabindex="-1" value=""></div>
                        <div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
                    </div>
                </form>
            </div>
            <div class="wcpgpp-box" >
                <h4 class="wcpgpp-title"><?php echo __('Looking for help?', 'woocommerce paypent per product'); ?></h4>
                <p><?php echo __('We have some resources available to help you in the right direction.', 'woocommerce paypent per product'); ?></p>
                <ul class="ul-square">
                    <li><a href="http://www.dreamfoxmedia.com/ufaq-category/wcpgpp-f/#utm_source=plugin&utm_medium=faq&utm_campaign=wcpgpp"><?php echo __('Knowledge Base', 'woocommerce paypent per product'); ?></a></li>
                    <li><a href="https://wordpress.org/plugins/woocommerce-product-payments/faq/"><?php echo __('Frequently Asked Questions', 'woocommerce paypent per product'); ?></a></li>
                </ul>
                <p><?php echo sprintf(__('If your answer can not be found in the resources listed above, please use the <a href="%s">support forums on WordPress.org</a>.'), 'https://wordpress.org/support/plugin/woocommerce-product-payments'); ?></p>
                <p><?php echo sprintf(__('Found a bug? Please <a href="%s">open an issue on GitHub</a>.'), 'https://github.com/dreamfoxnl/WC-Payment-Gateway-per-Product-Free/issues'); ?></p>
            </div>
        </div>
        <?php
    }

    add_action('add_meta_boxes', 'wpp_meta_box_add');

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
     * @return type
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
        $postPayments = get_post_meta($post->ID, 'payments', true) ? get_post_meta($post->ID, 'payments', true) : array();
        if (count($productIds) >= 10 && !count($postPayments)) {
            echo 'Limit reached Please download full version package at www.dreamfoxmedia.com!';
            return;
        }
        $woo = new WC_Payment_Gateways();
        $payments = $woo->payment_gateways;
        foreach ($payments as $pay) {
            /**
             *  skip if payment in disbled from admin
             */
            if ($pay->enabled == 'no') {
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
            if (is_array($productIds) && !in_array($post_id, $productIds) && count($productIds) <= 10) {
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
     * @global type $woocommerce
     * @param type $available_gateways
     * @return type
     */
    function wpppayment_gateway_disable_country($available_gateways) {
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

?>