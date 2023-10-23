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
        global $submenu;
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
            /* $submenu[$slug][] = [
                __('Flights', 'flight-management'),
                $capability,
                'admin.php?page=flight-management&tab=flights'
            ];
            $submenu[$slug][] = [
                __('Settings', 'flight-management'),
                $capability,
                'admin.php?page=flight-management&tab=settings'
            ]; */
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
            $result = $this->save_flight_configuration();

            if (is_wp_error($result)) {
                // Si hubo un error, agrega un mensaje de error
                $error_messages[] = $result ? $result->get_error_message() : '';
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
        $error = null;
        if (isset($_POST[FLIGHT_MANAGEMENT_PREFIX . 'user'])) {
            if (!update_option(FLIGHT_MANAGEMENT_PREFIX . 'user', sanitize_text_field($_POST[FLIGHT_MANAGEMENT_PREFIX . 'user']))) {
                $error = new WP_Error('error_saving_user', 'Error saving user');
            }
        }
        if (isset($_POST[FLIGHT_MANAGEMENT_PREFIX . 'password'])) {
            if (!update_option(FLIGHT_MANAGEMENT_PREFIX . 'password', sanitize_text_field($_POST[FLIGHT_MANAGEMENT_PREFIX . 'password']))) {
                $error = new WP_Error('error_saving_password', 'Error saving password');
            }
        }
        if (isset($_POST[FLIGHT_MANAGEMENT_PREFIX . 'mode'])) {
            if (!update_option(FLIGHT_MANAGEMENT_PREFIX . 'mode', sanitize_text_field($_POST[FLIGHT_MANAGEMENT_PREFIX . 'mode']))) {
                $error = new WP_Error('error_saving_mode', 'Error saving mode');
            }
        }
        if (isset($_POST[FLIGHT_MANAGEMENT_PREFIX . 'agent_sine'])) {
            if (!update_option(FLIGHT_MANAGEMENT_PREFIX . 'agent_sine', sanitize_text_field($_POST[FLIGHT_MANAGEMENT_PREFIX . 'agent_sine']))) {
                $error = new WP_Error('error_saving_agent_sine', 'Error saving agent sine');
            }
        }
        if (isset($_POST[FLIGHT_MANAGEMENT_PREFIX . 'terminal_id'])) {
            if (!update_option(FLIGHT_MANAGEMENT_PREFIX . 'terminal_id', sanitize_text_field($_POST[FLIGHT_MANAGEMENT_PREFIX . 'terminal_id']))) {
                $error = new WP_Error('error_saving_terminal_id', 'Error saving terminal id');
            }
        }
        if (isset($_POST[FLIGHT_MANAGEMENT_PREFIX . 'base_url'])) {
            if (!update_option(FLIGHT_MANAGEMENT_PREFIX . 'base_url', esc_url($_POST[FLIGHT_MANAGEMENT_PREFIX . 'base_url']))) {
                $error = new WP_Error('error_saving_base_url', 'Error saving base url');
            }
        }
        // return wp_erorr 
        return $error;
    }
}
