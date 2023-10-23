<?php

namespace Kiuws_Service_Flight_Management\Api\Frontend;

use Kiuws_Service_Flight_Management\Services\Kiuws;
use Kiuws_Service_Flight_Management\Services\OpenFlightsOrg;
use WP_REST_Controller;
use WP_REST_Server;

class Route extends WP_REST_Controller
{

    protected $namespace;

    public function __construct()
    {
        $this->namespace = 'kiuws-flight-management/v1';
    }

    /**
     * Register routes
     */
    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            'get-airport-codes',
            [
                [
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => [$this, 'get_airport_codes'],
                    'permission_callback' => [$this, 'get_route_access'],
                ]
            ]
        );

        register_rest_route(
            $this->namespace,
            'get-flight-available',
            [
                [
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => [$this, 'get_flight_available'],
                    'permission_callback' => [$this, 'get_route_access'],
                ]
            ]
        );
    }

    /**
     * Get route access
     */
    public function get_route_access($request)
    {
        return true;
    }

    /**
     * Get airport codes
     */
    public function get_airport_codes($request)
    {
        $params = $request->get_params();
        $search = $params['search'] ?? '';

        $open_flight_org = new OpenFlightsOrg();
        $response = $open_flight_org->getAirports($search);

        return rest_ensure_response($response);
    }

    /**
     * Get flight available
     */
    public function get_flight_available($request) {
        $params = $request->get_params();
        $origin = $params['origin'];
        $destination = $params['destination'];
        $departure_date = $params['depurate_date'];
        $adults = $params['adults'];
        $children = $params['children'];

        // Get configuration from options
        $base_url = get_option(FLIGHT_MANAGEMENT_PREFIX . 'base_url');
        $agent_sine = get_option(FLIGHT_MANAGEMENT_PREFIX . 'agent_sine');
        $terminal_id = get_option(FLIGHT_MANAGEMENT_PREFIX . 'terminal_id');
        $user = html_entity_decode(get_option(FLIGHT_MANAGEMENT_PREFIX . 'user'));
        $password = html_entity_decode(get_option(FLIGHT_MANAGEMENT_PREFIX . 'password'));
        $mode = get_option(FLIGHT_MANAGEMENT_PREFIX . 'mode');

        $kiuws = new Kiuws($base_url, $agent_sine, $terminal_id, $user, $password, $mode);
        $response = $kiuws->getAvailabilityFlights($departure_date, $origin, $destination, $adults, $children);

        return rest_ensure_response($response);
    }
}
