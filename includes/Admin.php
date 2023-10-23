<?php

namespace Kiuws_Service_Flight_Management\Includes;

use WP_Error;

class Admin
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'admin_menu']);
    }

    public function admin_menu()
    {
        $capability = 'manage_options';
        $slug = 'flight-management';

        add_menu_page(
            __('Flight Management', 'flight-management'),
            __('Flight Management', 'flight-management'),
            $capability,
            $slug,
            [$this, 'flight_management_page'],
            'dashicons-admin-site',
            20
        );

        if (!current_user_can($capability)) {
            wp_die('Permission denied');
        }

        add_submenu_page(
            $slug,
            __('Configuration', 'flight-management'),
            __('Configuration', 'flight-management'),
            $capability,
            $slug . '-configuration',
            [$this, 'flight_management_configuration_page']
        );
    }

    public function flight_management_page()
    {
        echo '<div class="wrap">';
        echo '<h2>Kiuws Flight Management</h2>';
        echo '</div>';
    }

    public function flight_management_configuration_page()
    {
        if (isset($_POST['submit'])) {
            // Process and save data

            if (is_wp_error($this->save_flight_configuration())) {
                // Si hubo un error, agrega un mensaje de error
                $error_messages[] = 'Se produjo un error al guardar la configuración';
            } else {
                // Si todo fue exitoso, agrega un mensaje de éxito
                add_settings_error('flight-management-messages', 'success', 'Configuration saved successfully', 'updated');
            }
        }
        // Show configuration form
        include_once FLIGHT_MANAGEMENT_DIR . 'templates/admin/configuration.php';
    }

    public function save_flight_configuration()
    {
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
}
