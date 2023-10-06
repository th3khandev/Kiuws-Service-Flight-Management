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

function create_flight_management_submenu() {
    // Add the sub menu "Configuration" to main menu 
    add_submenu_page(
        'flight-management', // Slug main menu
        'Configuration',
        'Configuration',
        'manage_options',
        'flight-management-configuration',
        'flight_management_configuration_page'
    );
}

function flight_management_configuration_page() {
    // Validate user permission
    if (!current_user_can('manage_options')) {
        wp_die('Permission Denied');
    }

    $error_messages = [];

    if (isset($_POST['submit'])) {
        // Process and save data
        $result = save_flight_configuration();
        
        if (is_wp_error($result)) {
            // Si hubo un error, agrega un mensaje de error
            $error_messages[] = $result->get_error_message();
        } else {
            // Si todo fue exitoso, agrega un mensaje de éxito
            add_settings_error('flight-management-messages', 'success', 'Configuration saved successfully', 'updated');
        }
    }

    // Show form configuration
    ?>
    <div class="wrap">
        <h2>Flight Management Configuration</h2>
        <!-- Show error o success messages -->
        <?php
        if (!empty($error_messages)) {
            // Show error messages
            foreach ($error_messages as $error_message) {
                echo '<div class="error"><p>' . esc_html($error_message) . '</p></div>';
            }
        } else {
            // Show success messages
            settings_errors('flight-management-messages');
        }
        ?>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row">User</th>
                    <td><input type="text" name="<?php echo FLIGHT_MANAGEMENT_PREFIX . 'user' ?>" value="<?php echo esc_attr(get_option(FLIGHT_MANAGEMENT_PREFIX . 'user')); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">Password</th>
                    <td><input type="password" name="<?php echo FLIGHT_MANAGEMENT_PREFIX . 'password' ?>" value="<?php echo esc_attr(get_option(FLIGHT_MANAGEMENT_PREFIX . 'password')); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">Mode</th>
                    <td>
                        <select name="<?php echo FLIGHT_MANAGEMENT_PREFIX . 'mode' ?>">
                            <option value="testing" <?php selected(get_option(FLIGHT_MANAGEMENT_PREFIX . 'mode'), 'testing'); ?>>Testing</option>
                            <option value="production" <?php selected(get_option(FLIGHT_MANAGEMENT_PREFIX . 'mode'), 'production'); ?>>Production</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">AgentSine</th>
                    <td><input type="text" name="<?php echo FLIGHT_MANAGEMENT_PREFIX . 'agent_sine' ?>" value="<?php echo esc_attr(get_option(FLIGHT_MANAGEMENT_PREFIX . 'agent_sine')); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">Terminal ID</th>
                    <td><input type="text" name="<?php echo FLIGHT_MANAGEMENT_PREFIX . 'terminal_id' ?>" value="<?php echo esc_attr(get_option(FLIGHT_MANAGEMENT_PREFIX . 'terminal_id')); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">Base URL</th>
                    <td><input type="text" name="<?php echo FLIGHT_MANAGEMENT_PREFIX . 'base_url' ?>" value="<?php echo esc_attr(get_option(FLIGHT_MANAGEMENT_PREFIX . 'base_url')); ?>" /></td>
                </tr>
            </table>
            <?php submit_button('Save Configuration'); ?>
        </form>
    </div>
    <?php
}

add_action('admin_menu', 'create_flight_management_submenu');

// Function to save the configuration
function save_flight_configuration() {
    if (isset($_POST[FLIGHT_MANAGEMENT_PREFIX . 'user'])) {
        update_option(FLIGHT_MANAGEMENT_PREFIX . 'user', sanitize_text_field($_POST[FLIGHT_MANAGEMENT_PREFIX . 'user']));
    }
    if (isset($_POST[FLIGHT_MANAGEMENT_PREFIX . 'password'])) {
        update_option(FLIGHT_MANAGEMENT_PREFIX . 'password', sanitize_text_field($_POST[FLIGHT_MANAGEMENT_PREFIX . 'password']));
    }
    if (isset($_POST[FLIGHT_MANAGEMENT_PREFIX . 'mode'])) {
        update_option(FLIGHT_MANAGEMENT_PREFIX . 'mode', sanitize_text_field($_POST[FLIGHT_MANAGEMENT_PREFIX . 'mode']));
    }
    if (isset($_POST[FLIGHT_MANAGEMENT_PREFIX . 'agent_sine'])) {
        update_option(FLIGHT_MANAGEMENT_PREFIX . 'agent_sine', sanitize_text_field($_POST[FLIGHT_MANAGEMENT_PREFIX . 'agent_sine']));
    }
    if (isset($_POST[FLIGHT_MANAGEMENT_PREFIX . 'terminal_id'])) {
        update_option(FLIGHT_MANAGEMENT_PREFIX . 'terminal_id', sanitize_text_field($_POST[FLIGHT_MANAGEMENT_PREFIX . 'terminal_id']));
    }
    if (isset($_POST[FLIGHT_MANAGEMENT_PREFIX . 'base_url'])) {
        update_option(FLIGHT_MANAGEMENT_PREFIX . 'base_url', esc_url($_POST[FLIGHT_MANAGEMENT_PREFIX . 'base_url']));
    }
}
add_action('admin_init', 'save_flight_configuration');

// Add the shortcode to display the flight search form
function generate_flight_search_form() {
    ob_start();
    // Create the form
    ?>
    <form id="flight-search-form">
        <div class="row">
            <!-- Add fields -->
            <!-- Origin -->
            <div class="col-12 col-sm-12 col-md-3 col-lg-3">
                <label for="origin" class="form-label">Desde: </label>
                <select name="origin" placeholder="Origen" class="form-control"></select>
            </div>
            <!-- Destination -->
            <div class="col-12 col-sm-12 col-md-3 col-lg-3">
                <label for="destination" class="form-label">Destino: </label>
                <select name="destination" placeholder="Destino" class="form-control"></select>
            </div>
            <!-- Departure date -->
            <div class="col-12 col-sm-12 col-md-3 col-lg-3">
                <label for="departure_date" class="form-label">Fecha de salida: </label>
                <input type="date" name="departure_date" placeholder="Fecha de salida" class="form-control" />
            </div>
            <!-- Amount Adults and children --> 
            <div class="col-12 col-sm-12 col-md-3 col-lg-3">
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                        <label for="amount_adults" class="form-label">Adultos: </label>
                        <input type="number" name="amount_adults" placeholder="Adultos" class="form-control" value="1"/>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                        <label for="amount_children" class="form-label">Niños: </label>
                        <input type="number" name="amount_children" placeholder="Niños" class="form-control" value="0" />
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-12 text-center mt-5">
                <button type="submit" class="btn btn-primary">Buscar vuelos</button>
            </div>
        </div>
    </form>
    <?php
    return ob_get_clean();
}

// Register the shortcode
function flight_search_form_shortcode() {
    return generate_flight_search_form();
}
add_shortcode('flight_search', 'flight_search_form_shortcode');
