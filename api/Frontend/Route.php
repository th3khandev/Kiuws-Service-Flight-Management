<?php

namespace Kiuws_Service_Flight_Management\Api\Frontend;

use Kiuws_Service_Flight_Management\Services\Kiuws;
use Kiuws_Service_Flight_Management\Services\OpenFlightsOrg;
use WP_REST_Controller;
use WP_REST_Server;

class Route extends WP_REST_Controller
{

    protected $namespace;
    private $kiuwsService;

    public function __construct()
    {
        $this->namespace = 'kiuws-flight-management/v1';

        // Get configuration from options
        $base_url = get_option(FLIGHT_MANAGEMENT_PREFIX . 'base_url');
        $agent_sine = get_option(FLIGHT_MANAGEMENT_PREFIX . 'agent_sine');
        $terminal_id = get_option(FLIGHT_MANAGEMENT_PREFIX . 'terminal_id');
        $user = html_entity_decode(get_option(FLIGHT_MANAGEMENT_PREFIX . 'user'));
        $password = html_entity_decode(get_option(FLIGHT_MANAGEMENT_PREFIX . 'password'));
        $mode = get_option(FLIGHT_MANAGEMENT_PREFIX . 'mode');

        $this->kiuwsService = new Kiuws($base_url, $agent_sine, $terminal_id, $user, $password, $mode);
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

        register_rest_route(
            $this->namespace,
            'get-flight-price',
            [
                [
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => [$this, 'get_flight_price'],
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

        $response = $this->kiuwsService->getAvailabilityFlights($departure_date, $origin, $destination, $adults, $children);

        return rest_ensure_response($response);
    }

    /**
     * Get flight price
     */
    public function get_flight_price ($request) {
        $params = $request->get_params();

        $flight_segments = $params['flight_segments'];
        $flight_segments_validate = [];
        $adults = 1;
        $children = 0;

        foreach($flight_segments as $flight_segment) {
            $depurate_date_time = $flight_segment['depurateDateTime'];
            $arrival_date_time = $flight_segment['arrivalDateTime'];
            $origin = $flight_segment['origin'];
            $destination = $flight_segment['destination'];
            $adults = $flight_segment['adults'];
            $children = $flight_segment['children'];
            $flight_number = $flight_segment['flightNumber'];
            $resBookDesig = $flight_segment['resBookDesig'];
            $airlineCode = $flight_segment['airlineCode'];

            $listResBookDesig = explode(',', $resBookDesig);
            foreach ($listResBookDesig as $value) {
                $response = $this->kiuwsService->getFlightPrice($depurate_date_time, $arrival_date_time, $flight_number, trim($value), $origin, $destination, $airlineCode, $adults, $children);
                if ($response['status'] == 'success') {
                    // add booking code to response
                    $flight_segments_validate[] = [
                        'depurateDateTime' => $depurate_date_time,
                        'arrivalDateTime' => $arrival_date_time,
                        'origin' => $origin,
                        'destination' => $destination,
                        'adults' => $adults,
                        'children' => $children,
                        'flightNumber' => $flight_number,
                        'resBookDesig' => trim($value),
                        'airlineCode' => $airlineCode,
                    ];
                    break;
                }
            }
        }

        if (count($flight_segments) != count($flight_segments_validate)) {
            return rest_ensure_response([
                'status' => 'error',
                'message' => 'No se completo la cotización de todos los vuelos.'
            ]);
        }


        $response = $this->kiuwsService->getFlightPriceMultipleSegments($flight_segments_validate, $adults, $children);
        $response['flight_segments'] = $flight_segments_validate;
        return rest_ensure_response($response);
    }
}
