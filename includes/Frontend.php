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
        wp_enqueue_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js', [], time(), true);
        wp_enqueue_script('intlTelInput', 'https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js', ['bootstrap'], time(), true);
        wp_enqueue_script('flight-management-manifest', FLIGHT_MANAGEMENT_URL . 'dist/js/manifest.js', ['intlTelInput'], FLIGHT_MANAGEMENT_VERSION, true);
        wp_enqueue_script('flight-management-vendor', FLIGHT_MANAGEMENT_URL . 'dist/js/vendor.js', ['flight-management-manifest'], FLIGHT_MANAGEMENT_VERSION, true);
        wp_enqueue_script('flight-management', FLIGHT_MANAGEMENT_URL . 'dist/js/flight-management.js', ['flight-management-vendor'], FLIGHT_MANAGEMENT_VERSION, true);
        wp_enqueue_script('stripe', 'https://js.stripe.com/v3/', [], time());

        // get stripe public key
        $stripe_mode = get_option(FLIGHT_MANAGEMENT_PREFIX . 'stripe_mode');
        $stripe_public_key = get_option(FLIGHT_MANAGEMENT_PREFIX . 'stripe_' . $stripe_mode . '_public_key');

        // send stripe public key to frontend
        wp_localize_script('flight-management', 'FLIGHT_MANAGEMENT', [
            'STRIPE_PUBLIC_KEY' => $stripe_public_key,
        ]);
    }

    public function loadStyles () {
        wp_enqueue_style('intlTelInput', 'https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css', [], time());
    }
}