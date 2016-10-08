<?php
/**
 * Plugin Name: Woocommerce categories Mailchimp groups
 * Plugin URI: http://www.dreamfoxmedia.nl/woocommerce/woocommerce-mailchimp-plugin/ 
 * Version: 1.0.5
 * Author: Dreamfox Media
 * Author URI: http://www.dreamfoxmedia.nl
 * Description: Connecting your Mailchimp groups to your WooCommerce categories. You will even be able to connect your 
 * Mailchimp group to any of your individual products. This great plugin will help you to stop sending floral discounts
 * to people who ordered kitchen appliances.
 * Requires at least: 3.7
 * Tested up to: 4.6.1
 * @Developer : Softsdev[Anand Rathi]( Dreamfoxmedia )
 */
/**
 * Check if WooCommerce is active
 */


if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))
{

    
    add_action( 'admin_enqueue_scripts', 'softsdev_mailchimp_enqueue' );
    /* ----------------------------------------------------- */
    // Submenu on woocommerce section
    add_action('admin_menu', 'softsdev_mailchimp_submenu_page');

    /* ----------------------------------------------------- */
    /**
     * Add mailchimp enqueue
     */
    
    function softsdev_mailchimp_enqueue() {
        wp_enqueue_style( 'softsdev_mailchimp_enqueue', plugin_dir_url( __FILE__ ) . '/css/style.css' );
    }
    /* ----------------------------------------------------- */
    /**
     * Menu of mailchimp page
     */
    function softsdev_mailchimp_submenu_page()
    {
        add_submenu_page('woocommerce', __('Mailchimp Group', 'softsdev'), __('Mailchimp Group', 'softsdev'), 'manage_options', 'softsdev-mailchimp', 'softsdev_mailchimp_settings');
    }

    /* ----------------------------------------------------- */
    /**
     * Update footer text
     */
    function softsdev_mailchimp_footer_text( $text ) {

        if(! empty( $_GET['page'] ) && strpos( $_GET['page'], 'softsdev-mailchimp' ) === 0 ) {
            $text = sprintf( 'If you enjoy using <strong>Woocommerce categories Mailchimp groups</strong>, please <a href="%s" target="_blank">leave us a ★★★★★ rating</a>. A <strong style="text-decoration: underline;">huge</strong> thank you in advance!', 'https://wordpress.org/support/view/plugin-reviews/woocommerce-mailchimp-plugin?rate=5#postform' );
        }

        return $text;
    }

    /* ----------------------------------------------------- */
    /**
     * Update footer version
     */
    function softsdev_mailchimp_update_footer($text) {
        if(! empty( $_GET['page'] ) && strpos( $_GET['page'], 'softsdev-mailchimp' ) === 0 ) {
            $text = 'Version 1.0.5';
        }

        return $text;   
    }


    /**
     * Setting form of mailchimp category
     */

    function softsdev_mailchimp_settings()
    {
        

        require 'inc/MCAPI.class.php';

        add_filter( 'admin_footer_text', 'softsdev_mailchimp_footer_text' );
        add_filter( 'update_footer', 'softsdev_mailchimp_update_footer' );

        
        // fwt and set settings
        if (isset($_POST['softsdev_mailchimp']))
        {
            $softsdev_wc_mc_setting = get_softsdev_wc_mc_setting( $_POST['softsdev_mailchimp'] );
            $api = new MCAPI($softsdev_wc_mc_setting['api']);
            if (!$api->ping())
            {
                $softsdev_wc_mc_setting = '';
            }
            update_option('softsdev_wc_mc_full_setting', $softsdev_wc_mc_setting);
        } else
        {
            $softsdev_wc_mc_setting = get_softsdev_wc_mc_setting();
        }
        // Getting lists of mailchimp from api
        $lists = array();
        if (isset($softsdev_wc_mc_setting['api']))
        {
            $api = new MCAPI($softsdev_wc_mc_setting['api']);

            $listing = $api->lists();
            $lists = $listing['data'];
        }
        echo '<div class="wrap "><div id="icon-tools" class="icon32"></div>';
        echo '<h2 style="padding-bottom:15px; margin-bottom:20px; border-bottom:1px solid #ccc">' . __('Woocommerce categories Mailchimp groups', 'softsdev') . '</h2>';
        ?>
        <div class="left-mc-setting">
            <form id="woo_dd" action="<?php echo $_SERVER['PHP_SELF'] . '?page=softsdev-mailchimp' ?>" method="post">
                <div class="postbox">
                    <h3 class="hndle"><?php echo __('Mailchimp Setting', 'softsdev'); ?></h3>
                    <table width="100%" class="form-table">
                        <tr>
                            <th width="170px">
                                <label for="softsdev_mailchimp_api"><?php echo __('Status', 'softsdev') ?> </label>
                                <!-- <img width="16" height="16" src="<?php //echo plugins_url('images/help.png', __FILE__) ?>" class="help_tip" title="<?php //echo __('Mailchimp API Key', 'softsdev'); ?>"> -->
                            </th>
                            <td>
                                <?php if( $softsdev_wc_mc_setting['api'] ) { ?>
                                    <span class="status positive"><?php _e( 'VERBONDEN' ,'softsdev' ); ?></span>
                                    <span>
                                        <a href = "http://www.dreamfoxmedia.com/ufaq-category/wcwctmg/" target="_blank">
                                            <img style="margin-left:5px; vertical-align:middle" width="20" height="20" src="<?php echo plugins_url('images/help.png', __FILE__) ?>" class="help_tip" title="<?php echo __('Questionmarks', 'softsdev'); ?>">
                                        </a>
                                    </span>
                                <?php } else { ?>
                                    <span class="status neutral"><?php _e( 'OUT OF SYNC', 'softsdev' ); ?></span>
                                    <a href = "http://www.dreamfoxmedia.com/ufaq-category/wcwctmg/" target="_blank">
                                        <img style="margin-left:5px; vertical-align:middle" width="18" height="18" src="<?php echo plugins_url('images/help.png', __FILE__) ?>" class="help_tip" title="<?php echo __('Questionmarks', 'softsdev'); ?>">
                                    </a>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <th width="170px">
                                <label for="softsdev_mailchimp_api"><?php echo __('Mailchimp API', 'softsdev') ?> </label>
                                <a href = "http://www.dreamfoxmedia.com/ufaq-category/wcwctmg/" target="_blank">
                                <img width="16" height="16" src="<?php echo plugins_url('images/help.png', __FILE__) ?>" class="help_tip" title="<?php echo __('Mailchimp API Key', 'softsdev'); ?>">
                                </a>
                            </th>
                            <td>
                                <input id="softsdev_mailchimp_api" name="softsdev_mailchimp[api]" type="text" value="<?php echo @$softsdev_wc_mc_setting['api'] ?>" size="40"/>
                                <br />
                                <p>The API key for connecting with your MailChimp account. <a href="https://admin.mailchimp.com/account/api">Get your API key here.</a></p>
                            </td>
                        </tr>
                        <tr style="display: <?php echo @$softsdev_wc_mc_setting['api'] ? '' : 'none' ?>">
                            <th width="170px">
                                <label for="softsdev_mailchimp_list"><?php echo __('Mailchimp List Name', 'softsdev') ?> </label>
                                <a href = "http://www.dreamfoxmedia.com/ufaq-category/wcwctmg/" target="_blank">
                                    <img width="16" height="16" src="<?php echo plugins_url('images/help.png', __FILE__) ?>" class="help_tip" title="<?php echo __('Mailchimp List', 'softsdev'); ?>"></a>
                            </th>
                            <td>
                                <select id="softsdev_mailchimp[list_mgroup_id]" name="softsdev_mailchimp[list_mgroup_id]">
                                    <option value="">None</option>
                                    <?php
                                    /**
                                     * ":" is seprator
                                     */
                                    foreach ($lists as $list) {
                                        // get all groups of list
                                        $groups = $api->listInterestGroupings($list['id']);
                                        echo "<optgroup label='" . $list['name'] . "'>";
                                        foreach ($groups as $group) {
                                            echo "<option value='".$list['id'].':'.$group['id']."' '".selected( $list['id'].':'.$group['id'], $softsdev_wc_mc_setting['list_mgroup_id'])."'>" . $group['name'] . "</option>";
                                        }
                                        echo "</optgroup>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr> 
                        <tr>
                            <th>
                                <label for="softsdev_mailchimp_enjoying"><?php echo __('Are you enjoying this plugin?') ?></label>
                            </th>
                            <td>
                                <p>See also our other plugins at: <a href="http://www.dreamfoxmedia.com/portfolio/portfolio-grid/1-portfolio-grid/our-plugins/">http://www.dreamfoxmedia.com/portfolio/portfolio-grid/1-portfolio-grid/our-plugins/</a></p>
                                <p>This plugin is not developed by or affiliated with MailChimp in any way.</p>
                            </td>
                        </tr>
                    </table>
                </div>              
                <input class="button-large button-primary" type="submit" value="Save Changes" />
            </form>
        </div>
        <div class="right-mc-setting">
            <div style="border: 5px dotted #cc4444; padding: 0 20px; background: white;">
            <h3>MailChimp for WordPress Premium</h3>
                <p>This plugin has a Premium add-on, unlocking several powerful features. <a href="http://www.dreamfoxmedia.com/project/woocommerce-categories-to-mailchimp-groups-plugin-premium/">Have a look at its benefits</a>!</p>
            </div>

            <?php $user = wp_get_current_user(); ?>
            <!-- Begin MailChimp Signup Form -->
            
            <form style="text-align:center; border-bottom:1px solid #ccc; border-top:1px solid #ccc; padding:20px 0; margin:20px 0;" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
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
                <label for="mce-EMAIL">Subscribe to our mailing list</label>
                <input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="email address" required>
                <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_a0293a6a24c69115bd080594e_131c5e0c11" tabindex="-1" value=""></div>
                <div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
                </div>
            </form>
            </div>

            <div class="mc4wp-box" >
                <h4 class="mc4wp-title"><?php echo __( 'Looking for help?', 'mailchimp-for-wp' ); ?></h4>
                <p><?php echo __( 'We have some resources available to help you in the right direction.', 'mailchimp-for-wp' ); ?></p>
                <ul class="ul-square">
                    <li><a href="https://mc4wp.com/kb/#utm_source=wp-plugin&utm_medium=mailchimp-for-wp&utm_campaign=sidebar"><?php echo __( 'Knowledge Base', 'mailchimp-for-wp' ); ?></a></li>
                    <li><a href="https://wordpress.org/plugins/mailchimp-for-wp/faq/"><?php echo __( 'Frequently Asked Questions', 'mailchimp-for-wp' ); ?></a></li>
                    <li><a href="http://developer.mc4wp.com/#utm_source=wp-plugin&utm_medium=mailchimp-for-wp&utm_campaign=sidebar"><?php echo __( 'Code reference for developers', 'mailchimp-for-wp' ); ?></a></li>
                </ul>
                <p><?php echo sprintf( __( 'If your answer can not be found in the resources listed above, please use the <a href="%s">support forums on WordPress.org</a>.' ), 'https://wordpress.org/support/plugin/mailchimp-for-wp' ); ?></p>
                <p><?php echo sprintf( __( 'Found a bug? Please <a href="%s">open an issue on GitHub</a>.' ), 'https://github.com/ibericode/mailchimp-for-wordpress/issues' ); ?></p>
            </div>

        </div>
        <style type="text/css">
            
        </style>
        <?php
    }


    /**
     * 
     * @param type $array
     * @param type $fields1
     * @param type $fields2
     * @return type
     */
    function softsdev_mc_list_data($array, $fields1, $fields2)
    {
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
     * @return type
     */
    function softsdev_get_mc_groups()
    {
        require 'inc/MCAPI.class.php';
        $softsdev_wc_mc_setting = get_softsdev_wc_mc_setting();
        if (isset($softsdev_wc_mc_setting['api']) && isset($softsdev_wc_mc_setting['list_id']) && $softsdev_wc_mc_setting['list_id'] != '')
        {
            $api = new MCAPI($softsdev_wc_mc_setting['api']);
            $mgroups = $api->listInterestGroupings($softsdev_wc_mc_setting['list_id']);

            foreach ($mgroups as $groups ){
                if( $groups['id'] == $softsdev_wc_mc_setting['mgroup_id'] )
                    return $groups['groups'];
            }
            
        }
        return array();
    }
    /**************************************** */
    /*
     * Add Extra colum to woocommerce product category
     */
    function softsdev_mc_product_cat_add_new_meta_field()
    {
        
        $softsdev_wc_mc_setting = get_softsdev_wc_mc_setting();
        if( !@$softsdev_wc_mc_setting['api'] ) return '';
        $groups = softsdev_get_mc_groups();
        // this will add the custom meta field to the add new term page
        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="softsdev_mc_group"><?php _e('Mailchimp Group', 'softsdev_mc'); ?></label></th>
            <td>
                <?php
                echo woocommerce_wp_select(
                        array(
                            'value' => '',
                            'label' => '',
                            'id' => 'softsdev_mc_group',
                            'options' => array('1' => 'Select Group') + softsdev_mc_list_data($groups, 'name', 'name')
                        )
                );
                ?>
                <p class="description"><?php _e('Enter a value for this field', 'softsdev'); ?></p>
            </td>
        </tr>
        <?php
    }

    add_action('product_cat_add_form_fields', 'softsdev_mc_product_cat_add_new_meta_field', 10, 2);



/**PRODUCTS *****************************************************************************************************/
    /**************************************** */
    /*
     * Add Extra colum to woocommerce product
     */
    function softsdev_mc_product_add_new_meta_field($post)
    {
        $softsdev_mc_group = get_post_meta( $post->ID, 'softsdev_mc_group', true );
        
        $softsdev_wc_mc_setting = get_softsdev_wc_mc_setting();
        if( !@$softsdev_wc_mc_setting['api'] ) return '';
        $groups = softsdev_get_mc_groups();
        // this will add the custom meta field to the add new term page
        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="softsdev_mc_group"><?php _e('Mailchimp Group', 'softsdev_mc'); ?></label></th>
            <td>
                <?php
                echo woocommerce_wp_select(
                        array(
                            'value' => $softsdev_mc_group,
                            'label' => '',
                            'id' => 'softsdev_mc_group',
                            'options' => array('1' => 'Select Group') + softsdev_mc_list_data($groups, 'name', 'name')
                        )
                );
                ?>
                <p class="description"><?php _e('Enter a value for this field', 'softsdev'); ?></p>
            </td>
        </tr>
        <?php
    }    

   add_action('product_add_form_fields', 'softsdev_mc_product_add_new_meta_field', 10, 2);

 
     /* Save Mailchimp Group meta field
     */
    
/*  function softsdev_mc_product_save_meta_field($post_id) 
    {
        $softsdev_mc_group = $_POST["softsdev_mc_group"];
        update_post_meta( $post_id, 'softsdev_mc_group', $softsdev_mc_group);        
    }
    add_action( 'save_post', 'softsdev_mc_product_save_meta_field' );

*/

/******************************************************************************************************/


    /**
     * Render Edit page of product category
     * @param type $term
     */
    function softsdev_mc_product_cat_edit_meta_field($term)
    {
        
        $softsdev_wc_mc_setting = get_softsdev_wc_mc_setting();
        if( !@$softsdev_wc_mc_setting['api'] ) return '';        
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
                echo woocommerce_wp_select(
                        array(
                            'value' => @$softsdev_mc_terms_groups['term'.$softsdev_wc_mc_setting['list_mgroup_id'].'_' . $t_id],
                            'id' => 'softsdev_mc_group',
                            'options' => array('' => 'Select Group') + softsdev_mc_list_data($groups, 'name', 'name')
                        )
                );
                ?>
                <p class="description"><?php _e('Enter a value for this field', 'softsdev'); ?></p>
            </td>
        </tr>
        <?php
    }

    add_action('product_cat_edit_form_fields', 'softsdev_mc_product_cat_edit_meta_field', 10, 2); 



    /**
     * Save Custom field softsdev_mc_group got woocommerce product category
     * @param type $term_id
     */
    function save_softsdev_mc_product_cat_custom_meta($term_id)
    {
        if (isset($_POST['softsdev_mc_group']))
        {
            $t_id = $term_id;
            $softsdev_wc_mc_setting = get_softsdev_wc_mc_setting();
            $softsdev_mc_terms_groups = get_option('softsdev_mc_term_group');
            $softsdev_mc_terms_groups['term'.$softsdev_wc_mc_setting['list_mgroup_id'].'_' . $t_id] = $_POST['softsdev_mc_group'];
            // Save the option array.
            update_option('softsdev_mc_term_group', $softsdev_mc_terms_groups);
        }
    }
    add_action('edited_product_cat', 'save_softsdev_mc_product_cat_custom_meta', 10, 2);
    add_action('create_product_cat', 'save_softsdev_mc_product_cat_custom_meta', 10, 2);


    /**************************************** */
    function save_variable_product_fields( $variation_id ) {
        // find the index for the given variation ID and save the associated points earned
        $index = array_search( $variation_id, $_POST['variable_post_id'] );
        if ( false !== $index ) {
            // points earned
            if ( '' !== $_POST['variable_points_earned'][ $index ] )
                update_post_meta( $variation_id, '_wc_points_earned', stripslashes( $_POST['variable_points_earned'][ $index ] ) );
            else
                delete_post_meta( $variation_id, '_wc_points_earned' );
            // maximum points discount
            if ( '' !== $_POST['variable_max_point_discount'][ $index ] )
                update_post_meta( $variation_id, '_wc_points_max_discount', stripslashes( $_POST['variable_max_point_discount'][ $index ] ) );
            else
                delete_post_meta( $variation_id, '_wc_points_max_discount' );
        }
    }
   
        // save the 'Points Earned' field for variable subscription products
    add_action( 'woocommerce_save_product_subscription_variation', array( $this, 'save_variable_product_fields' ) );

    function softsdev_mc_subscribe($order)
    {
        
        $softsdev_wc_mc_setting = get_softsdev_wc_mc_setting();

        if( !@$softsdev_wc_mc_setting['api'] ) return ''; 
     
        $softsdev_mc_terms_groups = get_option('softsdev_mc_term_group');
        // get order details
        $order = new WC_Order($order);
        // getting all products
        $products = $order->get_items();
    
        // define group variable
        $groups = array();
        foreach( $products as $product ){
            // Get terms of product
            $terms = get_the_terms( $product['product_id'], 'product_cat' );
            // getting all groups of term
            foreach ($terms as $term){
                if(array_key_exists( 'term'.$softsdev_wc_mc_setting['list_mgroup_id'].'_'.$term->term_id, $softsdev_mc_terms_groups) ){
                    $groups[] = $softsdev_mc_terms_groups['term'.$softsdev_wc_mc_setting['list_mgroup_id'].'_'.$term->term_id];
                }
            }
        }
        // subscribe to mailchimp
        softsdev_subscribe_to_mc( $groups );

    }
    
    
    
    /**
     * 
     * @param WC_Order $order
     */
    add_action('woocommerce_thankyou', 'softsdev_mc_subscribe', 20);
  
    
    /**
     * 
     * @param type $groups
     * 
     */
    function softsdev_subscribe_to_mc( $groups ){

        if( count( $groups ) > 0 )
        {
            $softsdev_wc_mc_setting = get_softsdev_wc_mc_setting();
            require 'inc/MCAPI.class.php';

            $api = new MCAPI($softsdev_wc_mc_setting['api']);

            if (isset($softsdev_wc_mc_setting['list_id'])){
                // getting current user data
                $current_user = wp_get_current_user();
                $my_email = $current_user->user_email;
                $merge_vars = Array(
                    'EMAIL' => $current_user->user_email,
                    'FNAME' => $current_user->user_firstname,
                    'LNAME' => $current_user->user_lastname,
                    'GROUPINGS' => /*implode(',', $groups)*/array(
                        array('id' => $softsdev_wc_mc_setting['mgroup_id'], 'groups'=>implode( ',', $groups ) )
                    )
                );

                //send subscription to mailchimp 
                $api->listSubscribe( $softsdev_wc_mc_setting['list_id'], $my_email, $merge_vars, 'html', true, true );
                
            }           
        }
    }
    
    function get_softsdev_wc_mc_setting( $setting = '' ){
        if( !$setting )
            $setting = get_option('softsdev_wc_mc_full_setting');
        if( !$setting )
            return array();
        if( $setting['list_mgroup_id'] )
            list($setting['list_id'], $setting['mgroup_id']) = explode(':', $setting['list_mgroup_id'] );
        else{
            $setting['list_id'] = $setting['mgroup_id'] = '';
        }
        return $setting;
    }
}











