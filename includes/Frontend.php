<?php

namespace Kiuws_Service_Flight_Management\Includes;

class Frontend {
    
    public function __construct()
    {
        add_shortcode('flight_management', [$this, 'flight_management_shortcode']);
    }

    public function flight_management_shortcode()
    {
        ob_start();
        $this->loadStyles();
        include_once FLIGHT_MANAGEMENT_DIR . 'templates/frontend/flight-management.php';
        $this->load_scripts();
        return ob_get_clean();
    }

    public function load_scripts () {
        wp_enqueue_script('intlTelInput', 'https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js', [], time(), true);
        wp_enqueue_script('flight-management-manifest', FLIGHT_MANAGEMENT_URL . 'dist/js/manifest.js', [], FLIGHT_MANAGEMENT_VERSION, true);
        wp_enqueue_script('flight-management-vendor', FLIGHT_MANAGEMENT_URL . 'dist/js/vendor.js', ['flight-management-manifest'], FLIGHT_MANAGEMENT_VERSION, true);
        wp_enqueue_script('flight-management', FLIGHT_MANAGEMENT_URL . 'dist/js/flight-management.js', ['flight-management-vendor'], FLIGHT_MANAGEMENT_VERSION, true);
    }

    public function loadStyles () {
        wp_enqueue_style('intlTelInput', 'https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css', [], time());
    }
}