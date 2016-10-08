<?php
/**
 * Plugin Name: Softsdev Event Manager
 * Plugin URI: www.softsdev.com/event-manager
 * Version: 1.0.0
 * Author: Softsdev
 * Author URI: www.softsdev.com 
 * Description: Extend Woocommerce plugin to See Quick view of product
 * Requires at least: 3.7
 * Tested up to: 4.1
 * @developer Softsdev <mail.softsdev@gmail.com>
 */
define('SD_EVENTS_PATH', plugin_dir_path(__FILE__));
define('SD_EVENTS_URL', plugins_url('', __FILE__));
define('SD_EVENTS_IMAGES', SD_EVENTS_URL . '/images');
define('SD_EVENTS_FILE', plugin_basename(__FILE__));
define('SD_EVENTS_INC', SD_EVENTS_PATH . '/inc');
define('SD_EVENTS_TEMPLATES', SD_EVENTS_PATH . '/templates');
define('SD_EVENTS_ADMIN_VIEWS', SD_EVENTS_TEMPLATES . '/admin');


/**
 * Include File event-manager-post-type
 */
require_once( SD_EVENTS_INC . '/CHelper.php' );

require_once( SD_EVENTS_PATH . '/softsdev-event-manager-post-type.php' );

require_once( SD_EVENTS_PATH . '/softsdev-event-manager-content.php' );

/**
 * Checking if class exits
 */
if (!class_exists('Softsdev_Event_Manager')) :

        /**
         * Class Softsdev_Event_Manager
         */
        class Softsdev_Event_Manager {

                /**
                 * 
                 */
                public function __construct() {
                        add_action("wp_ajax_load_event_location", array(&$this, 'load_event_location'));
                        add_action("wp_ajax_nopriv_load_event_location", array(&$this, 'load_event_location'));
                }

                /**
                 * 
                 */
                public function load_event_location() {

                        $address = $_POST['address'];
                        if (!$address) {
                                return null;
                                die;
                        }
                        $this->getGoogleMap($address);
                        die;
                }
                
                public function getGoogleMap($address){
                        $geocode = file_get_contents('http://maps.google.com/maps/api/geocode/json?address=' . urlencode($address));

                        $output = json_decode($geocode); //Store values in variable
                        if ($output->status == 'OK') { // Check if address is available or not
                                $lat = $output->results[0]->geometry->location->lat; //Returns Latitude
                                $long = $output->results[0]->geometry->location->lng; // Returns Longitude
                                ?>
                                <script type = "text/javascript">
                                        jQuery(document).ready(function () {
                                            initialize_map(<?php echo $lat ?>, <?php echo $long ?>, 'el_map');
                                        });
                                </script>
                                <input type="hidden" size="36" name="sdem[lat]" value="<?php echo $lat ?>" /> 
                                <input type="hidden" size="36" name="sdem[long]" value="<?php echo $long ?>" /> 
                                <div id="el_map" style="width:400px;height:250px; margin-top:10px;"></div>

                                <?php
                                if ($address === null)
                                        die;
                        }                        
                }

        }

        /**
         * Softsdev_Event_Manager class object
         */
        $sdem_obj = new Softsdev_Event_Manager;
endif;
?>