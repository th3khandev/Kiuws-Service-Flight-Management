<?php
/**
 * @link https://key-core.com
 * 
 * @since 1.0
 * 
 * @package Kiuws_Service_Flight_Management
 * 
 * Plugin Name: Kiuws Service Flight Management
 * Description: Streamline flight management on your site. Connect to Kiuws API for real-time flight data. Perfect for travel websites.
 * Version: 1.0
 * Author: Anthony Garcia - @th3khandev, Key Core
 * Author URI: https://key-core.com
 */

use Kiuws_Service_Flight_Management\Api\Api;
use Kiuws_Service_Flight_Management\DB\FlightManagementDB;
use Kiuws_Service_Flight_Management\Includes\Admin;
use Kiuws_Service_Flight_Management\Includes\Frontend;

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

final class Kiuws_Service_Flight_Management {

    /**
     * Plugin version
     * 
     * @var string
     */
    const VERSION = '1.0';

    /**
     * Class constructor
     */
    private function __construct() {
        $this->plugin_constants();
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), [$this, 'add_settings_link']);
        add_action('plugins_loaded', [$this, 'init_plugin']);
    }

    /**
     * Define plugin constants 
     * @since 1.0
     */
    public function plugin_constants() {
        define('FLIGHT_MANAGEMENT_VERSION', self::VERSION);
        define('FLIGHT_MANAGEMENT_DIR', plugin_dir_path(__FILE__));
        define('FLIGHT_MANAGEMENT_URL', plugin_dir_url(__FILE__));
        define('FLIGHT_MANAGEMENT_FILE', __FILE__);
        define('FLIGHT_MANAGEMENT_PREFIX', 'flight_management_');
    }

    /**
     * Initialize a singleton instance
     * 
     * @return \Kiuws_Service_Flight_Management
     * 
     * @since 1.0
     */
    public static function init() {
        static $instance = false;
        if (!$instance) {
            $instance = new self();
        }
        return $instance;
    }

    /**
     * On plugin activation
     * 
     * @since 1.0
     */
    public function activate() {
        $installed = get_option(FLIGHT_MANAGEMENT_PREFIX . 'installed');
        if (!$installed) {
            update_option(FLIGHT_MANAGEMENT_PREFIX . 'installed', time());
        }
        update_option(FLIGHT_MANAGEMENT_PREFIX . 'version', FLIGHT_MANAGEMENT_VERSION);

        // create tables in database
        $flightManagementDB = new FlightManagementDB();
        $flightManagementDB->initDB();
    }

    /**
     * On plugin deactivation
     * 
     * @since 1.0
     */
    public function deactivate() {
        // Do something
    }

    public function add_settings_link($links) {
        $settings_link = '<a href="admin.php?page=flight-management-configuration">Settings</a>';
        array_push($links, $settings_link);
        return $links;
    }

    /**
     * Initialize the plugin
     * 
     * @since 1.0
     */
    public function init_plugin() {
        // init plugin
        new Admin();
        new Frontend();
        new Api();
    }
}


/**
 * Initialize the main plugin
 * 
 * @return \Kiuws_Service_Flight_Management
 * 
 * @since 1.0
 */
function kiuws_service_flight_management() {
    return Kiuws_Service_Flight_Management::init();
}

// run plugin
kiuws_service_flight_management();
