=== Woocommerce Payment Gateway per Product Premium ===
Contributors: dreamfox
Donate link: http://www.dreamfoxmedia.com
Tags: woocommerce,payments,plugin,gateway
Requires at least: 3.0.1
Tested up to: 4.4.2
Stable tag: 1.2.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==
This plugin for woocommerce lets you select the available payment gateways for each individual product.
You can select for eacht individual product the payment gateway that will be used by checkout. If no selection is made, then the default payment gateways are displayed. If you for example only select paypal then only paypal will available for that product by checking out.
Works with latest Wordpress & Woocommerce


<b>other plugins from Dreamfox:</b><br>
- WC Shipping Gateway per Product:  <a href="http://wordpress.org/plugins/woocommerce-shipping-gateway-per-product/" target="_blank">Information</a> - <a href="http://wordpress.org/plugins/woocommerce-shipping-gateway-per-product/" target="_blank">Free version</a> - <a href="http://www.dreamfoxmedia.nl/shop/woocommerce-shipping-gateway-per-product-pro/" target="_blank">PRO Version</a><br>
- WC Payment Gateway per product:  <a href="http://wordpress.org/plugins/woocommerce-product-payments/" target="_blank">Information</a> - <a href="http://wordpress.org/plugins/woocommerce-product-payments/" target="_blank">Free version</a> - <a href="http://www.dreamfoxmedia.nl/shop/woocommerce-payment-gateway-per-product-pro/" target="_blank">PRO Version</a>
- WooCommerce Delivery date: <a href="http://wordpress.org/plugins/woocommerce-delivery-date/" target="_blank">Information</a> - <a href="http://wordpress.org/plugins/woocommerce-delivery-date" target="_blank">Free version</a> - <a href="http://www.dreamfoxmedia.nl/shop/woocommerce-delivery-date-pro/" target="_blank">PRO Version</a>
- WooCommerce MailChimp Plugin: <a href="http://wordpress.org/plugins/woocommerce-mailchimp-plugin/" target="_blank">Information</a> - <a href="http://wordpress.org/plugins/woocommerce-mailchimp-plugin" target="_blank">Free version</a> - <a href="http://www.dreamfoxmedia.com/shop/woocommerce-categories-to-mailchimp-groups-plugin-premium/#utm_source=wp-plugin_dir&utm_medium=description&utm_campaign=wordpress" target="_blank">Premium Version</a> 

== Installation ==

= For automatic installation: =

The simplest way to install is to click on 'Plugins' then 'Add' and type 'Woocommerce Payment Gateway per Product' in the search field.

= For manual installation 1: =

1. Login to your website and go to the Plugins section of your admin panel.
1. Click the Add New button.
1. Under Install Plugins, click the Upload link.
1. Select the plugin zip file (woocommerce-product-payments.x.x.x.zip) from your computer then click the Install Now button.
1. You should see a message stating that the plugin was installed successfully.
1. Click the Activate Plugin link.

= For manual installation 2: =

1. You should have access to the server where WordPress is installed. If you don't, see your system administrator.
1. Copy the plugin zip file (woocommerce-product-payments.x.x.x.zip) up to your server and unzip it somewhere on the file system.
1. Copy the "woocommerce-product-payments" folder into the /wp-content/plugins directory of your WordPress installation.
1. Login to your website and go to the Plugins section of your admin panel.
1. Look for "woocommerce-product-payments" and click Activate.
1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress


== Frequently asked questions ==


= What happens if i add more then one product in the shopping card with different selected payment gateways? =

Allowed payment gateways goes before denied payment gateways. so if for example you set:

- product 1 to paypal
- product 2 to paypal & credit card

Then both payment gateways are shown by checkout

= More Information =

For more information, feel free to visit the official website for this plugin: <a href="http://www.dreamfoxmedia,com" target="_blank">Dreamfox</a>.





== Screenshots ==



1. screenshot1.png

2. screenshot2.png

3. screenshot3.png



== Changelog ==

= 1.2.4 =

* update least version



= 1.2.3 =

* save on un selecting all payments



= 1.2.2 =

* add filter to show disabled Payment gateway "softsdev_show_disabled_gateways"



= 1.2.1 =

* Fixed Quick Edit Problem



= 1.2.0 =

* Hide Disabled payment gateway from admin product list

* Code Formatting



= 1.1.9 =

* fixed common issue.



= 1.1.8 =

* Fix Warning: in_array() [function.in-array]: Wrong data type for second argument



= 1.1.6 =

* Fix payment block display at the time of adding product( woocommerce 2.1.8 )



= 1.1.5 =

* Typo in description



= 1.1.4 =

* Restrict to save for autosave & revision



= 1.1.3 =

* Remove duplicate added files



= 1.1.2 =

* Fix conflicting with wp_mandrill plugin

 

= 1.1.1 =

* Fix bugs of available gateways for multiple products on cart



= 1.1.0 =

* Fix bugs of available gateways



= 1.0.9 =

* Fix country not available issue on switching settings



= 1.0.8 =

* replace deprecated functions with stable &  

* remove warnings



= 1.0.7 =

* add readme.txt file and fixes



= 1.0.6 =

* Rename default function to avoid conflict with function.php



= 1.0.5 =

* Fix Coding and flush



= 1.0.4 =

* Tested on Woocommerce 2.0.17



= 1.0.3 =

* add limit



= 1.0.2 =

* fixed typo



= 1.0.1 =

* added author



== Upgrade notice ==



= 1.0 =

Upgrade for new version



= 0.5 =

This version fixes a bug.  Upgrade immediately.

