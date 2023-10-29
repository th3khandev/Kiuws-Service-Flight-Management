<?php

namespace Kiuws_Service_Flight_Management\Includes;

use Kiuws_Service_Flight_Management\DB\FlightManagementModel;
use Kiuws_Service_Flight_Management\Services\Kiuws;

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
            __('Reservations', 'flight-management'),
            __('Reservations', 'flight-management'),
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

        add_submenu_page(
            $slug,
            __('Payment onfiguration', 'flight-management'),
            __('Payment onfiguration', 'flight-management'),
            $capability,
            $slug . '-payment-configuration',
            [$this, 'flight_management_payment_configuration_page']
        );
    }

    private function cancelReservation ($reservation, $kiuws_service) {
        $result = $kiuws_service->cancelReservation($reservation->booking_id);
        if ($result['status'] == 'success') {
            add_settings_error('flight-management-messages', 'success', 'Reservación cancelada con éxito', 'updated');
            $reservation->status = FlightManagementModel::STATUS_CANCELLED;
            $reservation->update();
        } else {
            add_settings_error('flight-management-messages', 'error', $result['message'], 'error');
            add_settings_error('flight-management-messages', 'error', 'Response: ' . json_encode($result['response']), 'error');
        }
    }

    public function flight_management_page()
    {
        if (isset($_POST['action']) ) {
            // get options
            $base_url = get_option(FLIGHT_MANAGEMENT_PREFIX . 'base_url');
            $agent_sine = get_option(FLIGHT_MANAGEMENT_PREFIX . 'agent_sine');
            $terminal_id = get_option(FLIGHT_MANAGEMENT_PREFIX . 'terminal_id');
            $user = html_entity_decode(get_option(FLIGHT_MANAGEMENT_PREFIX . 'user'));
            $password = html_entity_decode(get_option(FLIGHT_MANAGEMENT_PREFIX . 'password'));
            $mode = get_option(FLIGHT_MANAGEMENT_PREFIX . 'mode');

            $kiuws_service = new Kiuws($base_url, $agent_sine, $terminal_id, $user, $password, $mode);

            $id = $_POST['booking_id'];
            $flight_management = new FlightManagementModel();
            $reservation = $flight_management->getFlightByBookingId($id);

            if ($_POST['action'] === 'cancel_reservation') {
                // Delete reservation
                $this->cancelReservation($reservation, $kiuws_service);
            } else if ($_POST['action'] === 'complete_reservation') {
                $reservation->status = FlightManagementModel::STATUS_COMPLETED;
                $reservation->update();
                add_settings_error('flight-management-messages', 'success', 'Reservación actualizada con éxito', 'updated');
            }
        }
        include_once FLIGHT_MANAGEMENT_DIR . 'templates/admin/reservations.php';
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

    public function save_flight_payment_configuration () {
        if (isset($_POST[FLIGHT_MANAGEMENT_PREFIX . 'stripe_mode'])) {
            update_option(FLIGHT_MANAGEMENT_PREFIX . 'stripe_mode', sanitize_text_field($_POST[FLIGHT_MANAGEMENT_PREFIX . 'stripe_mode']));
        }
        if (isset($_POST[FLIGHT_MANAGEMENT_PREFIX . 'stripe_testing_public_key'])) {
            update_option(FLIGHT_MANAGEMENT_PREFIX . 'stripe_testing_public_key', sanitize_text_field($_POST[FLIGHT_MANAGEMENT_PREFIX . 'stripe_testing_public_key']));
        }
        if (isset($_POST[FLIGHT_MANAGEMENT_PREFIX . 'stripe_testing_private_key'])) {
            update_option(FLIGHT_MANAGEMENT_PREFIX . 'stripe_testing_private_key', sanitize_text_field($_POST[FLIGHT_MANAGEMENT_PREFIX . 'stripe_testing_private_key']));
        }
        if (isset($_POST[FLIGHT_MANAGEMENT_PREFIX . 'stripe_production_public_key'])) {
            update_option(FLIGHT_MANAGEMENT_PREFIX . 'stripe_production_public_key', sanitize_text_field($_POST[FLIGHT_MANAGEMENT_PREFIX . 'stripe_production_public_key']));
        }
        if (isset($_POST[FLIGHT_MANAGEMENT_PREFIX . 'stripe_production_private_key'])) {
            update_option(FLIGHT_MANAGEMENT_PREFIX . 'stripe_production_private_key', sanitize_text_field($_POST[FLIGHT_MANAGEMENT_PREFIX . 'stripe_production_private_key']));
        }
    }

    public function flight_management_payment_configuration_page () {
        if (isset($_POST['submit'])) {
            // Process and save data

            if (is_wp_error($this->save_flight_payment_configuration())) {
                // Si hubo un error, agrega un mensaje de error
                $error_messages[] = 'Se produjo un error al guardar la configuración';
            } else {
                // Si todo fue exitoso, agrega un mensaje de éxito
                add_settings_error('flight-management-messages', 'success', 'Configuration saved successfully', 'updated');
            }
        }
        // Show configuration form
        include_once FLIGHT_MANAGEMENT_DIR . 'templates/admin/payment-configuration.php';
    }
}
