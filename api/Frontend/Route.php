<?php

namespace Kiuws_Service_Flight_Management\Api\Frontend;

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
}
