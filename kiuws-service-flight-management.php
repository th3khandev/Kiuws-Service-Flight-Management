<?php
/**
 * Plugin Name: Kiuws Service Flight Management
 * Description: Streamline flight management on your site. Connect to Kiuws API for real-time flight data. Perfect for travel websites.
 * Version: 1.0
 * Author: Anthony Garcia - @th3khandev
 */

 // Define constants
define('FLIGHT_MANAGEMENT_VERSION', '1.0');
define('FLIGHT_MANAGEMENT_DIR', plugin_dir_path(__FILE__));
define('FLIGHT_MANAGEMENT_URL', plugin_dir_url(__FILE__));
define('FLIGHT_MANAGEMENT_FILE', __FILE__);
define('FLIGHT_MANAGEMENT_PREFIX', 'flight_management_');

// This function adds a page to the WordPress admin menu
function add_admin_menu_page() {
    add_menu_page(
        'Kiuws Flight Management', // Page title
        'Kiuws Flight Management', // Menu text
        'manage_options', // Capability required to access
        'flight-management', // Page slug
        'show_flight_management_page' // Function to display the page
    );
}

// This function displays the content of the flight management page
function show_flight_management_page() {
    // Add the content flight management 
    echo '<div class="wrap">';
    echo '<h2>Kiuws Flight Management</h2>';
    echo '</div>';
}

// Register the add_admin_menu_page function
add_action('admin_menu', 'add_admin_menu_page');
