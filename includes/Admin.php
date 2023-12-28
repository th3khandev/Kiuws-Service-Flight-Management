<?php

namespace Kiuws_Service_Flight_Management\Includes;

use Kiuws_Service_Flight_Management\DB\FlightManagementAirportModel;
use Kiuws_Service_Flight_Management\DB\FlightManagementModel;
use Kiuws_Service_Flight_Management\DB\FlightPaymentInfoModel;
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
            __('Payment configuration', 'flight-management'),
            __('Payment configuration', 'flight-management'),
            $capability,
            $slug . '-payment-configuration',
            [$this, 'flight_management_payment_configuration_page']
        );

        add_submenu_page(
            $slug,
            __('Kiuw parameters', 'flight-management'),
            __('Kiuw parameters', 'flight-management'),
            $capability,
            $slug . '-kiuw-parameters',
            [$this, 'flight_management_kiuw_parameters_page']
        );

        add_submenu_page(
            $slug,
            __('Airports', 'flight-management'),
            __('Airports', 'flight-management'),
            $capability,
            $slug . '-airports',
            [$this, 'flight_management_airports_page']
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
            } else if ($_POST['action'] === 'add_payment') {
                $payment = new FlightPaymentInfoModel();
                $payment->flight_id = $reservation->id;
                $payment->reference = $_POST['reference'];
                $payment->date = $_POST['date'];
                $payment->type = $_POST['type'];
                $payment->card_number = '';
                $payment->card_holder_name = '';
                $payment->card_holder_document_number = '';
                $payment->card_holder_email = '';
                $payment->currency = 'USD';
                $payment->save();

                $reservation->status = FlightManagementModel::STATUS_PAID;
                $reservation->update();

                add_settings_error('flight-management-messages', 'success', 'Reservación actualizada con éxito', 'updated');
            }
        }

        if (isset($_GET['booking_id'])) {
            $id = $_GET['booking_id'];
            $flight_management = new FlightManagementModel();
            $reservation = $flight_management->getFlightByBookingId($id);
            // show reservation details
            include FLIGHT_MANAGEMENT_DIR . 'templates/admin/reservation-details.php';
        } else {
            include_once FLIGHT_MANAGEMENT_DIR . 'templates/admin/reservations.php';
        }

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
        if (isset($_POST[FLIGHT_MANAGEMENT_PREFIX . 'email_to'])) {
            update_option(FLIGHT_MANAGEMENT_PREFIX . 'email_to', sanitize_text_field($_POST[FLIGHT_MANAGEMENT_PREFIX . 'email_to']));
        }
        if (isset($_POST[FLIGHT_MANAGEMENT_PREFIX . 'fee'])) {
            update_option(FLIGHT_MANAGEMENT_PREFIX . 'fee', sanitize_text_field($_POST[FLIGHT_MANAGEMENT_PREFIX . 'fee']));
        }
        if (isset($_POST[FLIGHT_MANAGEMENT_PREFIX . 'fee_fixed'])) {
            update_option(FLIGHT_MANAGEMENT_PREFIX . 'fee_fixed', sanitize_text_field($_POST[FLIGHT_MANAGEMENT_PREFIX . 'fee_fixed']));
        } else {
            update_option(FLIGHT_MANAGEMENT_PREFIX . 'fee_fixed', sanitize_text_field(0));
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

    public function flight_management_kiuw_parameters_page () {
        // make request to https://ssl00.kiusys.com/ws3/ip_addr.php
        $url = 'https://ssl00.kiusys.com/ws3/ip_addr.php';
        $response = wp_remote_get($url);
        $body = wp_remote_retrieve_body($response);
        echo '<div class="wrap">';
        echo '<div class="postbox" style="padding: 5px 15px;">';
        echo '<h2>'. $body .'</h2>';
        echo '</div>';
        echo '</div>';
    }

    public function flight_management_airports_page () {
        $error_messages = [];
        if (isset($_POST) && !empty($_POST) && isset($_POST['submit'])) {
            var_dump($_POST);
            // validate data 
            $code = $_POST['code'];
            $name = $_POST['name'];
            $city = $_POST['city_name'];
            $state = $_POST['state_name'];
            $country = $_POST['country_name'];

            if (empty($code)) {
                $error_messages[] = 'El código es requerido';
            }

            if (empty($name)) {
                $error_messages[] = 'El nombre es requerido';
            }

            if (empty($city)) {
                $error_messages[] = 'La ciudad es requerida';
            }

            if (empty($state)) {
                $error_messages[] = 'El estado es requerido';
            }

            if (empty($country)) {
                $error_messages[] = 'El país es requerido';
            }

            if (!empty($error_messages)) {
                // Si hubo un error, agrega un mensaje de error
                foreach ($error_messages as $error_message) {
                    add_settings_error('flight-management-messages', 'error', $error_message, 'error');
                }
                include_once FLIGHT_MANAGEMENT_DIR . 'templates/admin/airport-form.php';
                return;
            } else {
                // Si todo fue exitoso, agrega un mensaje de éxito
                $airport = new FlightManagementAirportModel();
                $airport->code = $code;
                $airport->name = $name;
                $airport->city_name = $city;
                $airport->state_name = $state;
                $airport->country_name = $country;

                if ($_POST['action'] === 'create') {
                    $airport->save();
                } else {
                    $airport->id = $_POST['airport_id'];
                    $airport->update();
                }
                add_settings_error('flight-management-messages', 'success', 'Aeropuerto guardado con éxito', 'updated');
            }
        } else if (isset($_GET['action'])) {
            $action = '';
            $code = '';
            $name = '';
            $id = '';
            $city = '';
            $country = '';
            $state = '';
            $airport_id = '';
            if ($_GET['action'] === 'create') {
                $action = 'create';
            } else {
                $action = 'edit';
                $id = $_GET['airport_id'];
                $airport = new FlightManagementAirportModel();
                $airport = $airport->getAirportById($id);
                $code = $airport->code;
                $name = $airport->name;
                $city = $airport->city_name;
                $state = $airport->state_name;
                $country = $airport->country_name;
                $airport_id = $airport->id;
            }

            include_once FLIGHT_MANAGEMENT_DIR . 'templates/admin/airport-form.php';
            return;
        }

        include_once FLIGHT_MANAGEMENT_DIR . 'templates/admin/airports.php';
    }
}
