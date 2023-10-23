<?php

namespace Kiuws_Service_Flight_Management\Api;

use Kiuws_Service_Flight_Management\Api\Frontend\Route;

use WP_REST_Controller;

class Api extends WP_REST_Controller {
    
    public function __construct()
    {
      add_action('rest_api_init', [$this, 'register_routes']);
    }

    /**
     * Register api routes
     */
    public function register_routes() {
        $settings_route = new Route();
        $settings_route->register_routes();
    }
}